<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\FreelancerCredits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:255'],
            'role' => ['required', 'in:customer,client,business,freelancer'],
        ]);

        $role = $validated['role'] === 'client' ? 'customer' : $validated['role'];

        $userPayload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ];

        if (Schema::hasColumn('users', 'account_type')) {
            $userPayload['account_type'] = $role === 'customer' ? 'client' : $role;
        }

        if (Schema::hasColumn('users', 'profile_type')) {
            $userPayload['profile_type'] = in_array($role, ['business', 'freelancer'], true) ? $role : null;
        }

        $user = User::create($userPayload);

        Auth::login($user);

        if (Schema::hasColumn('users', 'last_active_at')) {
            $user->forceFill(['last_active_at' => now()])->save();
        }

        if ($user->isBusiness()) {
            $user->initializeTrialIfMissing();
        }

        if ($user->isFreelancer()) {
            FreelancerCredits::ensureMonthlyCredits($user);
        }

        return $this->redirectAfterRegistration($user);
    }

    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $login = trim((string) $validated['email']);
        $email = strcasecmp($login, 'Admin04') === 0 ? 'admin04@bon.bg' : $login;

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && Auth::attempt([
            'email' => $email,
            'password' => $validated['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();

            if ($user && Schema::hasColumn('users', 'is_suspended') && $user->is_suspended) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Този профил е спрян. Свържете се с администратор.',
                ])->onlyInput('email');
            }

            if ($user && Schema::hasColumn('users', 'last_active_at')) {
                $user->forceFill(['last_active_at' => now()])->save();
            }

            if ($user && ($user->role === 'admin' || $user->accountType() === 'admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user && $user->isFreelancer()) {
                FreelancerCredits::ensureMonthlyCredits($user);
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Невалиден имейл или парола.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectAfterRegistration(User $user)
    {
        if ($user->role === 'admin' || $user->accountType() === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isBusiness()) {
            return redirect()->route('dashboard.business.profile.edit');
        }

        if ($user->isFreelancer()) {
            return redirect()->route('dashboard.freelancer.profile.edit');
        }

        return redirect()->route('dashboard.client.profile.edit');
    }
}

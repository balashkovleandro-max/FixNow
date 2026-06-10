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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ]);

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

        return redirect()->route('bon.onboarding');
    }

    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = $request->user();

            if ($user && Schema::hasColumn('users', 'last_active_at')) {
                $user->forceFill(['last_active_at' => now()])->save();
            }

            if ($user && $user->role === 'admin') {
                return redirect('/dashboard');
            }

            if ($user && $user->isFreelancer()) {
                FreelancerCredits::ensureMonthlyCredits($user);
            }

            return redirect()->route('bon.onboarding');
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
}

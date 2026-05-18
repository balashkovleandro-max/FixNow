<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class AdminBusinessController extends Controller
{
    public function activate(User $user): RedirectResponse
    {
        $this->authorizeAdminBusinessAction($user);

        $updates = [
            'subscription_status' => 'active',
            'subscription_started_at' => now(),
            'subscription_ends_at' => now()->addDays(30),
            'cancelled_at' => null,
        ];

        if (Schema::hasColumn('users', 'offer_points_balance')) {
            $updates['offer_points_balance'] = $user->planKey() === 'premium' ? 90 : 30;
            if (Schema::hasColumn('users', 'offer_points_initialized_at')) {
                $updates['offer_points_initialized_at'] = now();
            }
        }

        $user->forceFill($updates)->save();

        return back()->with('success', 'Изпълнителят е активиран за 30 дни.');
    }

    public function extendTrial(User $user): RedirectResponse
    {
        $this->authorizeAdminBusinessAction($user);

        $baseTrialEnd = $user->trial_ends_at && $user->trial_ends_at->greaterThan(now())
            ? $user->trial_ends_at
            : now();

        $updates = [
            'subscription_status' => 'trial',
            'trial_started_at' => $user->trial_started_at ?: now(),
            'trial_ends_at' => $baseTrialEnd->copy()->addDays(7),
            'cancelled_at' => null,
        ];

        if (Schema::hasColumn('users', 'offer_points_balance') && $user->offer_points_balance === null) {
            $updates['offer_points_balance'] = 45;
            if (Schema::hasColumn('users', 'offer_points_initialized_at')) {
                $updates['offer_points_initialized_at'] = now();
            }
        }

        $user->forceFill($updates)->save();

        return back()->with('success', 'Trial периодът е удължен със 7 дни.');
    }

    public function expire(User $user): RedirectResponse
    {
        $this->authorizeAdminBusinessAction($user);

        $user->forceFill([
            'subscription_status' => 'expired',
            'subscription_ends_at' => now(),
            'cancelled_at' => null,
        ])->save();

        return back()->with('success', 'Изпълнителят е маркиран като expired.');
    }

    public function cancel(User $user): RedirectResponse
    {
        $this->authorizeAdminBusinessAction($user);

        $user->forceFill([
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ])->save();

        return back()->with('success', 'Абонаментът е отменен.');
    }

    public function verify(User $user): RedirectResponse
    {
        $this->authorizeAdminBusinessAction($user);

        $user->forceFill([
            'is_verified' => true,
            'verified_at' => now(),
        ])->save();

        return back()->with('success', 'Изпълнителят е маркиран като verified.');
    }

    public function unverify(User $user): RedirectResponse
    {
        $this->authorizeAdminBusinessAction($user);

        $user->forceFill([
            'is_verified' => false,
            'verified_at' => null,
        ])->save();

        return back()->with('success', 'Verified статусът е премахнат.');
    }

    private function authorizeAdminBusinessAction(User $user): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
        abort_unless($user->isBusiness(), 404);
    }
}

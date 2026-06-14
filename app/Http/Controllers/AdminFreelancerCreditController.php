<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\FreelancerCreditTransaction;
use App\Models\FreelancerJobApplication;
use App\Models\User;
use App\Support\FreelancerCredits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AdminFreelancerCreditController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        abort_unless($admin && $admin->role === 'admin', 403);

        $freelancers = User::query()
            ->where('role', 'freelancer')
            ->withCount('freelancerJobApplications')
            ->latest()
            ->get();

        $transactions = FreelancerCreditTransaction::query()
            ->with(['user', 'admin', 'job', 'application'])
            ->latest()
            ->take(80)
            ->get();

        $purchasedPackages = FreelancerCreditTransaction::query()
            ->where('type', FreelancerCreditTransaction::TYPE_PURCHASE)
            ->latest()
            ->take(30)
            ->get();

        $applications = FreelancerJobApplication::query()
            ->with(['freelancer', 'job.business'])
            ->latest()
            ->take(50)
            ->get();

        return view('admin.freelancer-credits', compact('freelancers', 'transactions', 'purchasedPackages', 'applications'));
    }

    public function adjust(Request $request, User $user)
    {
        $admin = $request->user();

        abort_unless($admin && $admin->role === 'admin', 403);
        abort_unless($user->isFreelancer(), 404);

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:-10000', 'max:10000', 'not_in:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $transaction = FreelancerCredits::addCredits(
                $user,
                (int) $validated['amount'],
                FreelancerCreditTransaction::TYPE_ADMIN_ADJUSTMENT,
                $validated['description'] ?: 'Admin credit adjustment',
                $admin
            );

            if (Schema::hasTable('admin_activity_logs')) {
                AdminActivityLog::query()->create([
                    'admin_user_id' => $admin->id,
                    'action' => 'freelancer.credits.adjusted',
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'old_values' => null,
                    'new_values' => [
                        'amount' => (int) $validated['amount'],
                        'balance_after' => $transaction->balance_after,
                        'transaction_id' => $transaction->id,
                    ],
                    'metadata' => [
                        'description' => $validated['description'] ?: 'Admin credit adjustment',
                    ],
                ]);
            }
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()
            ->route('admin.freelancer-credits.index')
            ->with('success', 'Кредитният баланс е обновен.');
    }
}

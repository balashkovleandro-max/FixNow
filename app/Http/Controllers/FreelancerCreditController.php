<?php

namespace App\Http\Controllers;

use App\Models\FreelancerCreditTransaction;
use App\Support\FreelancerCredits;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FreelancerCreditController extends Controller
{
    public function index(Request $request)
    {
        $freelancer = $request->user();

        abort_unless($freelancer && $freelancer->isFreelancer(), 403);

        FreelancerCredits::ensureMonthlyCredits($freelancer);

        $creditStats = FreelancerCredits::stats($freelancer);

        $transactions = $freelancer->freelancerCreditTransactions()
            ->with(['job', 'application'])
            ->latest()
            ->paginate(20);

        $applications = $freelancer->freelancerJobApplications()
            ->with('job.business')
            ->latest()
            ->take(12)
            ->get();

        $packages = FreelancerCredits::PACKAGES;

        return view('freelancer.credits.index', compact('freelancer', 'creditStats', 'transactions', 'applications', 'packages'));
    }

    public function purchase(Request $request)
    {
        $freelancer = $request->user();

        abort_unless($freelancer && $freelancer->isFreelancer(), 403);

        $validated = $request->validate([
            'package' => ['required', Rule::in(array_keys(FreelancerCredits::PACKAGES))],
        ]);

        FreelancerCredits::ensureMonthlyCredits($freelancer);

        $packageKey = $validated['package'];
        $package = FreelancerCredits::PACKAGES[$packageKey];

        FreelancerCredits::addCredits(
            $freelancer,
            $package['credits'],
            FreelancerCreditTransaction::TYPE_PURCHASE,
            'Пакет кредити: ' . $package['label'],
            null,
            [
                'credit_package' => $packageKey,
                'price_amount' => $package['price'],
                'currency' => 'EUR',
            ]
        );

        return redirect()
            ->route('freelancer.credits.index')
            ->with('success', 'Пакетът е добавен към баланса ти. Нов баланс: ' . $freelancer->freelancerCreditsBalance() . ' кредита.');
    }
}

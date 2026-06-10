<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FreelancerJobApplication;
use App\Support\ProfileTrust;
use Illuminate\Support\Facades\Schema;

class FreelancerProfileController extends Controller
{
    public function show(User $user)
    {
        abort_unless($user->isFreelancer(), 404);

        $user->loadMissing('freelancerPortfolioItems');
        $trustSummary = ProfileTrust::summary($user);
        $portfolioItems = $user->freelancerPortfolioItems;

        $applications = Schema::hasTable('freelancer_job_applications')
            ? $user->freelancerJobApplications()
                ->with('job.business')
                ->whereIn('status', [
                    FreelancerJobApplication::STATUS_ACCEPTED,
                    FreelancerJobApplication::STATUS_COMPLETED,
                    FreelancerJobApplication::STATUS_DONE,
                ])
                ->latest()
                ->take(8)
                ->get()
            : collect();

        return view('freelancer.profile.show', compact('user', 'trustSummary', 'applications', 'portfolioItems'));
    }
}

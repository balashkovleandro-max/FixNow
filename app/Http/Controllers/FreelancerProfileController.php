<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FreelancerJobApplication;
use App\Support\CategoryCatalog;
use App\Support\ProfileTrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FreelancerProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        abort_unless($user && $user->isFreelancer(), 403);

        $user->loadMissing(['services', 'freelancerPortfolioItems']);

        return view('freelancer.profile.edit', [
            'user' => $user,
            'categories' => $this->freelancerCategories(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        abort_unless($user && $user->isFreelancer(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'headline' => ['nullable', 'string', 'max:160'],
            'short_description' => ['nullable', 'string', 'max:240'],
            'description' => ['nullable', 'string', 'max:3000'],
            'phone' => ['nullable', 'string', 'max:80'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'website' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'github' => ['nullable', 'string', 'max:255'],
            'behance' => ['nullable', 'string', 'max:255'],
            'years_experience' => ['nullable', 'string', 'max:80'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'project_rate' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'availability' => ['nullable', 'string', 'max:80'],
            'work_mode' => ['nullable', 'string', 'max:80'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['nullable', 'string', 'max:80'],
            'response_time_label' => ['nullable', 'string', 'max:120'],
            'booking_enabled' => ['nullable', 'boolean'],
            'service_categories' => ['nullable', 'array'],
            'service_categories.*' => ['nullable', 'string', 'max:120'],
            'service_cities' => ['nullable', 'array'],
            'service_cities.*' => ['nullable', 'string', 'max:120'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'business_category' => $validated['headline'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'avatar' => $validated['avatar'] ?? null,
            'city' => $validated['city'] ?? null,
            'website' => $validated['website'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'github' => $validated['github'] ?? null,
            'behance' => $validated['behance'] ?? null,
            'years_experience' => $validated['years_experience'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'project_rate' => $validated['project_rate'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'work_mode' => $validated['work_mode'] ?? null,
            'languages' => collect($validated['languages'] ?? [])->filter()->values()->all(),
            'response_time_label' => $validated['response_time_label'] ?? null,
            'booking_enabled' => $request->boolean('booking_enabled'),
            'service_categories' => collect($validated['service_categories'] ?? [])->filter()->values()->all(),
            'service_cities' => collect($validated['service_cities'] ?? [])->filter()->values()->all(),
        ];

        $payload = collect($payload)
            ->filter(fn ($value, $column) => Schema::hasColumn('users', $column))
            ->all();

        $user->forceFill($payload)->save();

        return redirect()
            ->route('freelancers.show', $user)
            ->with('success', 'Фрийлансър профилът е обновен.');
    }

    public function show(User $user)
    {
        abort_unless($user->isFreelancer(), 404);

        $user->loadMissing(['freelancerPortfolioItems', 'services']);
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

    private function freelancerCategories(): array
    {
        return CategoryCatalog::names()->all();
    }
}

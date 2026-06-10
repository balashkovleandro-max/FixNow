<?php

namespace App\Http\Controllers;

use App\Models\FreelancerJob;
use App\Models\User;
use App\Support\BusinessGrowthMetrics;
use App\Support\ProfileTrust;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class BonHomeController extends Controller
{
    public function index(Request $request)
    {
        $smartCategory = trim((string) $request->query('category', ''));

        $smartCategories = [
            'Web Design',
            'Development',
            'Marketing',
            'Ремонти',
            'Почистване',
            'Красота',
            'Ресторанти',
            'Хотели',
        ];

        $recommendedSpecialists = $this->recommendedSpecialists($smartCategory)->take(8);
        $smartJobs = $this->smartJobs($smartCategory);

        return view('bon.index', compact('recommendedSpecialists', 'smartCategory', 'smartCategories', 'smartJobs'));
    }

    private function recommendedSpecialists(?string $category = null): Collection
    {
        if (!Schema::hasTable('users')) {
            return collect();
        }

        $businesses = BusinessGrowthMetrics::publicBusinesses()
            ->each(fn (User $profile) => $profile->setAttribute('specialist_type', 'business'));

        $freelancerQuery = User::query()->where('role', 'freelancer');

        if (Schema::hasTable('freelancer_portfolio_items')) {
            $freelancerQuery->with(['freelancerPortfolioItems' => fn ($query) => $query->latest()->take(3)]);
        }

        $freelancers = $freelancerQuery
            ->latest()
            ->take(60)
            ->get()
            ->each(fn (User $profile) => $profile->setAttribute('specialist_type', 'freelancer'));

        $profiles = ProfileTrust::attach($businesses->merge($freelancers));

        if (filled($category)) {
            $profiles = $profiles->filter(fn (User $profile) => $this->matchesProfileCategory($profile, $category))->values();
        }

        return ProfileTrust::ranked($profiles)->values();
    }

    private function smartJobs(?string $category = null): Collection
    {
        if (!Schema::hasTable('freelancer_jobs')) {
            return collect();
        }

        return FreelancerJob::query()
            ->open()
            ->with('business')
            ->when($category, function ($query) use ($category) {
                $term = '%' . $category . '%';

                $query->where(function ($query) use ($term) {
                    $query
                        ->where('category', 'like', $term)
                        ->orWhere('title', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->latest()
            ->take(4)
            ->get();
    }

    private function matchesProfileCategory(User $profile, string $category): bool
    {
        $needle = BusinessGrowthMetrics::normalizeSearchText($category);

        if ($needle === '') {
            return true;
        }

        $haystack = collect([
            $profile->name,
            $profile->business_name,
            $profile->business_category,
            $profile->short_description,
            $profile->description,
        ])
            ->merge(method_exists($profile, 'serviceCategories') ? $profile->serviceCategories() : [])
            ->merge($profile->relationLoaded('freelancerPortfolioItems') ? $profile->freelancerPortfolioItems->pluck('title') : [])
            ->map(fn ($value) => BusinessGrowthMetrics::normalizeSearchText((string) $value))
            ->filter()
            ->implode(' ');

        return str_contains($haystack, $needle);
    }
}

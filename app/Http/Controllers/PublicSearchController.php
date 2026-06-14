<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CategoryCatalog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicSearchController extends Controller
{
    public function index(Request $request)
    {
        $profiles = $this->profiles()
            ->filter(fn (User $profile) => $this->matches($profile, $request))
            ->sortBy([
                fn (User $profile) => $profile->isPremium() ? 0 : 1,
                fn (User $profile) => -1 * (int) data_get($profile->trustSummary(), 'trust_score', 0),
                fn (User $profile) => -1 * (float) ($profile->averageRating() ?? 0),
                fn (User $profile) => -1 * (int) $profile->approvedReviewsCount(),
            ])
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 18;

        $results = new LengthAwarePaginator(
            $profiles->forPage($page, $perPage)->values(),
            $profiles->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $categories = CategoryCatalog::names();

        return view('search.index', compact('results', 'categories'));
    }

    private function profiles(): Collection
    {
        if (!Schema::hasTable('users')) {
            return collect();
        }

        $businesses = User::query()
            ->publiclyVisible()
            ->with(['services', 'businessPhotos', 'reviews'])
            ->publicRanked()
            ->limit(120)
            ->get();

        $freelancers = User::query()
            ->where(function ($query) {
                $query->where('role', 'freelancer');

                if (Schema::hasColumn('users', 'account_type')) {
                    $query->orWhere('account_type', 'freelancer');
                }

                if (Schema::hasColumn('users', 'profile_type')) {
                    $query->orWhere('profile_type', 'freelancer');
                }
            })
            ->when(Schema::hasColumn('users', 'is_suspended'), function ($query) {
                $query->where(function ($query) {
                    $query->where('is_suspended', false)->orWhereNull('is_suspended');
                });
            })
            ->with(['services', 'freelancerPortfolioItems', 'reviews'])
            ->latest()
            ->limit(120)
            ->get();

        return $businesses->merge($freelancers)->unique('id')->values();
    }

    private function matches(User $profile, Request $request): bool
    {
        return $this->contains($this->profileSearchText($profile), $request->string('q')->toString())
            && $this->contains($this->citySearchText($profile), $request->string('city')->toString())
            && $this->contains($this->categorySearchText($profile), $request->string('category')->toString())
            && $this->contains($this->profileSearchText($profile), $request->string('service')->toString());
    }

    private function profileSearchText(User $profile): string
    {
        $services = $profile->relationLoaded('services')
            ? $profile->services
            : collect();

        return collect([
            $profile->name,
            $profile->business_name,
            $profile->business_category,
            $profile->city,
            $profile->service_areas,
            $profile->short_description,
            $profile->description,
            implode(' ', $profile->serviceCategories()),
            implode(' ', $profile->serviceCities()),
            $services->pluck('title')->implode(' '),
            $services->pluck('category')->implode(' '),
            $services->pluck('description')->implode(' '),
            $services->pluck('city')->implode(' '),
        ])->filter()->implode(' ');
    }

    private function citySearchText(User $profile): string
    {
        return collect([
            $profile->city,
            $profile->service_areas,
            implode(' ', $profile->serviceCities()),
            $profile->relationLoaded('services') ? $profile->services->pluck('city')->implode(' ') : null,
        ])->filter()->implode(' ');
    }

    private function categorySearchText(User $profile): string
    {
        $profileCategories = collect($profile->serviceCategories())
            ->flatMap(fn ($category) => [$category, CategoryCatalog::displayName($category)]);

        $serviceCategories = $profile->relationLoaded('services')
            ? $profile->services->pluck('category')->flatMap(fn ($category) => [$category, CategoryCatalog::displayName($category)])
            : collect();

        return collect([
            $profile->business_category,
            $profile->business_category ? CategoryCatalog::displayName($profile->business_category) : null,
            $profileCategories->implode(' '),
            $serviceCategories->implode(' '),
        ])->filter()->implode(' ');
    }

    private function contains(string $haystack, ?string $needle): bool
    {
        $needle = trim((string) $needle);

        if ($needle === '') {
            return true;
        }

        return Str::of($haystack)->lower()->contains(Str::of($needle)->lower()->toString());
    }
}

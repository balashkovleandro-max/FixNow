<?php

namespace App\Support;

use App\Models\BusinessAnalyticsEvent;
use App\Models\BusinessRecommendation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class BusinessGrowthMetrics
{
    public static function publicBusinesses(): Collection
    {
        $relations = ['services'];

        if (Schema::hasTable('business_photos')) {
            $relations[] = 'businessPhotos';
        }

        $businesses = User::publiclyVisible()
            ->publicRanked()
            ->with($relations)
            ->latest()
            ->get();

        return self::ranked(self::attach($businesses))
            ->reject(fn (User $business) => CategoryCatalog::businessHasHiddenCategory($business))
            ->values();
    }

    public static function attach(Collection $businesses): Collection
    {
        $ids = $businesses->pluck('id')->filter()->values();

        if ($ids->isEmpty()) {
            return $businesses;
        }

        $reviewStats = collect();
        $recommendationCounts = collect();
        $profileViewCounts = collect();
        $clickCounts = collect();

        if (Schema::hasTable('reviews')) {
            $reviewStats = Review::query()
                ->selectRaw('business_id, COUNT(*) as reviews_count, AVG(rating) as average_rating')
                ->whereIn('business_id', $ids)
                ->where('status', Review::STATUS_APPROVED)
                ->groupBy('business_id')
                ->get()
                ->keyBy('business_id');
        }

        if (Schema::hasTable('business_recommendations')) {
            $recommendationCounts = BusinessRecommendation::query()
                ->selectRaw('business_id, COUNT(*) as recommendations_count')
                ->whereIn('business_id', $ids)
                ->groupBy('business_id')
                ->pluck('recommendations_count', 'business_id');
        }

        if (Schema::hasTable('business_analytics_events')) {
            $profileViewCounts = BusinessAnalyticsEvent::query()
                ->selectRaw('business_id, COUNT(*) as views_count')
                ->whereIn('business_id', $ids)
                ->where('event_type', BusinessAnalyticsEvent::PROFILE_VIEW)
                ->groupBy('business_id')
                ->pluck('views_count', 'business_id');

            $clickCounts = BusinessAnalyticsEvent::query()
                ->selectRaw('business_id, COUNT(*) as clicks_count')
                ->whereIn('business_id', $ids)
                ->whereIn('event_type', BusinessAnalyticsEvent::clickEventTypes())
                ->groupBy('business_id')
                ->pluck('clicks_count', 'business_id');
        }

        return ProfileTrust::attach($businesses)->map(function (User $business) use ($reviewStats, $recommendationCounts, $profileViewCounts, $clickCounts) {
            $reviews = $reviewStats->get($business->id);

            $business->setAttribute('growth_average_rating', $reviews ? round((float) $reviews->average_rating, 1) : null);
            $business->setAttribute('growth_reviews_count', $reviews ? (int) $reviews->reviews_count : 0);
            $business->setAttribute('growth_recommendations_count', (int) ($recommendationCounts[$business->id] ?? 0));
            $business->setAttribute('growth_profile_views_count', (int) ($profileViewCounts[$business->id] ?? 0));
            $business->setAttribute('growth_clicks_count', (int) ($clickCounts[$business->id] ?? 0));
            $trustSummary = $business->trust_summary ?? ProfileTrust::summary($business);

            $business->setAttribute('trust_summary', $trustSummary);
            $business->setAttribute('trust_score', $trustSummary['trust_score'] ?? 0);
            $business->setAttribute('trust_badges', $trustSummary['badges'] ?? []);
            $business->setAttribute('growth_score', self::score($business));

            return $business;
        });
    }

    public static function ranked(Collection $businesses): Collection
    {
        return $businesses
            ->sort(function (User $first, User $second) {
                $scoreComparison = ($second->growth_score ?? self::score($second)) <=> ($first->growth_score ?? self::score($first));

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                return ($second->created_at?->timestamp ?? 0) <=> ($first->created_at?->timestamp ?? 0);
            })
            ->values();
    }

    public static function score(User $business): float
    {
        $trustSummary = $business->trust_summary ?? ProfileTrust::summary($business);
        $lastActivityScore = (int) floor(($trustSummary['last_active_at']?->timestamp ?? 0) / 86400);

        return ($business->isPremium() ? 100000000 : 0)
            + ((int) ($trustSummary['trust_score'] ?? 0) * 100000)
            + ((float) ($trustSummary['average_rating'] ?? $business->growth_average_rating ?? 0) * 10000)
            + ((int) ($trustSummary['completed_projects_count'] ?? 0) * 500)
            + $lastActivityScore
            + ((int) ($business->growth_recommendations_count ?? 0) * 85)
            + ((int) ($business->growth_clicks_count ?? 0) * 8)
            + ((int) ($business->growth_profile_views_count ?? 0) * 3);
    }

    public static function filterByCity(Collection $businesses, ?string $city): Collection
    {
        $city = self::normalizeSearchText((string) $city);

        if ($city === '') {
            return $businesses;
        }

        return $businesses
            ->filter(fn (User $business) => self::matchesCity($business, $city))
            ->values();
    }

    public static function filterByCategory(Collection $businesses, ?string $category): Collection
    {
        $category = self::normalizeSearchText((string) $category);

        if ($category === '') {
            return $businesses;
        }

        if (CategoryCatalog::isHiddenCategory($category)) {
            return collect();
        }

        return $businesses
            ->filter(fn (User $business) => collect($business->serviceCategories())
                ->contains(fn ($serviceCategory) => self::matchesCategory((string) $serviceCategory, $category)))
            ->values();
    }

    public static function popularCategories(Collection $businesses, int $limit = 8): Collection
    {
        return $businesses
            ->flatMap(fn (User $business) => $business->serviceCategories())
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->reject(fn ($category) => CategoryCatalog::isHiddenCategory((string) $category))
            ->countBy()
            ->sortDesc()
            ->take($limit)
            ->map(fn ($count, $category) => [
                'name' => $category,
                'count' => $count,
            ])
            ->values();
    }

    public static function matchesCity(User $business, ?string $city, ?string $serviceCity = null): bool
    {
        $needle = self::normalizeSearchText((string) $city);

        if ($needle === '') {
            return true;
        }

        $cities = collect($business->serviceCities())
            ->push($business->city)
            ->push($serviceCity)
            ->map(fn ($value) => self::normalizeSearchText((string) $value))
            ->filter()
            ->unique()
            ->values();

        return $cities->contains($needle);
    }

    public static function matchesCategory(string $candidate, string $search): bool
    {
        $candidate = self::normalizeCategory($candidate);
        $search = self::normalizeCategory($search);

        if ($candidate === '' || $search === '') {
            return false;
        }

        return $candidate === $search
            || str_contains($candidate, $search)
            || str_contains($search, $candidate);
    }

    public static function normalizeSearchText(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        return mb_strtolower($value);
    }

    private static function normalizeCategory(string $value): string
    {
        $value = self::normalizeSearchText($value);

        $aliases = [
            'автосервизи' => 'автосервиз',
            'автосервиз' => 'автосервиз',
            'auto service' => 'автосервиз',
            'autoservice' => 'автосервиз',
            'електро услуги' => 'електроуслуги',
            'електроуслуги' => 'електроуслуги',
            'електро услуга' => 'електроуслуги',
            'електротехник' => 'електроуслуги',
            'electric' => 'електроуслуги',
            'вик' => 'вик услуги',
            'вик услуга' => 'вик услуги',
            'вик услуги' => 'вик услуги',
            'vik' => 'вик услуги',
            'plumbing' => 'вик услуги',
            'ремонт' => 'ремонти',
            'ремонти' => 'ремонти',
            'ремонти и строителство' => 'ремонти',
            'ремонт на апартаменти' => 'ремонти',
            'ремонт на бани' => 'ремонти',
            'repair specialists' => 'ремонти',
            'remonti' => 'ремонти',
            'repairs' => 'ремонти',
            'салон' => 'салони за красота',
            'салон за красота' => 'салони за красота',
            'салони за красота' => 'салони за красота',
            'красота и услуги' => 'салони за красота',
            'почистване' => 'почистване',
            'почистващи услуги' => 'почистване',
            'cleaning' => 'почистване',
            'хамали' => 'хамали и транспорт',
            'транспорт' => 'хамали и транспорт',
        ];

        return $aliases[$value] ?? $value;
    }
}

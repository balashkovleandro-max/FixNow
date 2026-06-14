<?php

namespace App\Http\Controllers;

use App\Support\BusinessGrowthMetrics;
use Illuminate\Http\Request;

class TopBusinessesController extends Controller
{
    public function index(Request $request)
    {
        $allBusinesses = BusinessGrowthMetrics::publicBusinesses();

        $filteredBusinesses = BusinessGrowthMetrics::filterByCity($allBusinesses, $request->query('city'));
        $filteredBusinesses = BusinessGrowthMetrics::filterByCategory($filteredBusinesses, $request->query('category'));

        $topRecommended = $filteredBusinesses->take(12);

        $mostRecommended = $filteredBusinesses
            ->filter(fn ($business) => (int) ($business->growth_recommendations_count ?? 0) > 0)
            ->sortByDesc(fn ($business) => (int) ($business->growth_recommendations_count ?? 0))
            ->take(8)
            ->values();

        $highestRated = $filteredBusinesses
            ->filter(fn ($business) => (int) ($business->growth_reviews_count ?? 0) > 0)
            ->sortByDesc(fn ($business) => ((float) ($business->growth_average_rating ?? 0) * 100) + (int) ($business->growth_reviews_count ?? 0))
            ->take(8)
            ->values();

        $newestBusinesses = $filteredBusinesses
            ->sortByDesc(fn ($business) => $business->created_at?->timestamp ?? 0)
            ->take(8)
            ->values();

        $verifiedBusinesses = $filteredBusinesses
            ->filter(fn ($business) => (bool) $business->is_verified)
            ->take(8)
            ->values();

        $premiumBusinesses = $filteredBusinesses
            ->filter(fn ($business) => $business->isPremium())
            ->take(8)
            ->values();

        $topPleven = BusinessGrowthMetrics::filterByCity($allBusinesses, 'Плевен')->take(6);
        $topAuto = BusinessGrowthMetrics::filterByCategory($allBusinesses, 'Автосервизи')->take(6);
        $topMakers = BusinessGrowthMetrics::filterByCategory($allBusinesses, 'Ремонти и строителство')
            ->unique('id')
            ->take(6)
            ->values();
        $topCleaning = BusinessGrowthMetrics::filterByCategory($allBusinesses, 'Почистване')->take(6);
        $topBusinessServices = BusinessGrowthMetrics::filterByCategory($allBusinesses, 'Бизнес консултации')
            ->merge(BusinessGrowthMetrics::filterByCategory($allBusinesses, 'Счетоводство и финанси'))
            ->unique('id')
            ->take(6)
            ->values();
        $popularCategories = BusinessGrowthMetrics::popularCategories($allBusinesses, 10);

        $cities = $allBusinesses
            ->flatMap(fn ($business) => $business->serviceCities())
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $categories = $popularCategories->pluck('name');

        return view('top-businesses', compact(
            'allBusinesses',
            'filteredBusinesses',
            'topRecommended',
            'mostRecommended',
            'highestRated',
            'newestBusinesses',
            'verifiedBusinesses',
            'premiumBusinesses',
            'topPleven',
            'topAuto',
            'topMakers',
            'topCleaning',
            'topBusinessServices',
            'popularCategories',
            'cities',
            'categories'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BusinessAnalyticsEvent;
use App\Models\Review;
use App\Models\User;
use App\Support\BusinessGrowthMetrics;
use App\Support\CategoryCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $businesses = BusinessGrowthMetrics::publicBusinesses();

        $businesses = BusinessGrowthMetrics::filterByCity($businesses, $request->query('city'));
        $businesses = BusinessGrowthMetrics::filterByCategory($businesses, $request->query('category'));

        if ($request->boolean('emergency')) {
            $businesses = $businesses->filter(fn (User $business) => $business->hasEmergencyServices())->values();
        }

        if ($request->boolean('works_24_7')) {
            $businesses = $businesses->filter(fn (User $business) => $business->worksAroundClock())->values();
        }

        if ($request->boolean('verified')) {
            $businesses = $businesses->filter(fn (User $business) => (bool) $business->is_verified)->values();
        }

        if ($request->boolean('premium')) {
            $businesses = $businesses->filter(fn (User $business) => $business->isPremium())->values();
        }

        if ($request->query('rating') === '4plus') {
            $businesses = $businesses
                ->filter(fn (User $business) => (float) ($business->growth_average_rating ?? 0) >= 4)
                ->values();
        }

        return view('businesses.index', compact('businesses'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'business') {
            abort(404);
        }

        $viewer = auth()->user();
        $canPreviewHiddenProfile = $viewer
            && ($viewer->id === $user->id || $viewer->role === 'admin');

        if (!$user->isPubliclyVisible() && !$canPreviewHiddenProfile) {
            abort(404);
        }

        if (!$canPreviewHiddenProfile && CategoryCatalog::businessHasHiddenCategory($user)) {
            abort(404);
        }

        if (!$canPreviewHiddenProfile && !request()->has('analytics_intent')) {
            BusinessAnalyticsEvent::record($user, BusinessAnalyticsEvent::PROFILE_VIEW, $viewer, [
                'source' => 'business_profile',
            ]);
        }

        $relations = [
            'services' => function ($query) {
                $query->latest();
            },
        ];

        if (Schema::hasTable('business_photos')) {
            $relations[] = 'businessPhotos';
        }

        $user->loadMissing($relations);

        $approvedReviews = collect();
        $reviewsCount = 0;
        $averageRating = null;

        if (Schema::hasTable('reviews')) {
            $approvedReviews = Review::query()
                ->where('business_id', $user->id)
                ->where('status', Review::STATUS_APPROVED)
                ->latest('approved_at')
                ->take(6)
                ->get();

            $reviewsCount = Review::query()
                ->where('business_id', $user->id)
                ->where('status', Review::STATUS_APPROVED)
                ->count();

            $averageRating = Review::query()
                ->where('business_id', $user->id)
                ->where('status', Review::STATUS_APPROVED)
                ->avg('rating');
        }

        $similarBusinessesQuery = User::publiclyVisible()
            ->publicRanked()
            ->whereKeyNot($user->id);

        if ($user->business_category || $user->city) {
            $similarBusinessesQuery->where(function ($query) use ($user) {
                if ($user->business_category) {
                    $query->where('business_category', $user->business_category);
                }

                if ($user->city) {
                    $method = $user->business_category ? 'orWhere' : 'where';
                    $query->{$method}('city', $user->city);
                }
            });
        }

        $similarBusinesses = $similarBusinessesQuery
            ->latest()
            ->take(3)
            ->get()
            ->reject(fn (User $business) => CategoryCatalog::businessHasHiddenCategory($business))
            ->values();

        $recommendationsCount = $user->recommendationsCount();

        return view('businesses.show', compact('user', 'similarBusinesses', 'approvedReviews', 'reviewsCount', 'averageRating', 'recommendationsCount'));
    }
}

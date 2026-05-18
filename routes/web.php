<?php

use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessAnalyticsController;
use App\Http\Controllers\BusinessPhotoController;
use App\Http\Controllers\BusinessServiceRequestController;
use App\Http\Controllers\AdminBusinessController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminServiceRequestController;
use App\Http\Controllers\BusinessRecommendationController;
use App\Http\Controllers\CustomerOfferController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SeoPageController;
use App\Http\Controllers\ServiceRequestAssignmentController;
use App\Http\Controllers\ServiceRequestPublicOfferController;
use App\Http\Controllers\ServiceRequestOfferController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TopBusinessesController;
use App\Models\BusinessAnalyticsEvent;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use App\Support\BusinessGrowthMetrics;
use App\Support\CategoryCatalog;
use Illuminate\Support\Facades\Schema;
Route::get('/', function () {
    $featuredBusinesses = collect();
    $topBusinesses = collect();
    $mostRecommendedBusinesses = collect();
    $verifiedBusinesses = collect();
    $newestBusinesses = collect();
    $popularCategories = collect();
    $latestReviews = collect();
    $heroStats = [
        'requests_this_month' => 0,
        'active_categories' => CategoryCatalog::all()->count(),
        'businesses' => 0,
        'premium_businesses' => 0,
    ];

    if (
        Schema::hasTable('users')
        && Schema::hasColumn('users', 'role')
        && Schema::hasColumn('users', 'subscription_status')
        && Schema::hasColumn('users', 'trial_ends_at')
        && Schema::hasColumn('users', 'subscription_ends_at')
    ) {
        $publicBusinesses = BusinessGrowthMetrics::publicBusinesses();
        $featuredBusinesses = $publicBusinesses->take(5);
        $topBusinesses = $publicBusinesses->take(6);
        $heroStats['businesses'] = $publicBusinesses->count();
        $heroStats['premium_businesses'] = $publicBusinesses
            ->filter(fn ($business) => $business->isPremium())
            ->count();
        $mostRecommendedBusinesses = $publicBusinesses
            ->filter(fn ($business) => (int) ($business->growth_recommendations_count ?? 0) > 0)
            ->sortByDesc(fn ($business) => (int) ($business->growth_recommendations_count ?? 0))
            ->take(6)
            ->values();
        $verifiedBusinesses = $publicBusinesses
            ->filter(fn ($business) => (bool) $business->is_verified)
            ->take(6)
            ->values();
        $newestBusinesses = $publicBusinesses
            ->sortByDesc(fn ($business) => $business->created_at?->timestamp ?? 0)
            ->take(6)
            ->values();
        $popularCategories = BusinessGrowthMetrics::popularCategories($publicBusinesses);

        if (Schema::hasTable('reviews')) {
            $latestReviews = Review::query()
                ->approved()
                ->with('business')
                ->whereHas('business', fn ($query) => $query->publiclyVisible())
                ->latest('approved_at')
                ->take(4)
                ->get();
        }
    }

    if (Schema::hasTable('service_requests')) {
        $heroStats['requests_this_month'] = ServiceRequest::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }

    return view('welcome', compact(
        'featuredBusinesses',
        'topBusinesses',
        'mostRecommendedBusinesses',
        'verifiedBusinesses',
        'newestBusinesses',
        'popularCategories',
        'latestReviews',
        'heroStats'
    ));
});

Route::get('/categories', function () {
    $categoryDefinitions = [
        ['name' => 'Ремонти и строителство', 'desc' => 'Майстори, довършителни работи, бани, покриви и монтажи'],
        ['name' => 'Спешни домашни услуги', 'desc' => 'ВиК, електро, ключари, течове и спешна помощ за дома'],
        ['name' => 'Поддръжка на домове и имоти', 'desc' => 'Почистване, хамали, климатици, градини и абонаментна поддръжка'],
        ['name' => 'ВиК услуги', 'desc' => 'Аварии, течове, бойлери и инсталации'],
        ['name' => 'Електроуслуги', 'desc' => 'Табла, контакти, осветление и диагностика'],
        ['name' => 'Автосервизи', 'desc' => 'Ремонт, гуми, диагностика и поддръжка'],
        ['name' => 'Ремонт на техника', 'desc' => 'Сервиз на уреди, диагностика и домашна техника'],
        ['name' => 'Услуги за малки бизнеси', 'desc' => 'Счетоводители, адвокати, маркетинг, принт и охрана'],
        ['name' => 'Красота и лични услуги', 'desc' => 'Фризьори, маникюр, козметика, фитнес и лични услуги'],
        ['name' => 'Образование и курсове', 'desc' => 'Частни уроци, автошколи и практически обучения'],
        ['name' => 'Събития и празници', 'desc' => 'DJ, водещи, кетъринг, торти, фотографи и видеографи'],
        ['name' => 'Локални магазини и търговци', 'desc' => 'Магазини, авточасти, строителни материали и специализирани стоки'],
        ['name' => 'Почистване', 'desc' => 'Домове, офиси, абонамент и след ремонт'],
    ];

    $publicBusinesses = collect();

    if (
        Schema::hasTable('users')
        && Schema::hasColumn('users', 'role')
        && Schema::hasColumn('users', 'subscription_status')
        && Schema::hasColumn('users', 'trial_ends_at')
        && Schema::hasColumn('users', 'subscription_ends_at')
    ) {
        $publicBusinesses = BusinessGrowthMetrics::publicBusinesses();
    }

    $categories = collect($categoryDefinitions)
        ->map(function ($category) use ($publicBusinesses) {
            $category['count'] = BusinessGrowthMetrics::filterByCategory($publicBusinesses, $category['name'])->count();

            return $category;
        });

    return view('categories', compact('categories'));
});
Route::get('/plans', [BillingController::class, 'plans'])->name('plans');
Route::redirect('/pricing', '/plans');
Route::view('/za-biznesi', 'za-biznesi')->name('business.landing');
Route::redirect('/za-biznesa', '/za-biznesi');
Route::redirect('/add-business', '/za-biznesi');
Route::get('/top-biznesi', [TopBusinessesController::class, 'index'])->name('top.businesses');
Route::redirect('/top-businesses', '/top-biznesi');
Route::get('/grad/{city}', [SeoPageController::class, 'city'])->name('seo.city');
Route::get('/grad/{city}/{category}', [SeoPageController::class, 'cityCategory'])->name('seo.city.category');
Route::get('/uslugi/{category}/{city}', [SeoPageController::class, 'categoryCity'])->name('seo.service.city');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/how-it-works', 'how-it-works');
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect('/login');
    }

if ($user->role === 'admin') {
    $allBusinesses = User::query()
        ->where('role', 'business')
        ->latest()
        ->get();

    $activeBusinesses = $allBusinesses->filter(fn ($business) => $business->effectiveSubscriptionStatus() === 'active')->count();
    $trialBusinesses = $allBusinesses->filter(fn ($business) => $business->effectiveSubscriptionStatus() === 'trial')->count();
    $expiredBusinesses = $allBusinesses->filter(fn ($business) => $business->effectiveSubscriptionStatus() === 'expired')->count();
    $cancelledBusinesses = $allBusinesses->filter(fn ($business) => $business->effectiveSubscriptionStatus() === 'cancelled')->count();
    $verifiedBusinesses = $allBusinesses->filter(fn ($business) => (bool) $business->is_verified)->count();
    $unverifiedBusinesses = $allBusinesses->count() - $verifiedBusinesses;
    $newBusinessesLast7Days = $allBusinesses->filter(fn ($business) => $business->created_at && $business->created_at->greaterThanOrEqualTo(now()->subDays(7)))->count();
    $standardBusinesses = $allBusinesses->filter(fn ($business) => $business->planKey() === 'standard')->count();
    $premiumBusinesses = $allBusinesses->filter(fn ($business) => $business->planKey() === 'premium')->count();
    $activeBusinessModels = $allBusinesses->filter(fn ($business) => $business->effectiveSubscriptionStatus() === 'active');
    $trialBusinessModels = $allBusinesses->filter(fn ($business) => $business->effectiveSubscriptionStatus() === 'trial');
    $totalExtraCitiesUsed = $allBusinesses->sum(fn ($business) => $business->extraCitiesUsed());

    $adminStats = [
        'total_users' => User::count(),
        'total_businesses' => $allBusinesses->count(),
        'active_businesses' => $activeBusinesses,
        'trial_businesses' => $trialBusinesses,
        'expired_businesses' => $expiredBusinesses,
        'cancelled_businesses' => $cancelledBusinesses,
        'verified_businesses' => $verifiedBusinesses,
        'unverified_businesses' => $unverifiedBusinesses,
        'new_businesses_last_7_days' => $newBusinessesLast7Days,
        'standard_businesses' => $standardBusinesses,
        'premium_businesses' => $premiumBusinesses,
        'total_extra_cities_used' => $totalExtraCitiesUsed,
        'potential_mrr' => $activeBusinessModels->sum(fn ($business) => $business->estimatedMonthlyAmount()),
        'trial_pipeline' => $trialBusinessModels->sum(fn ($business) => $business->estimatedMonthlyAmount()),
        'estimated_conversion' => $trialBusinessModels->sum(fn ($business) => $business->estimatedMonthlyAmount()) * 0.25,
    ];

    $businessFilter = request('status', 'all');

    $businesses = $allBusinesses->filter(function ($business) use ($businessFilter) {
        return match ($businessFilter) {
            'trial', 'active', 'expired', 'cancelled' => $business->effectiveSubscriptionStatus() === $businessFilter,
            'verified' => (bool) $business->is_verified,
            'unverified' => !$business->is_verified,
            'expiring_soon' => $business->effectiveSubscriptionStatus() === 'trial' && $business->trialDaysRemaining() <= 7,
            default => true,
        };
    })->values();

    $pendingBusinesses = $allBusinesses
        ->filter(fn ($business) => !$business->is_verified)
        ->sortByDesc(fn ($business) => $business->created_at?->timestamp ?? 0)
        ->take(8)
        ->values();

    $pendingReviews = collect();
    $serviceRequests = collect();
    $leadStats = [
        'total' => 0,
        'new' => 0,
        'contacted' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'closed' => 0,
        'urgent' => 0,
    ];
    $platformAnalytics = [
        'total_profile_views' => 0,
        'total_clicks' => 0,
        'current_month_profile_views' => 0,
        'current_month_phone_clicks' => 0,
        'current_month_clicks' => 0,
        'top_by_views' => collect(),
        'top_by_clicks' => collect(),
    ];

    if (Schema::hasTable('reviews')) {
        $pendingReviews = Review::query()
            ->pending()
            ->with('business')
            ->latest()
            ->take(20)
            ->get();
    }

    if (Schema::hasTable('service_requests')) {
        $serviceRequestRelations = ['assignedBusiness', 'customer'];

        if (Schema::hasTable('service_request_assignments')) {
            $serviceRequestRelations[] = 'assignments.business';
        }

        if (Schema::hasTable('service_request_offers')) {
            $serviceRequestRelations[] = 'offers.business';
            $serviceRequestRelations[] = 'selectedOffer.business';
        }

        $serviceRequests = ServiceRequest::query()
            ->with($serviceRequestRelations)
            ->latest()
            ->take(50)
            ->get();

        $leadStats = [
            'total' => ServiceRequest::query()->count(),
            'new' => ServiceRequest::query()->where('status', ServiceRequest::STATUS_NEW)->count(),
            'contacted' => ServiceRequest::query()->where('status', ServiceRequest::STATUS_CONTACTED)->count(),
            'completed' => ServiceRequest::query()
                ->whereIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CLOSED])
                ->count(),
            'cancelled' => ServiceRequest::query()->where('status', ServiceRequest::STATUS_CANCELLED)->count(),
            'closed' => ServiceRequest::query()->where('status', ServiceRequest::STATUS_CLOSED)->count(),
            'urgent' => Schema::hasColumn('service_requests', 'urgency')
                ? ServiceRequest::query()->where('urgency', ServiceRequest::URGENCY_URGENT)->count()
                : 0,
        ];
    }

    if (Schema::hasTable('business_analytics_events')) {
        $totalAnalytics = BusinessAnalyticsEvent::platformCounts();
        $monthAnalytics = BusinessAnalyticsEvent::platformCounts(now()->subDays(30));

        $platformAnalytics = [
            'total_profile_views' => (int) ($totalAnalytics[BusinessAnalyticsEvent::PROFILE_VIEW] ?? 0),
            'total_clicks' => BusinessAnalyticsEvent::clickTotal($totalAnalytics),
            'current_month_profile_views' => (int) ($monthAnalytics[BusinessAnalyticsEvent::PROFILE_VIEW] ?? 0),
            'current_month_phone_clicks' => (int) ($monthAnalytics[BusinessAnalyticsEvent::PHONE_CLICK] ?? 0),
            'current_month_clicks' => BusinessAnalyticsEvent::clickTotal($monthAnalytics),
            'top_by_views' => BusinessAnalyticsEvent::topBusinessesBy(BusinessAnalyticsEvent::PROFILE_VIEW, 5),
            'top_by_clicks' => BusinessAnalyticsEvent::query()
                ->selectRaw('business_id, COUNT(*) as aggregate')
                ->whereIn('event_type', BusinessAnalyticsEvent::clickEventTypes())
                ->whereNotNull('business_id')
                ->groupBy('business_id')
                ->orderByDesc('aggregate')
                ->with('business')
                ->take(5)
                ->get(),
        ];
    }

    return view('dashboards.admin', compact('adminStats', 'businesses', 'businessFilter', 'pendingBusinesses', 'pendingReviews', 'platformAnalytics', 'serviceRequests', 'leadStats'));
}

if ($user->role === 'business') {
    $user->initializeTrialIfMissing();
    $user->ensureOfferPointsInitialized();
    $businessRelations = ['services'];

    if (Schema::hasTable('business_photos')) {
        $businessRelations[] = 'businessPhotos';
    }

    $user->loadMissing($businessRelations);

    $analyticsMonthStats = BusinessAnalyticsEvent::emptyCounts();
    $analyticsTotalStats = BusinessAnalyticsEvent::emptyCounts();

    if (Schema::hasTable('business_analytics_events')) {
        $analyticsMonthStats = BusinessAnalyticsEvent::countsForBusiness($user, now()->startOfMonth());
        $analyticsTotalStats = BusinessAnalyticsEvent::countsForBusiness($user);
    }

    $analyticsStats = [
        'profile_views' => (int) ($analyticsMonthStats[BusinessAnalyticsEvent::PROFILE_VIEW] ?? 0),
        'phone_clicks' => (int) ($analyticsMonthStats[BusinessAnalyticsEvent::PHONE_CLICK] ?? 0),
        'website_clicks' => (int) ($analyticsMonthStats[BusinessAnalyticsEvent::WEBSITE_CLICK] ?? 0),
        'social_clicks' => (int) ($analyticsMonthStats[BusinessAnalyticsEvent::SOCIAL_CLICK] ?? 0),
        'inquiry_clicks' => (int) ($analyticsMonthStats[BusinessAnalyticsEvent::INQUIRY_CLICK] ?? 0),
        'chat_clicks' => (int) ($analyticsMonthStats[BusinessAnalyticsEvent::CHAT_CLICK] ?? 0),
        'website_social_clicks' => BusinessAnalyticsEvent::websiteAndSocialTotal($analyticsMonthStats),
        'total_clicks' => BusinessAnalyticsEvent::clickTotal($analyticsMonthStats),
    ];

    $analyticsTotals = [
        'profile_views' => (int) ($analyticsTotalStats[BusinessAnalyticsEvent::PROFILE_VIEW] ?? 0),
        'phone_clicks' => (int) ($analyticsTotalStats[BusinessAnalyticsEvent::PHONE_CLICK] ?? 0),
        'website_clicks' => (int) ($analyticsTotalStats[BusinessAnalyticsEvent::WEBSITE_CLICK] ?? 0),
        'social_clicks' => (int) ($analyticsTotalStats[BusinessAnalyticsEvent::SOCIAL_CLICK] ?? 0),
        'inquiry_clicks' => (int) ($analyticsTotalStats[BusinessAnalyticsEvent::INQUIRY_CLICK] ?? 0),
        'chat_clicks' => (int) ($analyticsTotalStats[BusinessAnalyticsEvent::CHAT_CLICK] ?? 0),
        'website_social_clicks' => BusinessAnalyticsEvent::websiteAndSocialTotal($analyticsTotalStats),
        'total_clicks' => BusinessAnalyticsEvent::clickTotal($analyticsTotalStats),
    ];

    $businessReviews = collect();
    $assignedServiceRequests = collect();
    $serviceRequestStats = [
        'total' => 0,
        'new' => 0,
        'contacted' => 0,
        'completed' => 0,
        'cancelled' => 0,
    ];
    $offerStats = [
        'points_balance' => $user->offerPointsBalance(),
        'remaining_offers' => $user->remainingOfferCount(),
        'sent_offers' => 0,
        'has_request_based_categories' => $user->hasRequestBasedCategories(),
    ];
    $reviewStats = [
        'approved' => 0,
        'pending' => 0,
        'average' => null,
    ];

    if (Schema::hasTable('reviews')) {
        $businessReviews = Review::query()
            ->where('business_id', $user->id)
            ->whereIn('status', [Review::STATUS_APPROVED, Review::STATUS_PENDING])
            ->latest()
            ->take(8)
            ->get();

        $reviewStats = [
            'approved' => Review::query()
                ->where('business_id', $user->id)
                ->where('status', Review::STATUS_APPROVED)
                ->count(),
            'pending' => Review::query()
                ->where('business_id', $user->id)
                ->where('status', Review::STATUS_PENDING)
                ->count(),
            'average' => Review::query()
                ->where('business_id', $user->id)
                ->where('status', Review::STATUS_APPROVED)
                ->avg('rating'),
        ];
    }

    if (Schema::hasTable('service_request_assignments')) {
        $assignedServiceRequests = ServiceRequestAssignment::query()
            ->with('serviceRequest')
            ->where('business_id', $user->id)
            ->latest()
            ->take(20)
            ->get();
    }

    if (Schema::hasTable('service_requests')) {
        $serviceRequestBaseQuery = ServiceRequest::query()
            ->where(function ($query) use ($user) {
                $query->where('assigned_business_id', $user->id);

                if (Schema::hasTable('service_request_assignments')) {
                    $query->orWhereHas('assignments', fn ($assignmentQuery) => $assignmentQuery->where('business_id', $user->id));
                }
            });

        $serviceRequestStats = [
            'total' => (clone $serviceRequestBaseQuery)->count(),
            'new' => (clone $serviceRequestBaseQuery)->where('status', ServiceRequest::STATUS_NEW)->count(),
            'contacted' => (clone $serviceRequestBaseQuery)->where('status', ServiceRequest::STATUS_CONTACTED)->count(),
            'completed' => (clone $serviceRequestBaseQuery)
                ->whereIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CLOSED])
                ->count(),
            'cancelled' => (clone $serviceRequestBaseQuery)->where('status', ServiceRequest::STATUS_CANCELLED)->count(),
        ];
    }

    if (Schema::hasTable('service_request_offers')) {
        $offerStats['sent_offers'] = \App\Models\ServiceRequestOffer::query()
            ->where('business_id', $user->id)
            ->count();
    }

    return view('dashboards.business', compact('analyticsStats', 'analyticsTotals', 'businessReviews', 'reviewStats', 'assignedServiceRequests', 'serviceRequestStats', 'offerStats'));
}

$customerServiceRequests = collect();
$customerRequestStats = [
    'total' => 0,
    'open' => 0,
    'completed' => 0,
    'offers' => 0,
];

if (Schema::hasTable('service_requests')) {
    $customerRequestRelations = ['assignedBusiness'];

    if (Schema::hasTable('service_request_offers')) {
        $customerRequestRelations[] = 'offers.business';
        $customerRequestRelations[] = 'selectedOffer.business';
    }

    if (Schema::hasTable('service_request_photos')) {
        $customerRequestRelations[] = 'photos';
    }

    $customerRequestQuery = ServiceRequest::query()
        ->with($customerRequestRelations)
        ->where(function ($query) use ($user) {
            if (Schema::hasColumn('service_requests', 'customer_id')) {
                $query->where('customer_id', $user->id);
            } else {
                $query->whereRaw('1 = 0');
            }

            if (filled($user->email) && Schema::hasColumn('service_requests', 'email')) {
                $query->orWhere(function ($legacyQuery) use ($user) {
                    if (Schema::hasColumn('service_requests', 'customer_id')) {
                        $legacyQuery->whereNull('customer_id');
                    }

                    $legacyQuery->where('email', $user->email);
                });
            }
        });

    $customerServiceRequests = (clone $customerRequestQuery)
        ->latest()
        ->take(50)
        ->get();

    $customerRequestStats = [
        'total' => $customerServiceRequests->count(),
        'open' => $customerServiceRequests
            ->whereIn('status', [ServiceRequest::STATUS_NEW, ServiceRequest::STATUS_OPEN, ServiceRequest::STATUS_CONTACTED, ServiceRequest::STATUS_IN_PROGRESS])
            ->count(),
        'completed' => $customerServiceRequests
            ->whereIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CLOSED])
            ->count(),
        'offers' => $customerServiceRequests->sum(fn ($serviceRequest) => $serviceRequest->relationLoaded('offers') ? $serviceRequest->offers->count() : 0),
    ];
}

return view('dashboards.client', compact('customerServiceRequests', 'customerRequestStats'));
})->name('dashboard');
Route::view('/offer-services', 'offer-services')->name('offer-services');
Route::view('/contact', 'contact')->name('contact');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/cookies', 'cookies')->name('cookies');
Route::redirect('/request-service', '/zayavi-oferta');
Route::redirect('/request', '/zayavi-oferta');
Route::redirect('/zayavka', '/zayavi-oferta');
Route::get('/zayavka/{serviceRequest:public_token}/offers', [ServiceRequestPublicOfferController::class, 'show'])->name('service-requests.offers.show');
Route::post('/zayavka/{serviceRequest:public_token}/offers/{offer}/accept', [ServiceRequestPublicOfferController::class, 'accept'])->name('service-requests.offers.accept');
Route::get('/zayavi-oferta', [ServiceRequestController::class, 'create'])->name('request.service');
Route::post('/zayavi-oferta', [ServiceRequestController::class, 'store'])->name('request.service.store');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');

    Route::get('/business/profile/edit', [BusinessProfileController::class, 'edit'])->name('business.profile.edit');
    Route::put('/business/profile/update', [BusinessProfileController::class, 'update'])->name('business.profile.update');
    Route::post('/business/profile/photos', [BusinessPhotoController::class, 'store'])->name('business.profile.photos.store');
    Route::delete('/business/profile/photos/{businessPhoto}', [BusinessPhotoController::class, 'destroy'])->name('business.profile.photos.destroy');
    Route::get('/business/service-requests', [BusinessServiceRequestController::class, 'index'])->name('business.service-requests.index');
    Route::patch('/business/service-requests/{serviceRequest}/contacted', [BusinessServiceRequestController::class, 'contacted'])->name('business.service-requests.contacted');
    Route::patch('/business/service-requests/{serviceRequest}/completed', [BusinessServiceRequestController::class, 'completed'])->name('business.service-requests.completed');
    Route::patch('/business/service-requests/{serviceRequest}/cancelled', [BusinessServiceRequestController::class, 'cancelled'])->name('business.service-requests.cancelled');
    Route::post('/business/service-requests/{serviceRequest}/offers', [ServiceRequestOfferController::class, 'store'])->name('business.service-requests.offers.store');
    Route::get('/business/billing', [BillingController::class, 'show'])->name('business.billing');
    Route::post('/business/billing/checkout', [BillingController::class, 'checkout'])->name('business.billing.checkout');
    Route::post('/business/billing/portal', [BillingController::class, 'portal'])->name('business.billing.portal');
    Route::post('/business/billing/upgrade-premium', [BillingController::class, 'upgradePremium'])->name('business.billing.upgrade-premium');

    Route::patch('/admin/businesses/{user}/activate-30-days', [AdminBusinessController::class, 'activate'])->name('admin.businesses.activate');
    Route::patch('/admin/businesses/{user}/extend-trial', [AdminBusinessController::class, 'extendTrial'])->name('admin.businesses.extend-trial');
    Route::patch('/admin/businesses/{user}/expire', [AdminBusinessController::class, 'expire'])->name('admin.businesses.expire');
    Route::patch('/admin/businesses/{user}/cancel', [AdminBusinessController::class, 'cancel'])->name('admin.businesses.cancel');
    Route::patch('/admin/businesses/{user}/verify', [AdminBusinessController::class, 'verify'])->name('admin.businesses.verify');
    Route::patch('/admin/businesses/{user}/unverify', [AdminBusinessController::class, 'unverify'])->name('admin.businesses.unverify');
    Route::get('/admin/service-requests', [AdminServiceRequestController::class, 'index'])->name('admin.service-requests.index');
    Route::patch('/admin/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::patch('/admin/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::patch('/admin/service-requests/{serviceRequest}/contacted', [AdminServiceRequestController::class, 'markContacted'])->name('admin.service-requests.contacted');
    Route::patch('/admin/service-requests/{serviceRequest}/closed', [AdminServiceRequestController::class, 'markClosed'])->name('admin.service-requests.closed');
    Route::patch('/service-request-assignments/{assignment}/contacted', [ServiceRequestAssignmentController::class, 'contacted'])->name('service-request-assignments.contacted');
    Route::patch('/service-request-assignments/{assignment}/declined', [ServiceRequestAssignmentController::class, 'declined'])->name('service-request-assignments.declined');
    Route::patch('/service-request-assignments/{assignment}/closed', [ServiceRequestAssignmentController::class, 'closed'])->name('service-request-assignments.closed');
    Route::patch('/customer/offers/{offer}/accept', [CustomerOfferController::class, 'accept'])->name('customer.offers.accept');
});

Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{user}/track/phone', [BusinessAnalyticsController::class, 'phone'])->name('businesses.track.phone');
Route::get('/businesses/{user}/track/website', [BusinessAnalyticsController::class, 'website'])->name('businesses.track.website');
Route::get('/businesses/{user}/track/inquiry', [BusinessAnalyticsController::class, 'inquiry'])->name('businesses.track.inquiry');
Route::get('/businesses/{user}/track/chat', [BusinessAnalyticsController::class, 'chat'])->name('businesses.track.chat');
Route::get('/businesses/{user}/track/social/{platform}', [BusinessAnalyticsController::class, 'social'])->name('businesses.track.social');
Route::post('/businesses/{user}/service-requests', [BusinessServiceRequestController::class, 'store'])->name('businesses.service-requests.store');
Route::post('/businesses/{user}/reviews', [ReviewController::class, 'store'])->name('businesses.reviews.store');
Route::post('/businesses/{user}/recommend', [BusinessRecommendationController::class, 'store'])->name('businesses.recommendations.store');
Route::get('/businesses/{user}', [BusinessController::class, 'show'])->name('businesses.show');

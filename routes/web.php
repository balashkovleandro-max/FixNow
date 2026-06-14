<?php

use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessAnalyticsController;
use App\Http\Controllers\BusinessInsightsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusinessPhotoController;
use App\Http\Controllers\BusinessServiceRequestController;
use App\Http\Controllers\AdminBusinessController;
use App\Http\Controllers\AdminFreelancerCreditController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminServiceRequestController;
use App\Http\Controllers\BonHomeController;
use App\Http\Controllers\BusinessDiagnosticController;
use App\Http\Controllers\BusinessRecommendationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CustomerOfferController;
use App\Http\Controllers\FreelancerCreditController;
use App\Http\Controllers\FreelancerDirectoryController;
use App\Http\Controllers\FreelancerJobController;
use App\Http\Controllers\FreelancerPortfolioController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\PublicSearchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SeoPageController;
use App\Http\Controllers\ServiceRequestAssignmentController;
use App\Http\Controllers\ServiceRequestPublicOfferController;
use App\Http\Controllers\ServiceRequestOfferController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TopBusinessesController;
use App\Models\BusinessAnalyticsEvent;
use App\Models\BusinessDiagnostic;
use App\Models\FreelancerJob;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use App\Support\BusinessGrowthMetrics;
use App\Support\CategoryCatalog;
use App\Support\FreelancerCredits;
use App\Support\ProfileCompletion;
use App\Support\ProfileTrust;
use Illuminate\Support\Facades\Schema;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => 'BON',
]))->name('health');

Route::get('/robots.txt', fn () => response(file_get_contents(public_path('robots.txt')), 200, [
    'Content-Type' => 'text/plain; charset=UTF-8',
]))->name('robots');

Route::get('/sitemap.xml', fn () => response(file_get_contents(public_path('sitemap.xml')), 200, [
    'Content-Type' => 'application/xml; charset=UTF-8',
]))->name('sitemap');

Route::get('/bon', [BonHomeController::class, 'index'])->name('bon.index');
Route::get('/', [BonHomeController::class, 'index'])->name('home');
Route::view('/onboarding', 'bon.onboarding')->middleware('auth')->name('bon.onboarding');
Route::view('/tools', 'bon.tools')->name('bon.tools');
Route::redirect('/instrumenti', '/tools');
Route::view('/za-potrebiteli', 'bon.consumers')->name('bon.consumers');
Route::get('/freelancers', [FreelancerDirectoryController::class, 'index'])->name('bon.freelancers');
Route::redirect('/za-freelanceri', '/freelancers');
Route::get('/freelancers/{user}', [FreelancerProfileController::class, 'show'])->name('freelancers.show');
Route::view('/talent-network', 'bon.talent-network')->name('bon.talent-network');
Route::view('/bon/command-center', 'bon.command-center')->name('bon.command-center');
Route::get('/bon/business-problem', [BusinessDiagnosticController::class, 'create'])->name('bon.business-problem');
Route::post('/bon/business-problem', [BusinessDiagnosticController::class, 'store'])->middleware('throttle:10,1')->name('bon.business-problem.store');
Route::get('/bon/business-problem/{diagnostic}/result', [BusinessDiagnosticController::class, 'result'])->name('bon.business-problem.result');
Route::view('/bon/profile', 'bon.profile')->name('bon.profile');
Route::view('/bon/demo-business-profile', 'bon.profile')->name('bon.demo-profile');
Route::redirect('/bon/business-profile', '/bon/profile');
Route::redirect('/business/command-center', '/bon/command-center');

Route::redirect('/legacy-home', '/');

Route::get('/categories', function () {
    $categoryDescriptions = [
        'Ресторанти и кафенета' => 'Места за храна, кафе, срещи, събития и локални преживявания.',
        'Хотели и настаняване' => 'Хотели, къщи за гости, апартаменти и други места за престой.',
        'Красота и козметика' => 'Салони, фризьори, маникюр, козметика и лична грижа.',
        'Фитнес и спорт' => 'Фитнес, спортни клубове, треньори, танци, йога и активности.',
        'Здраве и уелнес' => 'Здравни, уелнес, терапевтични и възстановителни услуги.',
        'Автосервизи' => 'Сервизи, диагностика, гуми, автомивки и авто поддръжка.',
        'Ремонти и строителство' => 'Ремонти, довършителни работи, строителни услуги и монтажи.',
        'Домашни услуги' => 'Помощ за дома, поддръжка, аварии, монтажи и локални услуги.',
        'Почистване' => 'Почистване за домове, офиси, обекти и абонаментна поддръжка.',
        'Образование и курсове' => 'Курсове, уроци, обучения, школи и професионално развитие.',
        'Маркетинг и реклама' => 'Маркетинг, реклама, social media, copywriting и кампании.',
        'Уеб сайтове и софтуер' => 'Сайтове, софтуер, автоматизации, интеграции и дигитални продукти.',
        'Дизайн и брандинг' => 'Визуална идентичност, UI/UX, графичен дизайн и бранд материали.',
        'Счетоводство и финанси' => 'Счетоводство, финансови услуги, анализи и бизнес отчетност.',
        'Правни услуги' => 'Правна помощ, договори, консултации и корпоративни услуги.',
        'Недвижими имоти' => 'Имоти, брокери, управление, оценки и консултации.',
        'Събития и фотография' => 'Фотография, видео, събития, монтаж и творчески услуги.',
        'Туризъм и развлечения' => 'Преживявания, развлечения, турове и локални активности.',
        'Магазини и търговия' => 'Магазини, търговци, продукти, шоуруми и локални брандове.',
        'Бизнес консултации' => 'Консултации, стратегия, операции, растеж и подобрение на процеси.',
        'Фрийланс услуги' => 'Независими специалисти, проектна работа и професионални услуги.',
        'Друго' => 'Други бизнеси, услуги и специализирани профили.',
    ];

    $categoryDefinitions = CategoryCatalog::all()
        ->map(fn ($category) => [
            'name' => $category['name'],
            'desc' => $categoryDescriptions[$category['name']] ?? 'Профили, услуги и бизнеси в тази категория.',
        ])
        ->all();

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
})->name('categories');
Route::get('/plans', [BillingController::class, 'plans'])->name('plans');
Route::redirect('/pricing', '/plans');
Route::view('/za-biznesi', 'bon.businesses')->name('business.landing');
Route::redirect('/za-biznesa', '/za-biznesi');
Route::redirect('/add-business', '/za-biznesi');
Route::get('/projects', [FreelancerJobController::class, 'publicIndex'])->name('freelancer.projects.index');
Route::get('/projects/create', function () {
    if (!auth()->check()) {
        return redirect()->route('register', ['role' => 'client']);
    }

    if (auth()->user()->isFreelancer()) {
        return redirect()->route('freelancer.jobs.index');
    }

    return redirect()->route('business.jobs.create');
})->name('freelancer.projects.create');
Route::get('/projects/{freelancerJob}', [FreelancerJobController::class, 'publicShow'])->name('freelancer.projects.show');
Route::get('/top-biznesi', [TopBusinessesController::class, 'index'])->name('top.businesses');
Route::redirect('/top-businesses', '/top-biznesi');
Route::get('/search', [PublicSearchController::class, 'index'])->name('search');
Route::get('/grad/{city}', [SeoPageController::class, 'city'])->name('seo.city');
Route::get('/grad/{city}/{category}', [SeoPageController::class, 'cityCategory'])->name('seo.city.category');
Route::get('/uslugi/{category}/{city}', [SeoPageController::class, 'categoryCity'])->name('seo.service.city');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/how-it-works', 'how-it-works');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::redirect('/dashboard', '/admin');
    Route::get('/businesses', [AdminController::class, 'businesses'])->name('businesses.index');
    Route::get('/businesses/{user}/edit', [AdminController::class, 'editBusiness'])->name('businesses.edit');
    Route::put('/businesses/{user}/profile', [AdminController::class, 'saveBusinessProfile'])->name('businesses.profile.update');
    Route::patch('/businesses/{user}', [AdminController::class, 'updateBusiness'])->name('businesses.update');
    Route::delete('/businesses/{user}', [AdminController::class, 'destroyBusiness'])->name('businesses.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/requests', [AdminController::class, 'requests'])->name('requests.index');
    Route::redirect('/consultations', '/admin/requests')->name('consultations.index');
    Route::patch('/requests/{serviceRequest}', [AdminController::class, 'updateRequest'])->name('requests.update');
    Route::delete('/requests/{serviceRequest}', [AdminController::class, 'destroyRequest'])->name('requests.destroy');
    Route::get('/offers', [AdminController::class, 'offers'])->name('offers.index');
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions.index');
    Route::get('/payments', [AdminController::class, 'subscriptions'])->name('payments');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('/cities', [AdminController::class, 'cities'])->name('cities.index');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/activity', [AdminController::class, 'activity'])->name('activity.index');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect('/login');
    }

    if ($user->role === 'admin' || $user->accountType() === 'admin') {
        return redirect()->route('admin.dashboard');
    }

if (false && $user->role === 'admin') {
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

    $adminStats['total_freelancers'] = User::query()->where('role', 'freelancer')->count();
    $adminStats['freelancer_jobs'] = Schema::hasTable('freelancer_jobs')
        ? FreelancerJob::query()->count()
        : 0;
    $adminStats['freelancer_applications'] = Schema::hasTable('freelancer_job_applications')
        ? \App\Models\FreelancerJobApplication::query()->count()
        : 0;
    $adminStats['credit_transactions'] = Schema::hasTable('freelancer_credit_transactions')
        ? \App\Models\FreelancerCreditTransaction::query()->count()
        : 0;
    $adminStats['business_diagnostics'] = Schema::hasTable('business_diagnostics')
        ? BusinessDiagnostic::query()->count()
        : 0;

    return view('dashboards.admin', compact('adminStats', 'businesses', 'businessFilter', 'pendingBusinesses', 'pendingReviews', 'platformAnalytics', 'serviceRequests', 'leadStats'));
}

if ($user->isBusiness()) {
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

    $freelancerJobStats = [
        'published' => 0,
        'open' => 0,
        'applications' => 0,
        'selected' => 0,
    ];
    $latestFreelancerJobs = collect();
    $recentFreelancerApplications = collect();
    $recentBusinessDiagnostics = collect();

    if (Schema::hasTable('freelancer_jobs')) {
        $freelancerJobsQuery = FreelancerJob::query()->where('business_id', $user->id);

        $freelancerJobStats['published'] = (clone $freelancerJobsQuery)->count();
        $freelancerJobStats['open'] = (clone $freelancerJobsQuery)->where('status', FreelancerJob::STATUS_OPEN)->count();

        $latestFreelancerJobs = (clone $freelancerJobsQuery)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        if (Schema::hasTable('freelancer_job_applications')) {
            $applicationQuery = \App\Models\FreelancerJobApplication::query()
                ->whereHas('job', fn ($query) => $query->where('business_id', $user->id));

            $freelancerJobStats['applications'] = (clone $applicationQuery)->count();
            $freelancerJobStats['selected'] = (clone $applicationQuery)
                ->where('status', \App\Models\FreelancerJobApplication::STATUS_ACCEPTED)
                ->count();

            $recentFreelancerApplications = (clone $applicationQuery)
                ->with(['freelancer', 'job'])
                ->latest()
                ->take(6)
                ->get();
        }
    }

    if (Schema::hasTable('business_diagnostics')) {
        $recentBusinessDiagnostics = BusinessDiagnostic::query()
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
    }

    return view('dashboards.business', compact('analyticsStats', 'analyticsTotals', 'businessReviews', 'reviewStats', 'assignedServiceRequests', 'serviceRequestStats', 'offerStats', 'freelancerJobStats', 'latestFreelancerJobs', 'recentFreelancerApplications', 'recentBusinessDiagnostics'));
}

if ($user->isFreelancer()) {
    FreelancerCredits::ensureMonthlyCredits($user);
    $user->loadMissing('freelancerPortfolioItems');

    $creditStats = FreelancerCredits::stats($user);
    $profile = ProfileCompletion::summary($user);
    $recentApplications = Schema::hasTable('freelancer_job_applications')
        ? $user->freelancerJobApplications()
            ->with('job.business')
            ->latest()
            ->take(8)
            ->get()
        : collect();

    $openJobs = Schema::hasTable('freelancer_jobs')
        ? FreelancerJob::query()
            ->open()
            ->with('business')
            ->withCount('applications')
            ->latest()
            ->take(6)
            ->get()
        : collect();

    return view('dashboards.freelancer', compact('creditStats', 'recentApplications', 'openJobs', 'profile'));
}

$customerServiceRequests = collect();
$favoriteProfiles = collect();
$customerFreelancerJobs = collect();
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

if (Schema::hasTable('user_favorites')) {
    $favoriteProfiles = $user->favorites()
        ->with('favoriteUser')
        ->latest()
        ->take(24)
        ->get()
        ->pluck('favoriteUser')
        ->filter()
        ->values();
}

if (Schema::hasTable('freelancer_jobs')) {
    $customerFreelancerJobs = $user->freelancerJobs()
        ->with(['applications.freelancer'])
        ->withCount('applications')
        ->latest()
        ->take(20)
        ->get();
}

return view('dashboards.client', compact('customerServiceRequests', 'customerRequestStats', 'favoriteProfiles', 'customerFreelancerJobs'));
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
Route::post('/zayavka/{serviceRequest:public_token}/offers/{offer}/accept', [ServiceRequestPublicOfferController::class, 'accept'])->middleware('throttle:10,1')->name('service-requests.offers.accept');
Route::get('/zayavi-oferta', [ServiceRequestController::class, 'create'])->name('request.service');
Route::post('/zayavi-oferta', [ServiceRequestController::class, 'store'])->middleware('throttle:6,1')->name('request.service.store');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::middleware(['auth'])->group(function () {
    Route::post('/favorites/{profile}', [FavoriteController::class, 'store'])->middleware('throttle:30,1')->name('favorites.store');
    Route::delete('/favorites/{profile}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->middleware('throttle:10,1')->name('services.store');

    Route::redirect('/dashboard/business/profile', '/business/profile/edit')->name('dashboard.business.profile.edit');
    Route::get('/business/profile/edit', [BusinessProfileController::class, 'edit'])->name('business.profile.edit');
    Route::put('/business/profile/update', [BusinessProfileController::class, 'update'])->middleware('throttle:20,1')->name('business.profile.update');
    Route::post('/business/profile/photos', [BusinessPhotoController::class, 'store'])->middleware('throttle:20,1')->name('business.profile.photos.store');
    Route::delete('/business/profile/photos/{businessPhoto}', [BusinessPhotoController::class, 'destroy'])->name('business.profile.photos.destroy');
    Route::get('/business/service-requests', [BusinessServiceRequestController::class, 'index'])->name('business.service-requests.index');
    Route::patch('/business/service-requests/{serviceRequest}/contacted', [BusinessServiceRequestController::class, 'contacted'])->name('business.service-requests.contacted');
    Route::patch('/business/service-requests/{serviceRequest}/completed', [BusinessServiceRequestController::class, 'completed'])->name('business.service-requests.completed');
    Route::patch('/business/service-requests/{serviceRequest}/cancelled', [BusinessServiceRequestController::class, 'cancelled'])->name('business.service-requests.cancelled');
    Route::post('/business/service-requests/{serviceRequest}/offers', [ServiceRequestOfferController::class, 'store'])->middleware('throttle:10,1')->name('business.service-requests.offers.store');
    Route::get('/business/insights', [BusinessInsightsController::class, 'index'])->name('business.insights.index');
    Route::post('/business/insights', [BusinessInsightsController::class, 'store'])->middleware('throttle:10,1')->name('business.insights.store');
    Route::get('/business/jobs', [FreelancerJobController::class, 'businessIndex'])->name('business.jobs.index');
    Route::get('/business/jobs/create', [FreelancerJobController::class, 'create'])->name('business.jobs.create');
    Route::post('/business/jobs', [FreelancerJobController::class, 'store'])->middleware('throttle:10,1')->name('business.jobs.store');
    Route::patch('/business/jobs/applications/{application}/select', [FreelancerJobController::class, 'selectApplication'])->middleware('throttle:20,1')->name('business.jobs.applications.select');
    Route::get('/business/billing', [BillingController::class, 'show'])->name('business.billing');
    Route::post('/business/billing/checkout', [BillingController::class, 'checkout'])->middleware('throttle:10,1')->name('business.billing.checkout');
    Route::post('/business/billing/portal', [BillingController::class, 'portal'])->middleware('throttle:10,1')->name('business.billing.portal');
    Route::post('/business/billing/upgrade-premium', [BillingController::class, 'upgradePremium'])->middleware('throttle:10,1')->name('business.billing.upgrade-premium');

    Route::get('/freelancer/credits', [FreelancerCreditController::class, 'index'])->name('freelancer.credits.index');
    Route::post('/freelancer/credits/purchase', [FreelancerCreditController::class, 'purchase'])->middleware('throttle:10,1')->name('freelancer.credits.purchase');
    Route::get('/freelancer/jobs', [FreelancerJobController::class, 'index'])->name('freelancer.jobs.index');
    Route::get('/freelancer/jobs/{freelancerJob}', [FreelancerJobController::class, 'show'])->name('freelancer.jobs.show');
    Route::post('/freelancer/jobs/{freelancerJob}/apply', [FreelancerJobController::class, 'apply'])->middleware('throttle:10,1')->name('freelancer.jobs.apply');
    Route::redirect('/dashboard/freelancer/profile', '/freelancer/profile/edit')->name('dashboard.freelancer.profile.edit');
    Route::get('/freelancer/profile/edit', [FreelancerProfileController::class, 'edit'])->name('freelancer.profile.edit');
    Route::put('/freelancer/profile', [FreelancerProfileController::class, 'update'])->middleware('throttle:20,1')->name('freelancer.profile.update');
    Route::post('/freelancer/portfolio', [FreelancerPortfolioController::class, 'store'])->middleware('throttle:10,1')->name('freelancer.portfolio.store');
    Route::delete('/freelancer/portfolio/{portfolioItem}', [FreelancerPortfolioController::class, 'destroy'])->name('freelancer.portfolio.destroy');

    Route::get('/dashboard/client/profile', [ClientProfileController::class, 'edit'])->name('dashboard.client.profile.edit');
    Route::put('/dashboard/client/profile', [ClientProfileController::class, 'update'])->middleware('throttle:20,1')->name('dashboard.client.profile.update');

    Route::patch('/admin/businesses/{user}/activate-30-days', [AdminBusinessController::class, 'activate'])->name('admin.businesses.activate');
    Route::patch('/admin/businesses/{user}/extend-trial', [AdminBusinessController::class, 'extendTrial'])->name('admin.businesses.extend-trial');
    Route::patch('/admin/businesses/{user}/expire', [AdminBusinessController::class, 'expire'])->name('admin.businesses.expire');
    Route::patch('/admin/businesses/{user}/cancel', [AdminBusinessController::class, 'cancel'])->name('admin.businesses.cancel');
    Route::patch('/admin/businesses/{user}/verify', [AdminBusinessController::class, 'verify'])->name('admin.businesses.verify');
    Route::patch('/admin/businesses/{user}/unverify', [AdminBusinessController::class, 'unverify'])->name('admin.businesses.unverify');
    Route::get('/admin/service-requests', [AdminServiceRequestController::class, 'index'])->name('admin.service-requests.index');
    Route::get('/admin/service-requests/{serviceRequest}', [AdminServiceRequestController::class, 'show'])->name('admin.service-requests.show');
    Route::patch('/admin/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::patch('/admin/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::patch('/admin/service-requests/{serviceRequest}/contacted', [AdminServiceRequestController::class, 'markContacted'])->name('admin.service-requests.contacted');
    Route::patch('/admin/service-requests/{serviceRequest}/closed', [AdminServiceRequestController::class, 'markClosed'])->name('admin.service-requests.closed');
    Route::patch('/admin/service-requests/{serviceRequest}/completed', [AdminServiceRequestController::class, 'markCompleted'])->name('admin.service-requests.completed');
    Route::patch('/admin/service-requests/{serviceRequest}/cancelled', [AdminServiceRequestController::class, 'markCancelled'])->name('admin.service-requests.cancelled');
    Route::get('/admin/freelancer-credits', [AdminFreelancerCreditController::class, 'index'])->name('admin.freelancer-credits.index');
    Route::post('/admin/freelancer-credits/{user}/adjust', [AdminFreelancerCreditController::class, 'adjust'])->middleware('throttle:20,1')->name('admin.freelancer-credits.adjust');
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
Route::post('/businesses/{user}/service-requests', [BusinessServiceRequestController::class, 'store'])->middleware('throttle:6,1')->name('businesses.service-requests.store');
Route::post('/businesses/{user}/reviews', [ReviewController::class, 'store'])->middleware('throttle:10,1')->name('businesses.reviews.store');
Route::post('/businesses/{user}/recommend', [BusinessRecommendationController::class, 'store'])->middleware('throttle:20,1')->name('businesses.recommendations.store');
Route::get('/businesses/{user}', [BusinessController::class, 'show'])->name('businesses.show');

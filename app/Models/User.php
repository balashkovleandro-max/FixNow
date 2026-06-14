<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use App\Support\CategoryCatalog;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_type',
        'profile_type',
        'is_suspended',
        'business_name',
        'business_category',
        'city',
        'address',
        'website',
        'working_hours',
        'phone',
        'avatar',
        'phone_verified_at',
        'last_active_at',
        'short_description',
        'description',
        'facebook',
        'instagram',
        'whatsapp',
        'viber',
        'payment_methods',
        'years_experience',
        'hourly_rate',
        'project_rate',
        'availability',
        'work_mode',
        'languages',
        'preferred_categories',
        'linkedin',
        'github',
        'behance',
        'emergency_services',
        'works_24_7',
        'booking_enabled',
        'response_time_label',
        'service_areas',
        'service_categories',
        'subscription_status',
        'trial_started_at',
        'trial_ends_at',
        'subscription_started_at',
        'subscription_ends_at',
        'stripe_customer_id',
        'stripe_subscription_id',
        'cancelled_at',
        'is_verified',
        'verified_at',
        'subscription_plan',
        'service_cities',
        'extra_city_addon_count',
        'offer_points_balance',
        'offer_points_initialized_at',
        'freelancer_credits_balance',
        'freelancer_monthly_credits_granted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'trial_started_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'subscription_started_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'service_cities' => 'array',
            'service_categories' => 'array',
            'extra_city_addon_count' => 'integer',
            'offer_points_balance' => 'integer',
            'offer_points_initialized_at' => 'datetime',
            'freelancer_credits_balance' => 'integer',
            'freelancer_monthly_credits_granted_at' => 'datetime',
            'languages' => 'array',
            'preferred_categories' => 'array',
            'emergency_services' => 'boolean',
            'works_24_7' => 'boolean',
            'booking_enabled' => 'boolean',
        ];
    }

    public function services()
    {
        return $this->hasMany(\App\Models\Service::class);
    }

    public function businessPhotos()
    {
        return $this->hasMany(\App\Models\BusinessPhoto::class, 'business_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function analyticsEvents()
    {
        return $this->hasMany(\App\Models\BusinessAnalyticsEvent::class, 'business_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'business_id');
    }

    public function recommendations()
    {
        return $this->hasMany(\App\Models\BusinessRecommendation::class, 'business_id');
    }

    public function serviceRequestAssignments()
    {
        return $this->hasMany(\App\Models\ServiceRequestAssignment::class, 'business_id');
    }

    public function serviceRequestOffers()
    {
        return $this->hasMany(\App\Models\ServiceRequestOffer::class, 'business_id');
    }

    public function financialReports()
    {
        return $this->hasMany(\App\Models\BusinessFinancialReport::class, 'business_id');
    }

    public function freelancerJobs()
    {
        return $this->hasMany(\App\Models\FreelancerJob::class, 'business_id');
    }

    public function freelancerJobApplications()
    {
        return $this->hasMany(\App\Models\FreelancerJobApplication::class, 'freelancer_id');
    }

    public function freelancerPortfolioItems()
    {
        return $this->hasMany(\App\Models\FreelancerPortfolioItem::class, 'freelancer_id')
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function freelancerCreditTransactions()
    {
        return $this->hasMany(\App\Models\FreelancerCreditTransaction::class);
    }

    public function favorites()
    {
        return $this->hasMany(\App\Models\UserFavorite::class);
    }

    public function favoriteProfiles()
    {
        return $this->belongsToMany(
            self::class,
            'user_favorites',
            'user_id',
            'favorite_user_id'
        )->withPivot('favorite_type')->withTimestamps();
    }

    public function isFavorite(User $profile): bool
    {
        if (!Schema::hasTable('user_favorites')) {
            return false;
        }

        return $this->favorites()
            ->where('favorite_user_id', $profile->id)
            ->exists();
    }

    public function favoritesCount(): int
    {
        return Schema::hasTable('user_favorites') ? $this->favorites()->count() : 0;
    }

    public function customerServiceRequests()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class, 'customer_id');
    }

    public function approvedReviews()
    {
        return $this->reviews()
            ->where('status', \App\Models\Review::STATUS_APPROVED)
            ->latest('approved_at');
    }

    public function scopePubliclyVisible($query)
    {
        $query->where('role', 'business');

        if (Schema::hasColumn('users', 'is_suspended')) {
            $query->where(function ($query) {
                $query->where('is_suspended', false)->orWhereNull('is_suspended');
            });
        }

        return $query->where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query
                            ->where('subscription_status', 'trial')
                            ->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '>=', now());
                    })
                    ->orWhere(function ($query) {
                        $query
                            ->whereIn('subscription_status', ['active', 'trialing'])
                            ->where(function ($query) {
                                $query
                                    ->whereNull('subscription_ends_at')
                                    ->orWhere('subscription_ends_at', '>=', now());
                            });
                    });
            });
    }

    public function scopePremiumFirst($query)
    {
        return $query->publicRanked();
    }

    public function scopePublicRanked($query)
    {
        if (Schema::hasColumn('users', 'subscription_plan')) {
            $query->orderByRaw(
                "CASE
                    WHEN subscription_plan = 'premium'
                        AND subscription_status IN ('active', 'trialing')
                        AND (subscription_ends_at IS NULL OR subscription_ends_at >= ?)
                    THEN 0
                    ELSE 1
                END",
                [now()->toDateTimeString()]
            );
        }

        if (Schema::hasColumn('users', 'is_verified')) {
            $query->orderByRaw("CASE WHEN is_verified IS TRUE THEN 0 ELSE 1 END");
        }

        return $query->orderByRaw(
            "CASE
                WHEN subscription_status IN ('active', 'trialing')
                    AND (subscription_ends_at IS NULL OR subscription_ends_at >= ?)
                    THEN 0
                WHEN subscription_status = 'trial'
                    AND trial_ends_at IS NOT NULL
                    AND trial_ends_at >= ?
                    THEN 1
                ELSE 2
            END",
            [
                now()->toDateTimeString(),
                now()->toDateTimeString(),
            ]
        );
    }

    public function isBusiness(): bool
    {
        return $this->role === 'business'
            || $this->account_type === 'business'
            || $this->profile_type === 'business';
    }

    public function isCustomer(): bool
    {
        return in_array($this->role, ['customer', 'client'], true)
            || in_array($this->account_type, ['customer', 'client'], true);
    }

    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer'
            || $this->account_type === 'freelancer'
            || $this->profile_type === 'freelancer';
    }

    public function accountType(): string
    {
        return $this->account_type ?: ($this->role === 'customer' ? 'client' : $this->role);
    }

    public function profileType(): ?string
    {
        return $this->profile_type ?: (in_array($this->role, ['business', 'freelancer'], true) ? $this->role : null);
    }

    public function freelancerCreditsBalance(): int
    {
        return max(0, (int) ($this->freelancer_credits_balance ?? 0));
    }

    public function trustSummary(): array
    {
        return \App\Support\ProfileTrust::summary($this);
    }

    public function trustScore(): int
    {
        return (int) ($this->trust_summary['trust_score'] ?? $this->trustSummary()['trust_score']);
    }

    public function trustBadges(): array
    {
        return $this->trust_summary['badges'] ?? $this->trustSummary()['badges'];
    }

    public function planKey(): string
    {
        return in_array($this->subscription_plan, ['standard', 'premium'], true)
            ? $this->subscription_plan
            : 'standard';
    }

    public function planLabel(): string
    {
        return $this->planKey() === 'premium' ? 'Premium' : 'Standard';
    }

    public function effectivePlanKey(): string
    {
        return $this->planKey() === 'premium' && $this->hasActiveSubscription()
            ? 'premium'
            : 'standard';
    }

    public function effectivePlanLabel(): string
    {
        return $this->effectivePlanKey() === 'premium' ? 'Premium' : 'Standard';
    }

    public function isPremium(): bool
    {
        return $this->effectivePlanKey() === 'premium';
    }

    public function isStandard(): bool
    {
        return !$this->isPremium();
    }

    public function hasPremiumBenefits(): bool
    {
        return $this->isPremium();
    }

    public function publicRankingScore(): int
    {
        $score = 0;

        if (!$this->isPremium()) {
            $score += 100;
        }

        if (!$this->is_verified) {
            $score += 10;
        }

        if (!$this->isSubscriptionActive()) {
            $score += 1;
        }

        $score += (int) ceil(max(0, 100 - ($this->profileCompleteness()['percent'] ?? 0)) / 20);

        return $score;
    }

    public function includedCityLimit(): int
    {
        return $this->effectivePlanKey() === 'premium' ? 5 : 2;
    }

    public function cityLimit(): int
    {
        return $this->includedCityLimit();
    }

    public function includedServiceLimit(): int
    {
        return $this->effectivePlanKey() === 'premium' ? 5 : 2;
    }

    public function categoryLimit(): int
    {
        return $this->includedServiceLimit();
    }

    public function photoLimit(): int
    {
        return $this->effectivePlanKey() === 'premium' ? 15 : 5;
    }

    public function includedMonthlyOfferPoints(): int
    {
        if ($this->isTrialActive()) {
            return 45;
        }

        if (!$this->hasActiveSubscription()) {
            return 0;
        }

        return $this->isPremium() ? 90 : 30;
    }

    public function offerPointsBalance(): int
    {
        if (!Schema::hasColumn('users', 'offer_points_balance')) {
            return $this->includedMonthlyOfferPoints();
        }

        return (int) ($this->offer_points_balance ?? $this->includedMonthlyOfferPoints());
    }

    public function ensureOfferPointsInitialized(): void
    {
        if (!$this->isBusiness() || !Schema::hasColumn('users', 'offer_points_balance')) {
            return;
        }

        if ($this->offer_points_balance !== null) {
            return;
        }

        $payload = [
            'offer_points_balance' => $this->includedMonthlyOfferPoints(),
        ];

        if (Schema::hasColumn('users', 'offer_points_initialized_at')) {
            $payload['offer_points_initialized_at'] = now();
        }

        $this->forceFill($payload)->save();
        $this->refresh();
    }

    public function canSpendOfferPoints(int $points = \App\Models\ServiceRequestOffer::POINTS_COST): bool
    {
        return $this->offerPointsBalance() >= $points;
    }

    public function spendOfferPoints(int $points = \App\Models\ServiceRequestOffer::POINTS_COST): void
    {
        if (!Schema::hasColumn('users', 'offer_points_balance')) {
            return;
        }

        $this->ensureOfferPointsInitialized();
        $this->forceFill([
            'offer_points_balance' => max(0, $this->offerPointsBalance() - $points),
        ])->save();
    }

    public function remainingOfferCount(): int
    {
        return (int) floor($this->offerPointsBalance() / \App\Models\ServiceRequestOffer::POINTS_COST);
    }

    public function hasRequestBasedCategories(): bool
    {
        return CategoryCatalog::businessHasRequestBasedCategories($this);
    }

    public function serviceCities(): array
    {
        $cities = $this->service_cities;

        if (is_string($cities)) {
            $decoded = json_decode($cities, true);
            $cities = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($cities)) {
            $cities = [];
        }

        $cities = collect($cities)
            ->map(fn ($city) => trim((string) $city))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($cities) && $this->city) {
            return [$this->city];
        }

        return $cities;
    }

    public function serviceCityCount(): int
    {
        return count($this->citiesUsedForPlan());
    }

    public function citiesUsedForPlan(): array
    {
        $cities = collect($this->serviceCities());

        if ($this->relationLoaded('services')) {
            $serviceCities = $this->services->pluck('city');
        } else {
            $serviceCities = $this->services()
                ->whereNotNull('city')
                ->pluck('city');
        }

        return $cities
            ->merge($serviceCities)
            ->map(fn ($city) => trim((string) $city))
            ->filter()
            ->unique(fn ($city) => mb_strtolower($city))
            ->values()
            ->all();
    }

    public function serviceCategories(): array
    {
        $categories = $this->service_categories;

        if (is_string($categories)) {
            $decoded = json_decode($categories, true);
            $categories = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($categories)) {
            $categories = [];
        }

        $categories = collect($categories)
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($categories) && $this->business_category) {
            return [$this->business_category];
        }

        return $categories;
    }

    public function serviceCategoryCount(): int
    {
        return count($this->categoriesUsedForPlan());
    }

    public function categoriesUsedForPlan(): array
    {
        $categories = collect($this->serviceCategories());

        if ($this->relationLoaded('services')) {
            $serviceCategories = $this->services->pluck('category');
        } else {
            $serviceCategories = $this->services()
                ->whereNotNull('category')
                ->pluck('category');
        }

        return $categories
            ->merge($serviceCategories)
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique(fn ($category) => mb_strtolower($category))
            ->values()
            ->all();
    }

    public function photoCount(): int
    {
        return $this->businessPhotoCount();
    }

    public function businessPhotoCount(): int
    {
        if (!Schema::hasTable('business_photos')) {
            return 0;
        }

        if ($this->relationLoaded('businessPhotos')) {
            return $this->businessPhotos->count();
        }

        return $this->businessPhotos()->count();
    }

    public function profileCompleteness(): array
    {
        $items = [
            [
                'key' => 'business-name',
                'label' => 'Име на бизнес',
                'weight' => 15,
                'complete' => filled($this->business_name),
            ],
            [
                'key' => 'phone',
                'label' => 'Телефон',
                'weight' => 15,
                'complete' => filled($this->phone),
            ],
            [
                'key' => 'city',
                'label' => 'Град',
                'weight' => 15,
                'complete' => $this->serviceCityCount() > 0,
            ],
            [
                'key' => 'category',
                'label' => 'Категория/услуга',
                'weight' => 15,
                'complete' => $this->serviceCategoryCount() > 0,
            ],
            [
                'key' => 'description',
                'label' => 'Описание',
                'weight' => 20,
                'complete' => filled($this->short_description) || filled($this->description),
            ],
            [
                'key' => 'photo',
                'label' => 'Снимка',
                'weight' => 20,
                'complete' => $this->photoCount() > 0,
            ],
        ];

        $items = collect($items);
        $completed = $items->filter(fn ($item) => (bool) $item['complete'])->count();
        $total = $items->count();
        $percent = (int) min(100, $items
            ->filter(fn ($item) => (bool) $item['complete'])
            ->sum('weight'));

        return [
            'percent' => $percent,
            'completed' => $completed,
            'total' => $total,
            'items' => $items->values()->all(),
            'missing' => $items
                ->filter(fn ($item) => !$item['complete'])
                ->pluck('label')
                ->values()
                ->all(),
        ];
    }

    public function approvedReviewsCount(): int
    {
        if (!Schema::hasTable('reviews')) {
            return 0;
        }

        if ($this->relationLoaded('reviews')) {
            return $this->reviews
                ->where('status', \App\Models\Review::STATUS_APPROVED)
                ->count();
        }

        return $this->approvedReviews()->count();
    }

    public function averageRating(): ?float
    {
        if (!Schema::hasTable('reviews')) {
            return null;
        }

        $average = $this->approvedReviews()->avg('rating');

        return $average !== null ? round((float) $average, 1) : null;
    }

    public function recommendationsCount(): int
    {
        if (!Schema::hasTable('business_recommendations')) {
            return 0;
        }

        if ($this->relationLoaded('recommendations')) {
            return $this->recommendations->count();
        }

        return $this->recommendations()->count();
    }

    public function hasEmergencyServices(): bool
    {
        return (bool) ($this->emergency_services ?: data_get($this, 'спешни_услуги'));
    }

    public function worksAroundClock(): bool
    {
        return (bool) $this->works_24_7;
    }

    public function responseTimeLabel(): ?string
    {
        return $this->response_time_label ?: null;
    }

    public function publicBadges(): array
    {
        return array_values(array_filter([
            $this->isPremium() ? 'Препоръчан' : null,
            $this->is_verified ? 'Потвърден' : null,
            $this->hasEmergencyServices() ? 'Спешни услуги' : null,
            $this->worksAroundClock() ? '24/7' : null,
            $this->responseTimeLabel(),
        ]));
    }

    public function extraCitiesUsed(): int
    {
        if (!$this->isPremium()) {
            return 0;
        }

        return max(0, $this->serviceCityCount() - $this->includedCityLimit());
    }

    public function extraCitiesMonthlyAmount(): float
    {
        return 0.0;
    }

    public function planMonthlyAmount(): float
    {
        return $this->planKey() === 'premium' ? 24.99 : 18.99;
    }

    public function estimatedMonthlyAmount(): float
    {
        return $this->planMonthlyAmount();
    }

    public function isTrialActive(): bool
    {
        return $this->isBusiness()
            && $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->greaterThanOrEqualTo(now());
    }

    public function isSubscriptionActive(): bool
    {
        return $this->isBusiness()
            && in_array($this->subscription_status, ['active', 'trialing'], true)
            && (
                $this->subscription_ends_at === null
                || $this->subscription_ends_at->greaterThanOrEqualTo(now())
            );
    }

    public function hasActiveSubscription(): bool
    {
        return $this->isSubscriptionActive();
    }

    public function hasPaymentIssue(): bool
    {
        return $this->isBusiness()
            && in_array($this->subscription_status, [
                'past_due',
                'unpaid',
                'incomplete',
                'incomplete_expired',
                'payment_failed',
            ], true);
    }

    public function isExpired(): bool
    {
        return $this->isBusiness()
            && $this->effectiveSubscriptionStatus() === 'expired';
    }

    public function isPubliclyVisible(): bool
    {
        if (Schema::hasColumn('users', 'is_suspended') && $this->is_suspended) {
            return false;
        }

        return $this->isBusiness()
            && ($this->isTrialActive() || $this->isSubscriptionActive());
    }

    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at || !$this->isTrialActive()) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInDays($this->trial_ends_at, false)));
    }

    public function effectiveSubscriptionStatus(): string
    {
        if (!$this->isBusiness()) {
            return $this->subscription_status ?: 'inactive';
        }

        if (in_array($this->subscription_status, ['cancelled', 'canceled'], true)) {
            return 'cancelled';
        }

        if (in_array($this->subscription_status, [
            'past_due',
            'unpaid',
            'incomplete',
            'incomplete_expired',
            'payment_failed',
        ], true)) {
            return $this->subscription_status;
        }

        if ($this->isSubscriptionActive()) {
            return $this->subscription_status === 'trialing' ? 'trialing' : 'active';
        }

        if ($this->isTrialActive()) {
            return 'trial';
        }

        return 'expired';
    }

    public function initializeTrialIfMissing(): void
    {
        if (!$this->isBusiness()) {
            return;
        }

        if (
            $this->trial_started_at
            || $this->trial_ends_at
            || in_array($this->subscription_status, [
                'active',
                'trialing',
                'expired',
                'cancelled',
                'canceled',
                'past_due',
                'unpaid',
                'incomplete',
                'incomplete_expired',
                'payment_failed',
            ], true)
        ) {
            return;
        }

        $startedAt = now();

        $this->forceFill([
            'subscription_status' => 'trial',
            'trial_started_at' => $startedAt,
            'trial_ends_at' => $startedAt->copy()->addDays(30),
        ])->save();
    }
}

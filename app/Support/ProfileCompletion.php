<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ProfileCompletion
{
    public static function summary(User $profile): array
    {
        $hasProfileImage = filled($profile->avatar) || self::businessPhotoCount($profile) > 0;
        $hasDescription = filled($profile->short_description) || filled($profile->description);
        $hasCategories = self::serviceCategoryCount($profile) > 0 || filled($profile->business_category);
        $hasCity = self::serviceCityCount($profile) > 0 || filled($profile->city);
        $hasPortfolio = $profile->isFreelancer() && self::portfolioCount($profile) > 0;

        $items = $profile->isFreelancer()
            ? [
                ['key' => 'name', 'label' => 'Име', 'weight' => 10, 'complete' => filled($profile->name)],
                ['key' => 'email', 'label' => 'Имейл', 'weight' => 10, 'complete' => filled($profile->email)],
                ['key' => 'phone', 'label' => 'Телефон', 'weight' => 12, 'complete' => filled($profile->phone)],
                ['key' => 'city', 'label' => 'Град', 'weight' => 12, 'complete' => $hasCity],
                ['key' => 'category', 'label' => 'Категория/услуги', 'weight' => 15, 'complete' => $hasCategories],
                ['key' => 'description', 'label' => 'Описание', 'weight' => 20, 'complete' => $hasDescription],
                ['key' => 'portfolio', 'label' => 'Портфолио', 'weight' => 21, 'complete' => $hasPortfolio],
            ]
            : [
                ['key' => 'business-name', 'label' => 'Име на бизнес', 'weight' => 12, 'complete' => filled($profile->business_name) || filled($profile->name)],
                ['key' => 'phone', 'label' => 'Телефон', 'weight' => 12, 'complete' => filled($profile->phone)],
                ['key' => 'email', 'label' => 'Имейл', 'weight' => 8, 'complete' => filled($profile->email)],
                ['key' => 'city', 'label' => 'Град', 'weight' => 12, 'complete' => $hasCity],
                ['key' => 'category', 'label' => 'Категория/услуги', 'weight' => 14, 'complete' => $hasCategories],
                ['key' => 'description', 'label' => 'Описание', 'weight' => 18, 'complete' => $hasDescription],
                ['key' => 'photo', 'label' => 'Снимки/лого', 'weight' => 14, 'complete' => $hasProfileImage],
                ['key' => 'working-hours', 'label' => 'Работно време', 'weight' => 10, 'complete' => filled($profile->working_hours)],
            ];

        $items = collect($items);
        $completed = $items->filter(fn ($item) => (bool) $item['complete'])->count();
        $percent = (int) min(100, $items
            ->filter(fn ($item) => (bool) $item['complete'])
            ->sum('weight'));

        return [
            'percent' => $percent,
            'completed' => $completed,
            'total' => $items->count(),
            'items' => $items->values()->all(),
            'missing' => $items
                ->filter(fn ($item) => !$item['complete'])
                ->pluck('label')
                ->values()
                ->all(),
        ];
    }

    private static function serviceCityCount(User $profile): int
    {
        return method_exists($profile, 'serviceCityCount') ? $profile->serviceCityCount() : (filled($profile->city) ? 1 : 0);
    }

    private static function serviceCategoryCount(User $profile): int
    {
        return method_exists($profile, 'serviceCategoryCount') ? $profile->serviceCategoryCount() : (filled($profile->business_category) ? 1 : 0);
    }

    private static function businessPhotoCount(User $profile): int
    {
        return method_exists($profile, 'photoCount') ? $profile->photoCount() : 0;
    }

    private static function portfolioCount(User $profile): int
    {
        if (!Schema::hasTable('freelancer_portfolio_items')) {
            return 0;
        }

        return $profile->relationLoaded('freelancerPortfolioItems')
            ? $profile->freelancerPortfolioItems->count()
            : $profile->freelancerPortfolioItems()->count();
    }
}

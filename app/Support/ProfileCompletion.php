<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class ProfileCompletion
{
    public static function summary(User $profile): array
    {
        $type = $profile->isFreelancer()
            ? 'freelancer'
            : ($profile->isBusiness() ? 'business' : 'client');

        $items = match ($type) {
            'freelancer' => self::freelancerItems($profile),
            'business' => self::businessItems($profile),
            default => self::clientItems($profile),
        };

        $items = collect($items);
        $completed = $items->filter(fn ($item) => (bool) $item['complete'])->count();
        $percent = (int) min(100, $items
            ->filter(fn ($item) => (bool) $item['complete'])
            ->sum('weight'));

        return [
            'type' => $type,
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

    private static function businessItems(User $profile): array
    {
        $hasProfileImage = filled($profile->avatar) || self::businessPhotoCount($profile) > 0;
        $hasDescription = filled($profile->short_description) || filled($profile->description);
        $hasCategories = self::serviceCategoryCount($profile) > 0 || filled($profile->business_category);
        $hasCity = self::serviceCityCount($profile) > 0 || filled($profile->city);
        $hasServices = self::serviceCount($profile) > 0 || self::serviceCategoryCount($profile) > 0;

        return [
            self::item('business-name', 'Име на бизнеса', 10, filled($profile->business_name) || filled($profile->name), self::url('business.profile.edit', '/business/profile/edit', 'identity')),
            self::item('phone', 'Телефон', 10, filled($profile->phone), self::url('business.profile.edit', '/business/profile/edit', 'contact')),
            self::item('city', 'Град', 10, $hasCity, self::url('business.profile.edit', '/business/profile/edit', 'cities')),
            self::item('category', 'Категория', 12, $hasCategories, self::url('business.profile.edit', '/business/profile/edit', 'categories')),
            self::item('description', 'Описание', 14, $hasDescription, self::url('business.profile.edit', '/business/profile/edit', 'description')),
            self::item('photo', 'Лого/снимка', 12, $hasProfileImage, self::url('business.profile.edit', '/business/profile/edit', 'gallery')),
            self::item('services', 'Услуги', 12, $hasServices, self::url('services.create', '/services/create')),
            self::item('working-hours', 'Работно време', 10, filled($profile->working_hours), self::url('business.profile.edit', '/business/profile/edit', 'hours')),
            self::item('gallery', 'Галерия', 10, self::businessPhotoCount($profile) > 0, self::url('business.profile.edit', '/business/profile/edit', 'gallery')),
        ];
    }

    private static function freelancerItems(User $profile): array
    {
        $hasDescription = filled($profile->short_description) || filled($profile->description);
        $hasCategories = self::serviceCategoryCount($profile) > 0 || filled($profile->business_category);
        $hasRate = filled($profile->hourly_rate) || filled($profile->project_rate);
        $profileUrl = self::url('freelancer.profile.edit', '/freelancer/profile/edit');

        return [
            self::item('name', 'Име', 8, filled($profile->name), $profileUrl.'#identity'),
            self::item('headline', 'Headline', 10, filled($profile->business_category), $profileUrl.'#identity'),
            self::item('phone', 'Телефон', 8, filled($profile->phone), $profileUrl.'#contact'),
            self::item('skills', 'Умения', 12, self::serviceCategoryCount($profile) > 0, $profileUrl.'#skills'),
            self::item('category', 'Категория', 10, $hasCategories, $profileUrl.'#skills'),
            self::item('bio', 'Bio', 12, $hasDescription, $profileUrl.'#bio'),
            self::item('rate', 'Цена/ставка', 10, $hasRate, $profileUrl.'#rates'),
            self::item('portfolio', 'Портфолио', 12, self::portfolioCount($profile) > 0, self::url('dashboard', '/dashboard', 'portfolio')),
            self::item('availability', 'Наличност', 10, filled($profile->availability), $profileUrl.'#availability'),
            self::item('avatar', 'Профилна снимка', 8, filled($profile->avatar), $profileUrl.'#identity'),
        ];
    }

    private static function clientItems(User $profile): array
    {
        $profileUrl = self::url('dashboard.client.profile.edit', '/dashboard/client/profile');

        return [
            self::item('name', 'Име', 22, filled($profile->name), $profileUrl.'#identity'),
            self::item('phone', 'Телефон', 20, filled($profile->phone), $profileUrl.'#contact'),
            self::item('city', 'Град', 18, filled($profile->city), $profileUrl.'#location'),
            self::item('email', 'Имейл', 20, filled($profile->email), $profileUrl.'#contact'),
            self::item('preferred-categories', 'Предпочитани категории', 20, !empty($profile->preferred_categories ?? []), $profileUrl.'#preferences'),
        ];
    }

    private static function item(string $key, string $label, int $weight, bool $complete, string $href): array
    {
        return compact('key', 'label', 'weight', 'complete', 'href');
    }

    private static function url(string $routeName, string $fallback, ?string $fragment = null): string
    {
        $url = Route::has($routeName) ? route($routeName) : url($fallback);

        return $fragment ? $url.'#'.$fragment : $url;
    }

    private static function serviceCityCount(User $profile): int
    {
        return method_exists($profile, 'serviceCityCount') ? $profile->serviceCityCount() : (filled($profile->city) ? 1 : 0);
    }

    private static function serviceCategoryCount(User $profile): int
    {
        return method_exists($profile, 'serviceCategoryCount') ? $profile->serviceCategoryCount() : (filled($profile->business_category) ? 1 : 0);
    }

    private static function serviceCount(User $profile): int
    {
        if (!Schema::hasTable('services')) {
            return 0;
        }

        return $profile->relationLoaded('services')
            ? $profile->services->count()
            : $profile->services()->count();
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

<?php

namespace App\Support;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategoryCatalog
{
    public const TYPE_REQUEST_BASED = 'request_based';
    public const TYPE_DIRECTORY_BASED = 'directory_based';

    public static function all(): Collection
    {
        if (Schema::hasTable('service_categories')) {
            $categories = ServiceCategory::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (ServiceCategory $category) => [
                    'name' => $category->name,
                    'slug' => $category->slug ?: self::slug($category->name),
                    'group' => $category->group,
                    'type' => $category->isRequestBased() ? self::TYPE_REQUEST_BASED : self::TYPE_DIRECTORY_BASED,
                    'accepts_requests' => $category->isRequestBased(),
                ]);

            if ($categories->isNotEmpty()) {
                return $categories
                    ->reject(fn (array $category) => self::isHiddenCategory($category['name'] ?? null))
                    ->values();
            }
        }

        return self::configDefinitions();
    }

    public static function grouped(): Collection
    {
        return self::all()->groupBy('group');
    }

    public static function homepageGroups(): Collection
    {
        $priority = [
            'Ремонти и строителство',
            'Спешни домашни услуги',
            'Поддръжка на домове и имоти',
            'Авто услуги',
            'Ремонт на техника и уреди',
            'Услуги за малки бизнеси',
        ];

        return self::grouped()
            ->only($priority)
            ->sortBy(fn ($items, $group) => array_search($group, $priority, true));
    }

    public static function requestBased(): Collection
    {
        return self::all()
            ->filter(fn ($category) => (bool) $category['accepts_requests'])
            ->values();
    }

    public static function directoryBased(): Collection
    {
        return self::all()
            ->filter(fn ($category) => !(bool) $category['accepts_requests'])
            ->values();
    }

    public static function acceptsRequests(?string $category): bool
    {
        $category = trim((string) $category);

        if ($category === '') {
            return false;
        }

        return self::requestBased()
            ->contains(fn ($definition) => self::matches((string) $definition['name'], $category));
    }

    public static function businessHasRequestBasedCategories(User $business): bool
    {
        return collect($business->serviceCategories())
            ->contains(fn ($category) => self::acceptsRequests((string) $category));
    }

    public static function businessHasHiddenCategory(User $business): bool
    {
        return collect($business->serviceCategories())
            ->push($business->business_category)
            ->filter()
            ->contains(fn ($category) => self::isHiddenCategory((string) $category));
    }

    public static function isHiddenCategory(?string $category): bool
    {
        $category = BusinessGrowthMetrics::normalizeSearchText((string) $category);

        if ($category === '') {
            return false;
        }

        return collect([
            'хотел',
            'хотели',
            'хотели и настаняване',
            'ресторант',
            'ресторанти',
            'ресторанти/кафенета',
            'кафене',
            'кафенета',
            'hotel',
            'hotels',
            'hoteli',
            'restaurant',
            'restaurants',
            'restoranti',
            'cafe',
            'cafes',
            'kafeneta',
        ])->contains(fn (string $hidden) => $category === $hidden || str_contains($category, $hidden));
    }

    public static function businessMatchesRequest(User $business, ?string $category, ?string $city): bool
    {
        if (!BusinessGrowthMetrics::matchesCity($business, $city)) {
            return false;
        }

        if (!self::acceptsRequests($category)) {
            return false;
        }

        return collect($business->serviceCategories())
            ->contains(fn ($businessCategory) => self::matches((string) $businessCategory, (string) $category));
    }

    public static function matches(string $candidate, string $search): bool
    {
        return BusinessGrowthMetrics::matchesCategory($candidate, $search)
            || BusinessGrowthMetrics::matchesCategory(self::normalizedAlias($candidate), self::normalizedAlias($search));
    }

    public static function slug(string $name): string
    {
        $map = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
            'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sht', 'ъ' => 'a',
            'ь' => 'y', 'ю' => 'yu', 'я' => 'ya',
        ];

        $value = strtr(mb_strtolower($name), $map);

        return Str::slug($value) ?: Str::slug($name);
    }

    private static function configDefinitions(): Collection
    {
        return collect(config('fixnow_categories.groups', []))
            ->flatMap(function (array $items, string $group) {
                return collect($items)->map(fn ($item, $index) => [
                    'name' => $item['name'],
                    'slug' => self::slug($item['name']),
                    'group' => $group,
                    'type' => $item['type'],
                    'accepts_requests' => $item['type'] === self::TYPE_REQUEST_BASED,
                    'sort_order' => $index,
                ]);
            })
            ->reject(fn (array $category) => self::isHiddenCategory($category['name'] ?? null))
            ->values();
    }

    private static function normalizedAlias(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim(mb_strtolower($value))) ?? '';

        $aliases = [
            'ремонти' => 'ремонти и строителство',
            'remonti' => 'ремонти и строителство',
            'repairs' => 'ремонти и строителство',
            'майстор' => 'ремонти и строителство',
            'майстори' => 'ремонти и строителство',
            'електро услуги' => 'електроуслуги',
            'electric' => 'електроуслуги',
            'електротехник' => 'електроуслуги',
            'вик' => 'вик услуги',
            'vik' => 'вик услуги',
            'plumbing' => 'вик услуги',
            'автосервиз' => 'автосервизи',
            'auto service' => 'автосервизи',
            'autoservice' => 'автосервизи',
            'cleaning' => 'почистване',
        ];

        return $aliases[$value] ?? $value;
    }
}

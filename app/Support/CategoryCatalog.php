<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryCatalog
{
    public const TYPE_REQUEST_BASED = 'request_based';
    public const TYPE_DIRECTORY_BASED = 'directory_based';

    public static function all(): Collection
    {
        return self::configDefinitions();
    }

    public static function names(): Collection
    {
        return self::all()
            ->pluck('name')
            ->filter()
            ->unique(fn ($name) => mb_strtolower((string) $name))
            ->values();
    }

    public static function grouped(): Collection
    {
        return self::all()->groupBy('group');
    }

    public static function homepageGroups(): Collection
    {
        return self::grouped();
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

    public static function displayName(?string $category): string
    {
        $category = trim((string) $category);

        if ($category === '') {
            return 'Друго';
        }

        $match = self::all()
            ->first(fn ($definition) => self::matches((string) $definition['name'], $category));

        return $match['name'] ?? 'Друго';
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

        return false;
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
        return collect(config('bon_categories.groups', []))
            ->flatMap(function (array $items, string $group) {
                return collect($items)->map(fn ($item, $index) => [
                    'name' => $item['name'],
                    'slug' => self::slug($item['name']),
                    'group' => $group,
                    'type' => $item['type'] ?? self::TYPE_REQUEST_BASED,
                    'accepts_requests' => ($item['type'] ?? self::TYPE_REQUEST_BASED) === self::TYPE_REQUEST_BASED,
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
            'ресторант' => 'ресторанти и кафенета',
            'ресторанти' => 'ресторанти и кафенета',
            'кафене' => 'ресторанти и кафенета',
            'кафенета' => 'ресторанти и кафенета',
            'cafe' => 'ресторанти и кафенета',
            'restaurant' => 'ресторанти и кафенета',
            'хотели' => 'хотели и настаняване',
            'хотел' => 'хотели и настаняване',
            'hotel' => 'хотели и настаняване',
            'къщи за гости' => 'хотели и настаняване',
            'апартаменти за настаняване' => 'хотели и настаняване',
            'красота' => 'красота и козметика',
            'красота и грижа' => 'красота и козметика',
            'красота и услуги' => 'красота и козметика',
            'салони за красота' => 'красота и козметика',
            'beauty' => 'красота и козметика',
            'спорт' => 'фитнес и спорт',
            'спорт и активности' => 'фитнес и спорт',
            'fitness' => 'фитнес и спорт',
            'ремонти' => 'ремонти и строителство',
            'ремонт' => 'ремонти и строителство',
            'remonti' => 'ремонти и строителство',
            'repairs' => 'ремонти и строителство',
            'repair specialist' => 'ремонти и строителство',
            'вик' => 'домашни услуги',
            'вик услуги' => 'домашни услуги',
            'plumbing' => 'домашни услуги',
            'електро услуги' => 'домашни услуги',
            'електроуслуги' => 'домашни услуги',
            'електротехник' => 'домашни услуги',
            'electric' => 'домашни услуги',
            'автосервиз' => 'автосервизи',
            'auto service' => 'автосервизи',
            'autoservice' => 'автосервизи',
            'cleaning' => 'почистване',
            'курсове' => 'образование и курсове',
            'education' => 'образование и курсове',
            'marketing' => 'маркетинг и реклама',
            'social media' => 'маркетинг и реклама',
            'copywriting' => 'маркетинг и реклама',
            'sales' => 'маркетинг и реклама',
            'web design' => 'уеб сайтове и софтуер',
            'development' => 'уеб сайтове и софтуер',
            'laravel' => 'уеб сайтове и софтуер',
            'wordpress' => 'уеб сайтове и софтуер',
            'ui/ux дизайн' => 'дизайн и брандинг',
            'ui/ux дизайнер' => 'дизайн и брандинг',
            'дизайн специалист' => 'дизайн и брандинг',
            'ui/ux design' => 'дизайн и брандинг',
            'branding' => 'дизайн и брандинг',
            'brand design' => 'дизайн и брандинг',
            'laravel developer' => 'уеб сайтове и софтуер',
            'marketing specialist' => 'маркетинг и реклама',
            'social media manager' => 'маркетинг и реклама',
            'copywriter' => 'маркетинг и реклама',
            'photographer' => 'събития и фотография',
            'видео монтажист' => 'събития и фотография',
            'finance' => 'счетоводство и финанси',
            'business operations' => 'бизнес консултации',
            'consulting' => 'бизнес консултации',
            'video editing' => 'събития и фотография',
            'photography' => 'събития и фотография',
            'фотографи' => 'събития и фотография',
            'събития и празници' => 'събития и фотография',
        ];

        return $aliases[$value] ?? $value;
    }
}

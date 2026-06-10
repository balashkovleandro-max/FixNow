<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Support\BusinessGrowthMetrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SeoPageController extends Controller
{
    public function city(string $city)
    {
        return $this->showSeoPage($city);
    }

    public function cityCategory(string $city, string $category)
    {
        return $this->showSeoPage($city, $category);
    }

    public function categoryCity(string $category, string $city)
    {
        return $this->showSeoPage($city, $category);
    }

    private function showSeoPage(string $citySlug, ?string $categorySlug = null)
    {
        $city = $this->cityLabel($citySlug);
        $category = $categorySlug ? $this->categoryData($categorySlug) : null;

        $allBusinesses = BusinessGrowthMetrics::publicBusinesses();
        $cityBusinesses = BusinessGrowthMetrics::filterByCity($allBusinesses, $city);
        $businesses = $category
            ? $this->filterByCategoryTerms($cityBusinesses, $category['terms'])
            : $cityBusinesses;

        $topBusinesses = $businesses->take(4)->values();
        $latestReviews = $this->latestReviews($businesses);
        $popularCities = $this->popularCities($allBusinesses);
        $popularCategories = BusinessGrowthMetrics::popularCategories($allBusinesses, 8);
        $quickCategoryLinks = $this->quickCategoryLinks($citySlug);
        $internalLinks = $this->internalLinks();
        $canonicalRoutes = $this->canonicalRoutes($citySlug, $categorySlug);

        $seoTitle = $this->seoTitle($city, $category);
        $h1 = $this->h1($city, $category);
        $intro = $this->intro($city, $category);

        return view('seo.location-category', compact(
            'city',
            'citySlug',
            'category',
            'categorySlug',
            'businesses',
            'topBusinesses',
            'latestReviews',
            'popularCities',
            'popularCategories',
            'quickCategoryLinks',
            'internalLinks',
            'canonicalRoutes',
            'seoTitle',
            'h1',
            'intro'
        ));
    }

    private function cityLabel(string $slug): string
    {
        return $this->cityMap()[$this->normalizeSlug($slug)] ?? $this->humanizeSlug($slug);
    }

    private function categoryData(string $slug): array
    {
        $slug = $this->normalizeSlug($slug);

        return $this->categoryMap()[$slug] ?? [
            'slug' => $slug,
            'label' => $this->humanizeSlug($slug),
            'terms' => [$this->humanizeSlug($slug)],
        ];
    }

    private function filterByCategoryTerms(Collection $businesses, array $terms): Collection
    {
        $terms = collect($terms)
            ->map(fn ($term) => mb_strtolower(trim((string) $term)))
            ->filter()
            ->values();

        if ($terms->isEmpty()) {
            return $businesses;
        }

        return $businesses
            ->filter(function (User $business) use ($terms) {
                $categories = collect($business->serviceCategories())
                    ->push($business->business_category)
                    ->map(fn ($category) => mb_strtolower(trim((string) $category)))
                    ->filter();

                return $categories->contains(function ($category) use ($terms) {
                    return $terms->contains(fn ($term) => str_contains($category, $term) || str_contains($term, $category));
                });
            })
            ->values();
    }

    private function latestReviews(Collection $businesses): Collection
    {
        if (!Schema::hasTable('reviews') || $businesses->isEmpty()) {
            return collect();
        }

        return Review::query()
            ->approved()
            ->whereIn('business_id', $businesses->pluck('id'))
            ->with('business')
            ->latest('approved_at')
            ->take(4)
            ->get();
    }

    private function popularCities(Collection $businesses): Collection
    {
        return $businesses
            ->flatMap(fn (User $business) => $business->serviceCities())
            ->map(fn ($city) => trim((string) $city))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(8)
            ->map(fn ($count, $city) => [
                'name' => $city,
                'slug' => $this->slugForCity($city),
                'count' => $count,
            ])
            ->values();
    }

    private function quickCategoryLinks(string $citySlug): array
    {
        return [
            ['label' => 'Автосервизи', 'url' => route('seo.city.category', ['city' => $citySlug, 'category' => 'avtoservizi'])],
            ['label' => 'Ремонти', 'url' => route('seo.city.category', ['city' => $citySlug, 'category' => 'remonti'])],
            ['label' => 'Почистване', 'url' => route('seo.city.category', ['city' => $citySlug, 'category' => 'pochistvane'])],
            ['label' => 'Електроуслуги', 'url' => route('seo.city.category', ['city' => $citySlug, 'category' => 'elektrouslugi'])],
            ['label' => 'ВиК услуги', 'url' => route('seo.city.category', ['city' => $citySlug, 'category' => 'vik-uslugi'])],
        ];
    }

    private function internalLinks(): array
    {
        return [
            ['label' => 'Топ бизнеси', 'url' => route('top.businesses')],
            ['label' => 'Заяви оферта', 'url' => route('request.service')],
            ['label' => 'За бизнеси', 'url' => route('business.landing')],
            ['label' => 'Всички бизнеси', 'url' => route('businesses.index')],
        ];
    }

    private function canonicalRoutes(string $citySlug, ?string $categorySlug): array
    {
        if (!$categorySlug) {
            return [
                ['label' => 'Всички услуги в града', 'url' => route('seo.city', ['city' => $citySlug])],
            ];
        }

        return [
            ['label' => '/grad формат', 'url' => route('seo.city.category', ['city' => $citySlug, 'category' => $categorySlug])],
            ['label' => '/uslugi формат', 'url' => route('seo.service.city', ['category' => $categorySlug, 'city' => $citySlug])],
        ];
    }

    private function seoTitle(string $city, ?array $category): string
    {
        if (!$category) {
            return "Бизнеси и услуги в {$city} | BON";
        }

        return "{$category['label']} в {$city} | Проверени бизнеси във BON";
    }

    private function h1(string $city, ?array $category): string
    {
        if (!$category) {
            return "Бизнеси и услуги в {$city}";
        }

        return "{$category['label']} в {$city}";
    }

    private function intro(string $city, ?array $category): string
    {
        if (!$category) {
            return "Открийте проверени бизнеси, услуги и локални професионалисти в {$city}. Вижте профили, отзиви, препоръки и директни контакти.";
        }

        return "Разгледайте {$category['label']} в {$city} с реални профили, рейтинги, препоръки и директни CTA бутони за контакт или заявка за оферта.";
    }

    private function slugForCity(string $city): string
    {
        $reverse = array_flip($this->cityMap());

        return $reverse[$city] ?? $this->normalizeSlug($city);
    }

    private function normalizeSlug(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace(['_', ' '], '-', $value);
        $value = preg_replace('/-+/', '-', $value) ?: $value;

        return trim($value, '-');
    }

    private function humanizeSlug(string $slug): string
    {
        return collect(explode('-', $this->normalizeSlug($slug)))
            ->filter()
            ->map(fn ($word) => mb_convert_case($word, MB_CASE_TITLE, 'UTF-8'))
            ->implode(' ');
    }

    private function cityMap(): array
    {
        return [
            'pleven' => 'Плевен',
            'sofia' => 'София',
            'plovdiv' => 'Пловдив',
            'varna' => 'Варна',
            'burgas' => 'Бургас',
            'ruse' => 'Русе',
            'stara-zagora' => 'Стара Загора',
            'veliko-tarnovo' => 'Велико Търново',
            'blagoevgrad' => 'Благоевград',
            'dobrich' => 'Добрич',
        ];
    }

    private function categoryMap(): array
    {
        return [
            'avtoservizi' => ['slug' => 'avtoservizi', 'label' => 'Автосервизи', 'terms' => ['автосервиз', 'авто сервиз', 'автомобил']],
            'remonti' => ['slug' => 'remonti', 'label' => 'Ремонти', 'terms' => ['ремонт', 'строителство']],
            'vik-uslugi' => ['slug' => 'vik-uslugi', 'label' => 'ВиК услуги', 'terms' => ['вик', 'водопровод']],
            'elektrouslugi' => ['slug' => 'elektrouslugi', 'label' => 'Електроуслуги', 'terms' => ['електро', 'електротехник']],
            'saloni' => ['slug' => 'saloni', 'label' => 'Салони за красота', 'terms' => ['салон', 'красота', 'фризьор', 'маникюр']],
            'pochistvane' => ['slug' => 'pochistvane', 'label' => 'Почистване', 'terms' => ['почистване']],
        ];
    }
}

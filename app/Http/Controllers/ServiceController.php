<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\BusinessGrowthMetrics;
use App\Support\CategoryCatalog;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->publiclyVisible();
            });

        $services = $query
            ->latest()
            ->get()
            ->reject(fn (Service $service) => CategoryCatalog::isHiddenCategory((string) $service->category)
                || ($service->user && CategoryCatalog::businessHasHiddenCategory($service->user)))
            ->values();

        if ($request->filled('category')) {
            $category = (string) $request->query('category');

            if (CategoryCatalog::isHiddenCategory($category)) {
                $services = collect();
            }

            $services = $services
                ->filter(function (Service $service) use ($category) {
                    if (BusinessGrowthMetrics::matchesCategory((string) $service->category, $category)) {
                        return true;
                    }

                    return $service->user
                        && collect($service->user->serviceCategories())
                            ->contains(fn ($businessCategory) => BusinessGrowthMetrics::matchesCategory((string) $businessCategory, $category));
                })
                ->values();
        }

        if ($request->filled('city')) {
            $city = (string) $request->query('city');
            $services = $services
                ->filter(fn (Service $service) => $service->user
                    && BusinessGrowthMetrics::matchesCity($service->user, $city, $service->city))
                ->values();
        }

        $services = $services
            ->sortBy([
                fn ($first, $second) => ($first->user?->publicRankingScore() ?? 999) <=> ($second->user?->publicRankingScore() ?? 999),
                fn ($first, $second) => ($second->created_at?->timestamp ?? 0) <=> ($first->created_at?->timestamp ?? 0),
            ])
            ->values();

        if ($request->boolean('emergency')) {
            $services = $services
                ->filter(fn (Service $service) => $service->user?->hasEmergencyServices())
                ->values();
        }

        if ($request->boolean('works_24_7')) {
            $services = $services
                ->filter(fn (Service $service) => $service->user?->worksAroundClock())
                ->values();
        }

        if ($request->boolean('verified')) {
            $services = $services
                ->filter(fn (Service $service) => (bool) $service->user?->is_verified)
                ->values();
        }

        if ($request->boolean('premium')) {
            $services = $services
                ->filter(fn (Service $service) => $service->user?->isPremium())
                ->values();
        }

        if ($request->query('rating') === '4plus') {
            $services = $services
                ->filter(fn (Service $service) => (float) ($service->user?->averageRating() ?? 0) >= 4)
                ->values();
        }

        return view('services.index', compact('services'));
    }

    public function show(Service $service)
    {
        $service->loadMissing('user');

        $viewer = auth()->user();
        $canPreviewHiddenService = $viewer
            && $service->user
            && ($viewer->id === $service->user->id || $viewer->role === 'admin');

        if (!$service->user || (!$service->user->isPubliclyVisible() && !$canPreviewHiddenService)) {
            abort(404);
        }

        if (!$canPreviewHiddenService && (CategoryCatalog::isHiddenCategory((string) $service->category) || CategoryCatalog::businessHasHiddenCategory($service->user))) {
            abort(404);
        }

        return view('services.show', compact('service'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user || !$user->isBusiness()) {
            abort(403);
        }

        $user->loadMissing('services');

        return view('services.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isBusiness()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'nullable|numeric',
            'phone' => 'required|string|max:50',
            'image' => 'nullable|file|image|mimetypes:image/jpeg,image/png,image/webp|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'title.required' => 'Полето за заглавие е задължително.',
            'category.required' => 'Моля, избери категория.',
            'city.required' => 'Полето за град е задължително.',
            'description.required' => 'Полето за описание е задължително.',
            'phone.required' => 'Полето за телефон е задължително.',
            'price.numeric' => 'Цената трябва да бъде число.',
            'image.image' => 'Файлът трябва да бъде изображение.',
            'image.mimes' => 'Снимката трябва да е jpg, jpeg, png или webp.',
            'image.max' => 'Снимката трябва да е до 2MB.',
        ]);

        if ($user->services()->count() >= $user->categoryLimit()) {
            return back()
                ->withErrors(['title' => $user->effectivePlanLabel() . ' позволява до ' . $user->categoryLimit() . ' категории/услуги с активния ви абонамент.'])
                ->withInput();
        }

        $proposedCities = collect($user->citiesUsedForPlan())
            ->push($validated['city'])
            ->map(fn ($city) => trim((string) $city))
            ->filter()
            ->unique(fn ($city) => mb_strtolower($city))
            ->values();

        if ($proposedCities->count() > $user->cityLimit()) {
            return back()
                ->withErrors(['city' => $user->effectivePlanLabel() . ' позволява до ' . $user->cityLimit() . ' града с активния ви абонамент. Преминете към Premium или намалете обслужваните градове.'])
                ->withInput();
        }

        $proposedCategories = collect($user->categoriesUsedForPlan())
            ->push($validated['category'])
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique(fn ($category) => mb_strtolower($category))
            ->values();

        if ($proposedCategories->count() > $user->categoryLimit()) {
            return back()
                ->withErrors(['category' => $user->effectivePlanLabel() . ' позволява до ' . $user->categoryLimit() . ' категории/услуги с активния ви абонамент.'])
                ->withInput();
        }

        if ($request->hasFile('image') && $user->photoCount() >= $user->photoLimit()) {
            return back()
                ->withErrors(['image' => $user->effectivePlanLabel() . ' позволява до ' . $user->photoLimit() . ' снимки с активния ви абонамент.'])
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        $validated['user_id'] = $user->id;

        Service::create($validated);

        return redirect()->route('services.create')->with('success', 'Услугата беше публикувана успешно.');
    }
}

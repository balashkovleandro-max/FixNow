@php
    $businessName = $business->business_name ?: $business->name;
    $serviceCategories = array_values(array_filter($business->serviceCategories()));
    $serviceCities = array_values(array_filter($business->serviceCities()));
    $primaryCategory = $serviceCategories[0] ?? ($business->business_category ?: 'Локален изпълнител');
    $primaryCity = $serviceCities[0] ?? ($business->city ?: 'България');
    $averageRating = data_get($business, 'growth_average_rating') ?: $business->averageRating();
    $reviewsCount = (int) (data_get($business, 'growth_reviews_count') ?? $business->approvedReviewsCount());
    $recommendationsCount = (int) (data_get($business, 'growth_recommendations_count') ?? $business->recommendationsCount());
    $profileViews = (int) data_get($business, 'growth_profile_views_count', 0);
    $badges = $business->publicBadges();
    $hasPremiumBenefits = method_exists($business, 'hasPremiumBenefits') ? $business->hasPremiumBenefits() : $business->isPremium();
    $hasRequestBasedCategory = method_exists($business, 'hasRequestBasedCategories') && $business->hasRequestBasedCategories();
    $coverImage = data_get($business, 'cover_image');
    $galleryPreview = collect();

    if ($business->relationLoaded('businessPhotos')) {
        $galleryPreview = $business->businessPhotos
            ->pluck('path')
            ->filter()
            ->unique()
            ->values();

        $coverImage = $coverImage ?: $galleryPreview->first();
    }

    if (!$coverImage && $business->relationLoaded('services')) {
        $coverImage = optional($business->services->first(fn ($service) => filled($service->image)))->image;
    }

    $extraBadges = collect($badges)
        ->reject(fn ($badge) => in_array($badge, ['Premium', 'Препоръчан', 'Потвърден'], true))
        ->take(2)
        ->values();
@endphp

<article data-testid="executor-card" class="group relative overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.08] shadow-xl shadow-black/20 backdrop-blur-xl transition duration-300 ease-out motion-safe:hover:-translate-y-1 hover:border-cyan-300/30 hover:bg-white/[0.11] hover:shadow-2xl hover:shadow-cyan-950/20">
    <div class="relative h-28 overflow-hidden">
        @if($coverImage)
            <img src="{{ asset('storage/' . $coverImage) }}" alt="{{ $businessName }}" loading="lazy" data-testid="executor-card-cover-image" class="h-full w-full object-cover opacity-90 transition duration-300 group-hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-t from-[#020812] via-[#020812]/35 to-transparent"></div>

            @if($galleryPreview->count() > 1)
                <div class="absolute bottom-3 right-3 flex -space-x-2">
                    @foreach($galleryPreview->skip(1)->take(3) as $previewImage)
                        <img src="{{ asset('storage/' . $previewImage) }}" alt="{{ $businessName }}" loading="lazy" class="h-8 w-8 rounded-xl border border-white/20 object-cover shadow-lg shadow-black/30">
                    @endforeach
                </div>
            @endif
        @else
            <div data-testid="executor-card-fallback-visual" class="absolute inset-0 bg-gradient-to-br from-cyan-400/20 via-blue-500/12 to-violet-600/25"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_22%_24%,rgba(34,211,238,0.34),transparent_28%),radial-gradient(circle_at_78%_12%,rgba(168,85,247,0.30),transparent_32%),linear-gradient(180deg,transparent,rgba(2,8,18,0.50))]"></div>
            <div class="absolute left-4 top-4 rounded-2xl border border-white/10 bg-slate-950/45 px-3 py-2 text-xs font-black text-cyan-100 backdrop-blur-xl">
                {{ $primaryCategory }}
            </div>
            <div class="absolute bottom-4 right-4 h-14 w-14 rounded-3xl border border-cyan-300/20 bg-cyan-300/10 blur-sm"></div>
        @endif
    </div>

    <div class="relative p-5">
        <div class="-mt-12 mb-4 flex items-end justify-between gap-4">
            <a href="{{ route('businesses.show', $business) }}" class="shrink-0" aria-label="Виж профил на {{ $businessName }}">
                @if(!empty($business->avatar))
                    <img src="{{ asset('storage/' . $business->avatar) }}" alt="{{ $businessName }}" loading="lazy" class="h-16 w-16 rounded-3xl border-4 border-[#020812] object-cover shadow-lg shadow-black/35">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-3xl border-4 border-[#020812] bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 text-2xl font-black text-white shadow-lg shadow-black/35">
                        {{ strtoupper(mb_substr($businessName, 0, 1)) }}
                    </div>
                @endif
            </a>

            <div class="flex flex-wrap justify-end gap-2 pb-1">
                @if($hasPremiumBenefits)
                    <span class="rounded-full bg-violet-400/15 px-3 py-1 text-xs font-black text-violet-100 shadow-[0_0_20px_rgba(168,85,247,0.18)] ring-1 ring-violet-300/25">Premium</span>
                @endif
                @if($recommendationsCount > 0)
                    <span class="rounded-full bg-amber-300/15 px-3 py-1 text-xs font-black text-amber-100 shadow-[0_0_18px_rgba(251,191,36,0.16)] ring-1 ring-amber-200/25">Препоръчан</span>
                @endif
                @if($business->is_verified)
                    <span class="rounded-full bg-emerald-400/12 px-3 py-1 text-xs font-black text-emerald-100 ring-1 ring-emerald-300/20">Потвърден</span>
                @endif
            </div>
        </div>

        <div class="min-w-0">
            <a href="{{ route('businesses.show', $business) }}" class="block">
                <h3 class="line-clamp-1 text-xl font-black text-white transition group-hover:text-cyan-100">{{ $businessName }}</h3>
            </a>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm font-bold">
                <span class="text-cyan-200">{{ $primaryCategory }}</span>
                <span class="h-1 w-1 rounded-full bg-white/30"></span>
                <span class="text-white/65">{{ $primaryCity }}</span>
            </div>
        </div>

        <p class="mt-4 line-clamp-2 min-h-[3rem] text-sm leading-6 text-white/64">
            {{ $business->short_description ?: $business->description ?: 'Професионален профил във FixNow.bg с директен контакт, ясна информация и локални услуги за клиенти.' }}
        </p>

        <div class="mt-4 flex flex-wrap gap-2">
            @foreach(array_slice($serviceCategories, 0, 3) as $category)
                <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-xs font-bold text-white/70">{{ $category }}</span>
            @endforeach
            @foreach(array_slice($serviceCities, 0, 2) as $city)
                @if($city !== $primaryCity)
                    <span class="rounded-full border border-cyan-300/15 bg-cyan-300/[0.08] px-3 py-1 text-xs font-bold text-cyan-100">{{ $city }}</span>
                @endif
            @endforeach
            @foreach($extraBadges as $badge)
                <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-xs font-bold text-white/70">{{ $badge }}</span>
            @endforeach
        </div>

        <div class="mt-5 grid grid-cols-3 gap-2">
            <div class="rounded-2xl border border-white/10 bg-slate-950/42 p-3">
                @if($averageRating)
                    <p class="font-black text-amber-200">{{ number_format($averageRating, 1, '.', '') }} ★</p>
                    <p class="mt-1 text-[11px] font-bold text-white/45">{{ $reviewsCount }} отзива</p>
                @else
                    <p class="font-black text-amber-200">Нов</p>
                    <p class="mt-1 text-[11px] font-bold text-white/45">без отзиви</p>
                @endif
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/42 p-3">
                <span class="sr-only">{{ $recommendationsCount }} препоръки</span>
                <p class="font-black text-violet-100">{{ $recommendationsCount }}</p>
                <p class="mt-1 text-[11px] font-bold text-white/45">препоръки</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/42 p-3">
                <p class="font-black text-cyan-100">{{ $profileViews }}</p>
                <p class="mt-1 text-[11px] font-bold text-white/45">прегледа</p>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
            <a href="{{ route('businesses.track.inquiry', ['user' => $business, 'source' => 'business_card']) }}" data-track="cta_send_inquiry" class="flex min-h-12 items-center justify-center rounded-2xl border border-cyan-300/25 bg-cyan-400/12 px-4 py-3 text-center font-black text-cyan-100 transition hover:bg-cyan-400/20 active:scale-[0.99]">
                Изпрати запитване
            </a>
            <a href="{{ route('businesses.show', $business) }}" data-track="cta_view_business" class="flex min-h-12 items-center justify-center rounded-2xl border border-white/15 bg-white/[0.08] px-4 py-3 text-center font-black text-white transition hover:bg-white/15 active:scale-[0.99]">
                Виж профил
            </a>
        </div>

        @if($hasRequestBasedCategory)
            <a href="{{ route('request.service', ['category' => $serviceCategories[0] ?? $business->business_category, 'city' => $serviceCities[0] ?? $business->city]) }}" data-track="cta_request" class="mt-3 flex min-h-11 items-center justify-center rounded-2xl border border-violet-300/20 bg-violet-400/10 px-4 py-3 text-center text-sm font-black text-violet-100 transition hover:bg-violet-400/20 active:scale-[0.99]">
                Пусни заявка в тази категория
            </a>
        @endif
    </div>
</article>

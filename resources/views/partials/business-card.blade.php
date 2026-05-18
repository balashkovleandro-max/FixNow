@php
    $businessName = $business->business_name ?: $business->name;
    $averageRating = data_get($business, 'growth_average_rating') ?: $business->averageRating();
    $reviewsCount = (int) (data_get($business, 'growth_reviews_count') ?? $business->approvedReviewsCount());
    $recommendationsCount = (int) (data_get($business, 'growth_recommendations_count') ?? $business->recommendationsCount());
    $profileViews = (int) data_get($business, 'growth_profile_views_count', 0);
    $badges = $business->publicBadges();
    $coverImage = data_get($business, 'cover_image');

    if (!$coverImage && $business->relationLoaded('businessPhotos')) {
        $coverImage = optional($business->businessPhotos->first())->path;
    }

    if (!$coverImage && $business->relationLoaded('services')) {
        $coverImage = optional($business->services->first(fn ($service) => filled($service->image)))->image;
    }

    $hasRequestBasedCategory = method_exists($business, 'hasRequestBasedCategories') && $business->hasRequestBasedCategories();
@endphp

<article class="fn-hover-lift group overflow-hidden rounded-3xl border border-white/10 bg-white/10 shadow-xl shadow-black/20 backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:border-cyan-300/30 hover:shadow-2xl hover:shadow-blue-950/30">
    <a href="{{ route('businesses.show', $business) }}" class="block">
        @if($coverImage)
            <img src="{{ asset('storage/' . $coverImage) }}" alt="{{ $businessName }}" loading="lazy" class="h-44 w-full object-cover transition duration-300 group-hover:scale-[1.02]">
        @else
            <div class="relative h-44 bg-gradient-to-br from-cyan-400/20 via-blue-500/10 to-violet-600/25 transition duration-300 group-hover:scale-[1.02]">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_28%_22%,rgba(34,211,238,0.22),transparent_30%),linear-gradient(180deg,transparent,rgba(2,8,18,0.42))]"></div>
            </div>
        @endif
    </a>

    <div class="p-5">
        <div class="-mt-12 mb-4 flex items-end justify-between gap-4">
            @if(!empty($business->avatar))
                <img src="{{ asset('storage/' . $business->avatar) }}" alt="{{ $businessName }}" loading="lazy" class="h-20 w-20 rounded-2xl border-4 border-slate-950 object-cover">
            @else
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border-4 border-slate-950 bg-gradient-to-br from-cyan-400 to-violet-600 text-2xl font-black">
                    {{ strtoupper(mb_substr($businessName, 0, 1)) }}
                </div>
            @endif

            <div class="flex flex-wrap justify-end gap-2">
                @foreach(array_slice($badges, 0, 3) as $badge)
                    @php
                        $isPremiumBadge = in_array($badge, ['Premium', 'Препоръчан'], true);
                        $isVerifiedBadge = $badge === 'Потвърден';
                    @endphp
                    <span class="{{ $isPremiumBadge ? 'fn-premium-shine bg-violet-400/15 text-violet-100 ring-1 ring-violet-300/20' : ($isVerifiedBadge ? 'bg-emerald-400/10 text-emerald-100 ring-1 ring-emerald-300/20' : 'bg-white/10 text-white/80') }} rounded-full px-3 py-1 text-xs font-black">{{ $badge }}</span>
                @endforeach
            </div>
        </div>

        <h3 class="text-xl font-black">{{ $businessName }}</h3>
        <p class="mt-1 text-sm font-semibold text-cyan-200">{{ $business->business_category ?: 'Локален бизнес' }}</p>

        <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
            <span class="rounded-full bg-white/10 px-3 py-1 text-white/70">{{ implode(', ', array_slice($business->serviceCities(), 0, 2)) ?: 'България' }}</span>
            @if($averageRating)
                <span class="rounded-full bg-amber-400/10 px-3 py-1 text-amber-200">{{ number_format($averageRating, 1, '.', '') }} ★ · {{ $reviewsCount }}</span>
            @else
                <span class="rounded-full bg-amber-400/10 px-3 py-1 text-amber-200">Няма оценки</span>
            @endif
            <span class="rounded-full bg-violet-400/10 px-3 py-1 text-violet-200">{{ $recommendationsCount }} препоръки</span>
            @if($profileViews > 0)
                <span class="rounded-full bg-cyan-400/10 px-3 py-1 text-cyan-200">{{ $profileViews }} views</span>
            @endif
        </div>

        <p class="mt-4 line-clamp-2 text-sm leading-6 text-white/60">
            {{ $business->short_description ?: $business->description ?: 'Професионален профил във FixNow.bg с директен контакт и публична информация за клиенти.' }}
        </p>

        <div class="mt-5 grid gap-3 {{ $business->phone ? 'grid-cols-2' : 'grid-cols-1' }}">
            <a href="{{ route('businesses.show', $business) }}" class="flex min-h-11 items-center justify-center rounded-2xl bg-blue-500/20 px-4 py-3 text-center font-black text-blue-100 hover:bg-blue-500/30">Виж</a>
            @if($business->phone)
                <a href="{{ route('businesses.track.phone', ['user' => $business, 'source' => 'business_card']) }}" class="flex min-h-11 items-center justify-center rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-center font-black text-emerald-100 hover:bg-emerald-400/20">Обади се</a>
            @endif
        </div>

        <div class="mt-3 grid gap-3">
            <a href="{{ route('businesses.track.inquiry', ['user' => $business, 'source' => 'business_card']) }}" class="flex min-h-11 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-400/10 px-4 py-3 text-center font-black text-cyan-100 hover:bg-cyan-400/20">Изпрати запитване</a>
            @if($hasRequestBasedCategory)
                <a href="{{ route('request.service', ['category' => $business->serviceCategories()[0] ?? $business->business_category, 'city' => $business->serviceCities()[0] ?? $business->city]) }}" class="flex min-h-11 items-center justify-center rounded-2xl border border-violet-300/20 bg-violet-400/10 px-4 py-3 text-center font-black text-violet-100 hover:bg-violet-400/20">Пусни заявка</a>
            @endif
        </div>
    </div>
</article>

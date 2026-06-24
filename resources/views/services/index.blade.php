<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги | BON</title>
    <meta name="description" content="Намерете услуги от активни бизнеси във BON. Търсете по категория, град, рейтинг, Premium и потвърдени локални професионалисти.">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    @php
        $services = $services ?? collect();
        $categories = \App\Support\CategoryCatalog::names()->all();
        $trustFilters = [
            ['name' => 'premium', 'label' => 'Premium', 'value' => '1'],
            ['name' => 'verified', 'label' => 'Потвърден бизнес', 'value' => '1'],
            ['name' => 'emergency', 'label' => 'Спешни услуги', 'value' => '1'],
            ['name' => 'works_24_7', 'label' => '24/7', 'value' => '1'],
            ['name' => 'rating', 'label' => '4+ рейтинг', 'value' => '4plus'],
        ];
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_16%_12%,rgba(249,115,22,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="rounded-[34px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Услуги и оферти</p>
                    <h1 class="mt-4 max-w-4xl text-3xl font-black leading-tight sm:text-5xl">Намерете услуга от активен бизнес във BON</h1>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-white/70">Филтрирайте по категория, град и trust сигнали. Резултатите идват само от публично видими active или trial профили на бизнеси.</p>
                </div>
                <a href="{{ route('request.service') }}" class="rounded-2xl border border-orange-300/20 bg-orange-300/10 px-6 py-4 text-center font-black text-orange-100 hover:bg-orange-400/15">Заяви оферта</a>
            </div>

            <details class="mt-6 rounded-3xl border border-white/10 bg-slate-950/70 p-3 lg:hidden">
                <summary class="flex cursor-pointer list-none items-center justify-between rounded-2xl bg-white/10 px-4 py-4 text-base font-black">
                    <span>Филтри и търсене</span>
                    <span class="text-orange-300">Отвори</span>
                </summary>
                <form method="GET" action="{{ route('services.index') }}" class="mt-3 grid gap-3">
                    <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                        <span class="block text-xs font-black uppercase text-white/50">Категория</span>
                        <input name="category" list="service-category-options-mobile" value="{{ request('category') }}" placeholder="Избери или напиши категория" class="mt-2 min-h-12 w-full bg-transparent text-base text-white outline-none placeholder:text-white/40">
                        <datalist id="service-category-options-mobile">
                            @foreach($categories as $category)
                                <option value="{{ $category }}"></option>
                            @endforeach
                        </datalist>
                    </label>
                    <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                        <span class="block text-xs font-black uppercase text-white/50">Град</span>
                        <input name="city" value="{{ request('city') }}" placeholder="София, Плевен, Варна..." class="mt-2 min-h-12 w-full bg-transparent text-base text-white outline-none placeholder:text-white/40">
                    </label>
                    <div class="grid gap-2">
                        @foreach($trustFilters as $filter)
                            <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-white/75">
                                <input type="checkbox" name="{{ $filter['name'] }}" value="{{ $filter['value'] }}" {{ request($filter['name']) == $filter['value'] ? 'checked' : '' }} class="rounded border-white/20 bg-slate-950 text-orange-400">
                                {{ $filter['label'] }}
                            </label>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('services.index') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-center font-black text-white">Изчисти</a>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-4 py-4 font-black text-white">Приложи</button>
                    </div>
                </form>
            </details>

            <form method="GET" action="{{ route('services.index') }}" class="mt-8 hidden gap-3 rounded-3xl border border-white/10 bg-slate-950/60 p-3 lg:grid lg:grid-cols-[1fr_1fr_auto]">
                <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                    <span class="block text-xs font-black uppercase text-white/50">Категория</span>
                    <input name="category" list="service-category-options" value="{{ request('category') }}" placeholder="Избери или напиши категория" class="mt-2 w-full bg-transparent text-white outline-none placeholder:text-white/40">
                    <datalist id="service-category-options">
                        @foreach($categories as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                </label>

                <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                    <span class="block text-xs font-black uppercase text-white/50">Град</span>
                    <input name="city" value="{{ request('city') }}" placeholder="София, Плевен, Варна..." class="mt-2 w-full bg-transparent text-white outline-none placeholder:text-white/40">
                </label>

                <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25">Търси</button>

                <div class="lg:col-span-3 flex flex-wrap gap-2">
                    @foreach($trustFilters as $filter)
                        <label class="flex cursor-pointer items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">
                            <input type="checkbox" name="{{ $filter['name'] }}" value="{{ $filter['value'] }}" {{ request($filter['name']) == $filter['value'] ? 'checked' : '' }} class="rounded border-white/20 bg-slate-950 text-orange-400">
                            {{ $filter['label'] }}
                        </label>
                    @endforeach
                    <a href="{{ route('services.index') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Изчисти</a>
                </div>
            </form>
        </section>

        <section class="mt-8">
            @if($services->count())
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($services as $service)
                        @php
                            $business = $service->user;
                            $badges = $business?->publicBadges() ?? [];
                            $rating = $business?->averageRating();
                            $reviewsCount = $business?->approvedReviewsCount() ?? 0;
                        @endphp
                        <article class="overflow-hidden rounded-3xl border border-white/10 bg-white/10 shadow-xl shadow-black/20 backdrop-blur-xl">
                            <a href="{{ route('services.show', $service) }}" class="block">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" loading="lazy" class="h-52 w-full object-cover">
                                @else
                                    <div class="flex h-52 items-center justify-center bg-gradient-to-br from-orange-500/20 via-orange-400/10 to-orange-600/20 text-white/55">Няма снимка</div>
                                @endif
                            </a>
                            <div class="p-5">
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-bold text-orange-300">{{ \App\Support\CategoryCatalog::displayName($service->category) }}</span>
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/70">{{ $service->city }}</span>
                                    @foreach(array_slice($badges, 0, 3) as $badge)
                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/80">{{ $badge }}</span>
                                    @endforeach
                                </div>
                                <h3 class="text-xl font-black">{{ $service->title }}</h3>
                                @if($business)
                                    <a href="{{ route('businesses.show', $business) }}" class="mt-1 inline-flex text-sm font-semibold text-orange-300 hover:text-white">
                                        {{ $business->business_name ?: $business->name }}
                                    </a>
                                @else
                                    <p class="mt-1 text-sm font-semibold text-white/55">Профилът на бизнеса не е наличен</p>
                                @endif
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-white/60">{{ $service->description }}</p>
                                <div class="mt-5 grid gap-4 sm:flex sm:items-end sm:justify-between">
                                    <div>
                                        @if($service->price)
                                            <p class="text-2xl font-black text-orange-300">от {{ number_format($service->price, 2, ',', ' ') }} €</p>
                                        @else
                                            <p class="text-sm font-bold text-white/60">Цена по договаряне</p>
                                        @endif
                                        @if($rating)
                                            <p class="mt-1 text-xs text-amber-200">{{ number_format($rating, 1, '.', '') }} ★ · {{ $reviewsCount }} отзива</p>
                                        @else
                                            <p class="mt-1 text-xs text-white/45">Все още няма рейтинг</p>
                                        @endif
                                    </div>
                                    <div class="grid gap-2 {{ $business?->phone ? 'grid-cols-2 sm:grid-cols-1' : 'grid-cols-1' }}">
                                        <a href="{{ route('services.show', $service) }}" class="flex min-h-11 items-center justify-center rounded-2xl bg-orange-500/20 px-4 py-3 text-center text-sm font-black text-orange-100 hover:bg-orange-500/30">Виж</a>
                                        @if($business?->phone)
                                            <a href="{{ route('businesses.track.phone', $business) }}" class="flex min-h-11 items-center justify-center rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-center text-sm font-black text-emerald-100 hover:bg-emerald-400/20">Обади се</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-10 text-center shadow-xl shadow-black/20 backdrop-blur-xl" data-testid="public-services-empty-state">
                    <p class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-2xl font-black">B</p>
                    <h2 class="mt-5 text-2xl font-black">Все още няма активни бизнеси тук</h2>
                    <p class="mx-auto mt-3 max-w-md text-white/60">Пусни заявка и ще ти помогнем да намериш подходящ бизнес. Ако предлагаш такава услуга, добави профил на бизнес и започни да събираш видимост.</p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white">Пусни заявка</a>
                        <a href="{{ route('business.landing') }}" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-4 font-black text-white hover:bg-white/10">Стани бизнес</a>
                    </div>
                </div>
            @endif
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

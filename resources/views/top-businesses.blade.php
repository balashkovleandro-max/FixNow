<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Топ изпълнители | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(37,99,235,0.22),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(168,85,247,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-[34px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">FixNow rankings</p>
            <div class="mt-3 grid gap-6 lg:grid-cols-[1fr_360px] lg:items-end">
                <div>
                    <h1 class="max-w-4xl text-4xl font-black leading-tight sm:text-6xl">Топ изпълнители, препоръки и класации</h1>
                    <p class="mt-4 max-w-3xl text-base leading-8 text-white/70">Класациите използват реални сигнали: публична видимост, Premium, verified статус, рейтинг, отзиви, препоръки и активност.</p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="inline-flex justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white shadow-lg shadow-blue-600/25">Заяви оферта</a>
                        <a href="{{ route('businesses.index') }}" class="inline-flex justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-4 font-black text-white hover:bg-white/10">Разгледай изпълнители</a>
                    </div>
                </div>
                <form method="GET" action="{{ route('top.businesses') }}" class="grid gap-3 rounded-3xl border border-white/10 bg-slate-950/55 p-4">
                    <select name="city" class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-3 text-white outline-none">
                        <option value="">Всички градове</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    <select name="category" class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-3 text-white outline-none">
                        <option value="">Всички категории</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                    <div class="grid grid-cols-2 gap-3">
                        <button class="rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-4 py-3 font-black text-white">Филтрирай</button>
                        <a href="{{ route('top.businesses') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center font-black text-white hover:bg-white/10">Изчисти</a>
                    </div>
                </form>
            </div>
        </section>

        @php
            $sections = [
                ['id' => 'top-recommended', 'title' => 'Топ препоръчани изпълнители', 'items' => $topRecommended, 'empty' => 'Все още няма активни изпълнители за тази класация.'],
                ['id' => 'most-recommended', 'title' => 'Най-препоръчвани', 'items' => $mostRecommended, 'empty' => 'Все още няма препоръки.'],
                ['id' => 'highest-rated', 'title' => 'Най-високо оценени', 'items' => $highestRated, 'empty' => 'Все още няма одобрени отзиви.'],
                ['id' => 'newest-businesses', 'title' => 'Най-нови изпълнители', 'items' => $newestBusinesses, 'empty' => 'Все още няма нови активни изпълнители.'],
                ['id' => 'verified-businesses', 'title' => 'Потвърдени изпълнители', 'items' => $verifiedBusinesses, 'empty' => 'Все още няма потвърдени изпълнители.'],
                ['id' => 'premium-businesses', 'title' => 'Premium / Препоръчани изпълнители', 'items' => $premiumBusinesses, 'empty' => 'Все още няма Premium изпълнители.'],
            ];
        @endphp

        @foreach($sections as $section)
            <section class="mt-8" data-testid="{{ $section['id'] }}">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <h2 class="text-2xl font-black">{{ $section['title'] }}</h2>
                    <span class="text-sm font-bold text-white/45">{{ $section['items']->count() }} резултата</span>
                </div>

                @if($section['items']->isNotEmpty())
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                        @foreach($section['items'] as $business)
                            @include('partials.business-card', ['business' => $business])
                        @endforeach
                    </div>
                @else
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl">
                        <p class="font-black">{{ $section['empty'] }}</p>
                    </div>
                @endif
            </section>
        @endforeach

        <section class="mt-8 grid gap-5 lg:grid-cols-2">
            @foreach([
                'Топ в Плевен' => $topPleven,
                'Топ автосервизи' => $topAuto,
                'Топ майстори' => $topMakers,
                'Топ почистване' => $topCleaning,
                'Топ услуги за малки бизнеси' => $topBusinessServices,
            ] as $title => $items)
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">{{ $title }}</h2>
                    <div class="mt-4 grid gap-3">
                        @forelse($items as $business)
                            <a href="{{ route('businesses.show', $business) }}" class="flex items-center justify-between gap-3 rounded-2xl bg-slate-950/45 px-4 py-3 hover:bg-white/10">
                                <span class="font-bold">{{ $business->business_name ?: $business->name }}</span>
                                <span class="text-xs text-white/50">{{ (int) data_get($business, 'growth_recommendations_count', 0) }} препоръки</span>
                            </a>
                        @empty
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3 text-sm text-white/55">Няма достатъчно данни.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </section>

        <section class="mt-8 rounded-3xl border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
            <h2 class="text-2xl font-black">Популярни категории</h2>
            <div class="mt-5 flex flex-wrap gap-3">
                @forelse($popularCategories as $category)
                    <a href="{{ route('top.businesses', ['category' => $category['name']]) }}" class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-sm font-bold text-white/75 hover:border-cyan-300/30 hover:text-cyan-100">
                        {{ $category['name'] }} · {{ $category['count'] }}
                    </a>
                @empty
                    <p class="text-sm text-white/55">Категориите ще се появят, когато има активни изпълнители.</p>
                @endforelse
            </div>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

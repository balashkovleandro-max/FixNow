<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бизнеси | BON</title>
    <meta name="description" content="Разгледайте активни бизнеси, услуги и места в BON. Филтрирайте по град, категория, Premium, проверени профили, рейтинг и локални услуги.">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page bon-dark-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    @php
        $businesses = $businesses ?? collect();
        $filterChips = [
            ['name' => 'premium', 'label' => 'Premium', 'value' => '1'],
            ['name' => 'verified', 'label' => 'Потвърдени', 'value' => '1'],
            ['name' => 'emergency', 'label' => 'Спешни услуги', 'value' => '1'],
            ['name' => 'works_24_7', 'label' => '24/7', 'value' => '1'],
            ['name' => 'rating', 'label' => '4+ рейтинг', 'value' => '4plus'],
        ];
        $categories = \App\Support\CategoryCatalog::names()->all();
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.22),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-[34px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <div class="grid gap-8 lg:grid-cols-[1fr_420px] lg:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">BON бизнеси</p>
                    <h1 class="mt-4 max-w-4xl text-3xl font-black leading-tight sm:text-5xl">Открийте проверени бизнеси, услуги и места около вас</h1>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-white/70">Публичните резултати показват само активни или trial профили. Premium и проверените бизнеси получават предимство в подреждането.</p>
                </div>
                <div class="rounded-3xl border border-orange-300/20 bg-orange-300/10 p-5">
                    <p class="text-3xl font-black">{{ $businesses->count() }}</p>
                    <p class="mt-2 text-sm leading-6 text-white/70">публично видими бизнес профила според текущите филтри</p>
                </div>
            </div>

            <details class="mt-6 rounded-3xl border border-white/10 bg-slate-950/70 p-3 lg:hidden">
                <summary class="flex cursor-pointer list-none items-center justify-between rounded-2xl bg-white/10 px-4 py-4 text-base font-black">
                    <span>Филтри и търсене</span>
                    <span class="text-orange-300">Отвори</span>
                </summary>
                <form method="GET" action="{{ route('businesses.index') }}" class="mt-3 grid gap-3">
                    <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                        <span class="block text-xs font-black uppercase text-white/50">Категория</span>
                        <input name="category" list="business-category-options-mobile" value="{{ request('category') }}" placeholder="Избери или напиши категория" class="mt-2 min-h-12 w-full bg-transparent text-base text-white outline-none placeholder:text-white/40">
                        <datalist id="business-category-options-mobile">
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
                        @foreach($filterChips as $chip)
                            <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-white/75">
                                <input type="checkbox" name="{{ $chip['name'] }}" value="{{ $chip['value'] }}" {{ request($chip['name']) == $chip['value'] ? 'checked' : '' }} class="rounded border-white/20 bg-slate-950 text-orange-400">
                                {{ $chip['label'] }}
                            </label>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('businesses.index') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-center font-black text-white">Изчисти</a>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-4 py-4 font-black text-white">Приложи</button>
                    </div>
                </form>
            </details>

            <form method="GET" action="{{ route('businesses.index') }}" class="mt-8 hidden gap-3 rounded-3xl border border-white/10 bg-slate-950/60 p-3 lg:grid lg:grid-cols-[1fr_1fr_auto]">
                <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                    <span class="block text-xs font-black uppercase text-white/50">Категория</span>
                    <input name="category" list="business-category-options" value="{{ request('category') }}" placeholder="Избери или напиши категория" class="mt-2 w-full bg-transparent text-white outline-none placeholder:text-white/40">
                    <datalist id="business-category-options">
                        @foreach($categories as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                </label>

                <label class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                    <span class="block text-xs font-black uppercase text-white/50">Град</span>
                    <input name="city" value="{{ request('city') }}" placeholder="София, Плевен, Варна..." class="mt-2 w-full bg-transparent text-white outline-none placeholder:text-white/40">
                </label>

                <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25">Филтрирай</button>

                <div class="lg:col-span-3 flex flex-wrap gap-2">
                    @foreach($filterChips as $chip)
                        <label class="flex cursor-pointer items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">
                            <input type="checkbox" name="{{ $chip['name'] }}" value="{{ $chip['value'] }}" {{ request($chip['name']) == $chip['value'] ? 'checked' : '' }} class="rounded border-white/20 bg-slate-950 text-orange-400">
                            {{ $chip['label'] }}
                        </label>
                    @endforeach
                    <a href="{{ route('businesses.index') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Изчисти</a>
                </div>
            </form>
        </section>

        <section class="mt-8">
            @if($businesses->isEmpty())
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-10 text-center shadow-xl shadow-black/20 backdrop-blur-xl" data-testid="public-businesses-empty-state">
                    <p class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-2xl font-black">B</p>
                    <h2 class="mt-5 text-2xl font-black">Все още няма активни бизнеси тук</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-white/60">Ако предлагаш услуги или управляваш място в тази категория, добави профил и започни да изграждаш видимост, доверие и професионално онлайн присъствие.</p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('categories') }}" class="rounded-2xl border border-orange-300/20 bg-orange-300/10 px-6 py-4 font-black text-orange-100 hover:bg-orange-400/15">Виж категории</a>
                        <a href="{{ route('business.landing') }}" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white">Добави бизнес</a>
                    </div>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($businesses as $business)
                        @include('partials.business-card', ['business' => $business])
                    @endforeach
                </div>
            @endif
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_10%,rgba(34,211,238,0.18),transparent_30%),radial-gradient(circle_at_82%_20%,rgba(168,85,247,0.16),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="mb-8 rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">Категории</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">Открий точните услуги и изпълнители по категория</h1>
            <p class="mt-4 max-w-2xl text-base leading-8 text-white/70">Разгледайте профили за ремонти, спешни домашни услуги, авто услуги, почистване, техника, услуги за малки бизнеси, красота и още локални услуги.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('request.service') }}" class="inline-flex justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white shadow-lg shadow-blue-600/25">Заяви оферта</a>
                <a href="{{ route('businesses.index') }}" class="inline-flex justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-4 font-black text-white hover:bg-white/10">Разгледай изпълнители</a>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($categories as $category)
                <a href="{{ route('services.index', ['category' => $category['name']]) }}" class="group rounded-3xl border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl transition hover:-translate-y-1 hover:border-cyan-300/30">
                    <div class="mb-5 h-20 rounded-2xl bg-gradient-to-br from-cyan-400/20 via-blue-500/10 to-violet-600/20 ring-1 ring-white/10"></div>
                    <h2 class="text-xl font-black">{{ $category['name'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-white/60">{{ $category['desc'] }}</p>
                    <p class="mt-5 text-sm font-bold text-cyan-200">
                        {{ $category['count'] > 0 ? $category['count'] . ' ' . ($category['count'] === 1 ? 'изпълнител' : 'изпълнители') : 'Очаква първи изпълнител' }}
                    </p>
                </a>
            @empty
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-10 text-center shadow-xl shadow-black/20 backdrop-blur-xl sm:col-span-2 lg:col-span-4">
                    <p class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-violet-600 text-2xl font-black">F</p>
                    <h2 class="mt-5 text-2xl font-black">Все още няма активни категории</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-white/60">Пусни заявка и ще ти помогнем да намериш подходящ изпълнител, или добави профил, ако предлагаш локални услуги.</p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white">Пусни заявка</a>
                        <a href="{{ route('business.landing') }}" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-4 font-black text-white hover:bg-white/10">Стани изпълнител</a>
                    </div>
                </div>
            @endforelse
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

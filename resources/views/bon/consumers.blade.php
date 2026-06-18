@php
    $navItems = [
        ['label' => 'Начало', 'href' => route('home')],
        ['label' => 'За бизнеси', 'href' => route('business.landing')],
        ['label' => 'Инструменти', 'href' => route('bon.tools')],
        ['label' => 'Планове', 'href' => route('plans')],
    ];

    $needs = [
        'Трябва ми услуга',
        'Искам оферта',
        'Търся свободен час',
        'Искам правилен бизнес',
        'Имам спешна нужда',
        'Не знам откъде да започна',
    ];

    $steps = [
        ['title' => 'Описваш нуждата', 'text' => 'С няколко думи казваш какво ти трябва и къде.'],
        ['title' => 'BON подрежда посоката', 'text' => 'Нуждата се превръща в ясна следваща стъпка.'],
        ['title' => 'Стигаш до решение', 'text' => 'Подходящ бизнес, оферта, услуга или свободно време.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>За потребители | BON Business Operating Network</title>
    <meta name="description" content="BON помага на хората да намерят правилното решение без излишно търсене.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bon-dark-page antialiased">
    <main class="relative min-h-screen overflow-x-hidden text-white">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(37,99,235,.18)_0%,rgba(2,6,23,.82)_42%,rgba(2,6,23,1)_100%)]"></div>
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-pink-300/18 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-20 h-[34rem] w-[34rem] rounded-full bg-violet-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.28]" style="background-image: linear-gradient(to right, rgba(236,72,153,.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.07) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 px-4 pb-16 pt-5 sm:px-6 lg:px-8">
            <header class="mx-auto flex max-w-[1320px] items-center justify-between rounded-[1.75rem] border border-white/70 bg-white/75 px-4 py-3 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <div class="text-2xl font-black tracking-tight">BON</div>
                        <div class="hidden text-sm font-medium text-slate-500 sm:block">Business Operating Network</div>
                    </div>
                </a>

                <nav class="hidden items-center gap-8 lg:flex">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}" class="text-sm font-semibold text-slate-600 transition hover:text-pink-500">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <a href="{{ route('register') }}" onclick="window.trackBonEvent('sign_up_start', { source: 'consumers_landing_header' })" class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-pink-500 px-5 py-3 text-sm font-black text-white shadow-xl shadow-pink-500/25">Регистрация</a>
            </header>

            <section class="mx-auto grid max-w-[1320px] gap-8 pt-14 lg:grid-cols-[1fr_0.8fr] lg:items-center lg:pt-20">
                <div>
                    <div class="inline-flex rounded-full border border-pink-200/70 bg-white/80 px-4 py-2 text-sm font-bold text-pink-600 shadow-sm">За потребители</div>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight tracking-tight sm:text-6xl">
                        Кажи какво ти трябва. <span class="bg-gradient-to-r from-fuchsia-500 to-pink-500 bg-clip-text text-transparent">BON ще те насочи.</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Вместо да губиш време в случайно търсене, описваш нуждата си и BON те насочва към подходящ бизнес, оферта, услуга или свободно време.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-fuchsia-500 to-pink-500 px-6 text-sm font-black text-white shadow-xl shadow-pink-500/25">Опиши нужда</a>
                        <a href="{{ route('home') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm">Виж как работи</a>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-pink-900/10 backdrop-blur-2xl">
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($needs as $need)
                            <div class="rounded-3xl border border-white/70 bg-white/75 p-4 text-sm font-bold text-slate-700 shadow-sm shadow-pink-900/5">{{ $need }}</div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-10 grid max-w-[1320px] gap-5 lg:grid-cols-3">
                @foreach ($steps as $index => $step)
                    <article class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-pink-900/5 backdrop-blur-2xl">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-fuchsia-500 to-pink-500 text-sm font-black text-white">{{ $index + 1 }}</div>
                        <h2 class="mt-5 text-xl font-black">{{ $step['title'] }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $step['text'] }}</p>
                    </article>
                @endforeach
            </section>
        </div>
    </main>
</body>
</html>

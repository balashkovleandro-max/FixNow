@php
    $navItems = [
        ['label' => 'Начало', 'href' => route('home')],
        ['label' => 'За бизнеси', 'href' => route('business.landing')],
        ['label' => 'Инструменти', 'href' => route('bon.tools')],
        ['label' => 'Планове', 'href' => route('plans')],
    ];

    $roles = ['Маркетинг и реклама', 'Уеб сайтове и софтуер', 'Дизайн и брандинг', 'Събития и фотография', 'Бизнес консултации', 'Фрийланс услуги'];

    $flow = [
        ['title' => 'Бизнесът има проблем', 'text' => 'Продажби, реклама, сайт, съдържание, процеси или растеж.'],
        ['title' => 'BON изяснява нуждата', 'text' => 'Подреждаме какво реално трябва да се направи.'],
        ['title' => 'Специалистът помага', 'text' => 'Подбран човек изпълнява конкретното решение.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BON Talent Network | BON Business Operating Network</title>
    <meta name="description" content="BON Talent Network е мрежа от специалисти, които помагат на бизнесите да изпълнят правилните решения.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bon-dark-page antialiased">
    <main class="relative min-h-screen overflow-x-clip text-white">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(37,99,235,.18)_0%,rgba(2,6,23,.82)_42%,rgba(2,6,23,1)_100%)]"></div>
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-20 h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.28]" style="background-image: linear-gradient(to right, rgba(124,58,237,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.07) 1px, transparent 1px); background-size: 72px 72px;"></div>

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
                        <a href="{{ $item['href'] }}" class="text-sm font-semibold text-slate-600 transition hover:text-violet-600">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <a href="mailto:hello@bon.bg?subject=BON%20Talent%20Network" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-black text-white shadow-xl shadow-violet-500/25">Кандидатствай</a>
            </header>

            <section class="mx-auto grid max-w-[1320px] gap-8 pt-14 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:pt-20">
                <div>
                    <div class="inline-flex rounded-full border border-violet-200/70 bg-white/80 px-4 py-2 text-sm font-bold text-violet-700 shadow-sm">BON Talent Network</div>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight tracking-tight sm:text-6xl">
                        Стани част от мрежата от специалисти на <span class="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">BON.</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Ако си маркетолог, дизайнер, уеб разработчик, copywriter, sales специалист, видео монтажист, фотограф, консултант или друг специалист, BON може да те свързва с бизнеси, които вече имат реална нужда от помощ.
                    </p>
                    <p class="mt-5 max-w-2xl rounded-3xl border border-blue-100 bg-white/70 p-5 text-sm font-semibold leading-7 text-slate-600 shadow-sm">
                        Не гониш случайни клиенти. Получаваш възможности, когато даден бизнес има конкретен проблем, който твоите умения могат да решат.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="mailto:hello@bon.bg?subject=BON%20Talent%20Network" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">Кандидатствай като специалист</a>
                        <a href="{{ route('business.landing') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm">Виж как работи</a>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-violet-900/10 backdrop-blur-2xl">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($roles as $role)
                            <div class="rounded-2xl border border-white/70 bg-white/80 px-3 py-3 text-center text-xs font-black text-slate-600 shadow-sm">{{ $role }}</div>
                        @endforeach
                    </div>

                    <div class="mt-6 grid gap-4">
                        @foreach ($flow as $index => $item)
                            <article class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm shadow-blue-900/5">
                                <div class="flex gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-sm font-black text-white">{{ $index + 1 }}</div>
                                    <div>
                                        <h2 class="text-lg font-black">{{ $item['title'] }}</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>

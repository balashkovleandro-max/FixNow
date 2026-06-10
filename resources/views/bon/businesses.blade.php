@php
    $navItems = [
        ['label' => 'Начало', 'href' => route('home')],
        ['label' => 'За бизнеси', 'href' => route('business.landing')],
        ['label' => 'Инструменти', 'href' => route('bon.tools')],
        ['label' => 'Планове', 'href' => route('plans')],
    ];

    $profileElements = [
        ['title' => 'Професионален публичен профил', 'text' => 'Име, описание, услуги, снимки, работни градове и ясни контактни действия.'],
        ['title' => 'Услуги и локации', 'text' => 'Показвате къде работите, какво предлагате и как клиентите да разпознаят силния ви профил.'],
        ['title' => 'Отзиви и доверие', 'text' => 'Рейтинг, препоръки, verified badge и по-подредено представяне пред клиента.'],
        ['title' => 'Статистики', 'text' => 'Следите преглеждания, кликове и реалния интерес към профила си.'],
    ];

    $premiumBenefits = [
        'По-високо позициониране в публичните резултати',
        'Premium / Препоръчан badge',
        'Повече снимки, градове и категории',
        'Показване в препоръчани бизнеси',
        'По-силен профил с галерия и доверителни сигнали',
        'Приоритетна поддръжка',
    ];

    $whyBon = [
        ['title' => 'Социалният пост изчезва', 'text' => 'BON профилът остава подреден, публичен и готов за хора, които търсят вашата услуга или място.'],
        ['title' => 'Сайтът сам по себе си не стига', 'text' => 'BON добавя категории, градове, badges, отзиви и локална откриваемост към вашето представяне.'],
        ['title' => 'Доверието трябва да се вижда', 'text' => 'Снимки, рейтинг, проверка и ясно описание намаляват съмнението преди първия контакт.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>За бизнеси | BON Business Operating Network</title>
    <meta name="description" content="BON помага на бизнесите да изградят професионален публичен профил, повече видимост, доверие, отзиви, статистики и Premium позициониране.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="antialiased">
    <main class="relative min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.98)_0%,rgba(248,250,255,.82)_42%,rgba(248,250,255,1)_100%)]"></div>
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-20 h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[28rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/14 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.24]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

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
                        <a href="{{ $item['href'] }}" class="text-sm font-semibold text-slate-600 transition hover:text-blue-600">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <a href="{{ route('register') }}" data-track="cta_business_signup" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-black text-white shadow-xl shadow-violet-500/25">Добави бизнес</a>
            </header>

            <section class="mx-auto grid max-w-[1320px] gap-10 pt-14 lg:grid-cols-[1fr_0.86fr] lg:items-center lg:pt-20">
                <div>
                    <div class="inline-flex rounded-full border border-blue-200/70 bg-white/80 px-4 py-2 text-sm font-bold text-blue-700 shadow-sm">Платформа за бизнес присъствие</div>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight tracking-tight sm:text-6xl">
                        Изградете профил, който носи <span class="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">видимост и доверие.</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        BON помага на бизнесите да се представят професионално онлайн: профил, категории, градове, снимки, отзиви, badges, статистики и Premium видимост в един подреден публичен слой.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @guest
                            <a href="{{ route('register') }}" data-track="cta_business_signup" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/25">Създай BON профил</a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/25">Към таблото</a>
                        @endauth
                        <a href="{{ route('plans') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm">Виж плановете</a>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl">
                    <div class="rounded-[1.75rem] bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-2xl shadow-violet-500/25">
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full bg-white/18 px-3 py-1 text-xs font-black">Premium</span>
                            <span class="rounded-full bg-white/18 px-3 py-1 text-xs font-black">Потвърден</span>
                        </div>
                        <div class="mt-10 flex h-16 w-16 items-center justify-center rounded-3xl bg-white/20 text-2xl font-black ring-1 ring-white/30">B</div>
                        <h2 class="mt-5 text-2xl font-black">Вашият BON профил</h2>
                        <p class="mt-2 text-sm leading-6 text-white/75">Услуги, локации, снимки, отзиви, директен контакт и показатели за интереса към бизнеса.</p>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach(['Видимост в категории', 'Показване по градове', 'Отзиви и badges', 'Статистика за кликове'] as $item)
                            <div class="rounded-2xl border border-slate-200/70 bg-white/75 px-4 py-3 text-sm font-bold text-slate-600">{{ $item }}</div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-12 max-w-[1320px]">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Какво получава бизнесът</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Профилът не е просто визитка. Той е публично доказателство.</h2>
                </div>
                <div class="mt-7 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($profileElements as $item)
                        <article class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl">
                            <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-white">✓</div>
                            <h3 class="text-lg font-black">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="mx-auto mt-12 grid max-w-[1320px] gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Premium видимост</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Premium помага профилът ви да изпъкне.</h2>
                    <p class="mt-5 text-base leading-8 text-slate-600">
                        Standard дава стабилно публично присъствие. Premium добавя по-силно позициониране, визуални badges, повече съдържание и повече сигнали за доверие пред клиента.
                    </p>
                    <a href="{{ route('plans') }}" class="mt-7 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">Виж Standard и Premium</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($premiumBenefits as $benefit)
                        <div class="rounded-3xl border border-white/70 bg-white/75 p-5 text-sm font-bold text-slate-700 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                            {{ $benefit }}
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mx-auto mt-12 max-w-[1320px] rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:p-8">
                <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">Защо не е просто пост или линк</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Онлайн присъствието трябва да е подредено, проверимо и постоянно.</h2>
                    </div>
                    <div class="grid gap-4">
                        @foreach ($whyBon as $item)
                            <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-5">
                                <h3 class="font-black">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-12 max-w-[1320px] rounded-[2rem] border border-white/70 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-6 text-white shadow-2xl shadow-violet-500/20 sm:p-8">
                <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-white/70">Старт във BON</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Покажете бизнеса си така, както клиентите очакват да го видят.</h2>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-white/75">Силен профил, ясни категории, доверие, снимки и Premium възможности за повече видимост.</p>
                    </div>
                    @guest
                        <a href="{{ route('register') }}" data-track="cta_business_signup" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl">Добави бизнес</a>
                    @endguest
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl">Към таблото</a>
                    @endauth
                </div>
            </section>
        </div>
    </main>
</body>
</html>

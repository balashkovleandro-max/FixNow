@php
    $tools = [
        ['title' => 'Финансов анализ', 'text' => 'Въведи оборот, разходи, персонал, наем и маркетинг, за да видиш реална печалба, маржове и къде бизнесът губи пари.', 'color' => 'from-blue-500 to-cyan-400'],
        ['title' => 'Калкулатор “Колко клиенти ми трябват?”', 'text' => 'Изчислява колко клиенти или поръчки са нужни на месец, за да покриеш разходите и да постигнеш желаната печалба.', 'color' => 'from-violet-500 to-purple-500'],
        ['title' => 'Visibility Score', 'text' => 'Показва колко добре е представен бизнесът онлайн — снимки, описание, услуги, градове, отзиви, активност и Premium видимост.', 'color' => 'from-fuchsia-500 to-pink-500'],
        ['title' => 'Калкулатор за ценообразуване', 'text' => 'Помага да разбереш дали дадена услуга или продукт е на правилна цена според себестойност, време, труд и желан марж.', 'color' => 'from-cyan-400 to-emerald-400'],
        ['title' => 'Репутация и отзиви', 'text' => 'Помага на бизнеса да събира повече отзиви, да следи средния рейтинг и да разбира какво влияе на доверието.', 'color' => 'from-indigo-500 to-blue-500'],
        ['title' => 'Месечен бизнес доклад', 'text' => 'Обобщава резултатите за месеца — видимост, профил, финанси, отзиви и конкретни следващи стъпки.', 'color' => 'from-blue-600 via-violet-600 to-fuchsia-500'],
    ];

    $toolGroups = [
        [
            'label' => 'Business tools',
            'title' => 'За бизнеси',
            'items' => ['Бизнес диагностика', 'Стратегия за развитие', 'Консултации', 'Анализ приходи/разходи', 'Публикуване на задачи', 'Намиране на фрийлансъри', 'Статистика', 'Резервации / booking', 'Абонамент / план'],
            'accent' => 'from-blue-500 to-cyan-400',
        ],
        [
            'label' => 'Freelancer tools',
            'title' => 'За фрийлансъри',
            'items' => ['Проекти / Портфолио', 'Кандидатури', 'Умения', 'Намери задачи', 'Отзиви', 'Статистика'],
            'accent' => 'from-violet-500 to-fuchsia-500',
        ],
        [
            'label' => 'Consumer tools',
            'title' => 'За потребители',
            'items' => ['Търсене', 'Любими', 'Резервации / заявки', 'Отзиви', 'История'],
            'accent' => 'from-fuchsia-500 to-pink-500',
        ],
        [
            'label' => 'Admin tools',
            'title' => 'За админ',
            'items' => ['Потребители', 'Бизнеси', 'Фрийлансъри', 'Абонаменти', 'Задачи', 'Консултации', 'Статистика', 'Плащания'],
            'accent' => 'from-cyan-400 to-blue-500',
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Инструменти | BON</title>
    <meta name="description" content="BON инструменти за финансов анализ, нужни клиенти, Visibility Score, ценообразуване, репутация, отзиви и месечни бизнес доклади.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="bon-dark-page antialiased">
    <main class="relative min-h-screen overflow-x-hidden bg-[#020617] text-white">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(37,99,235,.18)_0%,rgba(2,6,23,.82)_42%,rgba(2,6,23,1)_100%)]"></div>
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-20 h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.25]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10">
            @include('partials.public-header')

            <section class="mx-auto max-w-[1440px] px-4 py-9 sm:px-6 sm:py-16 lg:px-8 lg:py-24">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="inline-flex rounded-full border border-blue-300/20 bg-white/10 px-4 py-2 text-sm font-bold text-blue-100 shadow-sm shadow-black/20">
                        BON инструменти
                    </div>
                    <h1 class="mt-4 text-[31px] font-black leading-tight tracking-tight text-white sm:mt-6 sm:text-6xl">
                        Не просто профил. Система за бизнес показатели, видимост и растеж.
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-[15px] leading-7 text-slate-300 sm:mt-6 sm:text-lg sm:leading-8">
                        BON събира най-важните сигнали за бизнеса на едно място: финанси, нужни клиенти,
                        ценообразуване, видимост, репутация, отзиви и месечни доклади.
                    </p>
                </div>

                <div class="mt-8 grid gap-4 sm:mt-12 sm:gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($tools as $tool)
                        <article class="rounded-[1.45rem] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl transition hover:-translate-y-1 hover:border-blue-300/30 hover:bg-white/[0.13] sm:rounded-[2rem] sm:p-6">
                            <div class="mb-4 h-2 w-16 rounded-full bg-gradient-to-r {{ $tool['color'] }} sm:mb-5 sm:w-20"></div>
                            <h2 class="text-xl font-black tracking-tight sm:text-2xl">{{ $tool['title'] }}</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-300 sm:leading-7">{{ $tool['text'] }}</p>
                        </article>
                    @endforeach
                </div>

                <section class="mt-8 rounded-[1.45rem] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:mt-12 sm:rounded-[2rem] sm:p-8">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-200/80">Tool system</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-4xl">Инструментите са различни според ролята.</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-300">
                                BON не показва еднакъв dashboard на всички. Всеки профил получава секции и инструменти според реалната си работа.
                            </p>
                        </div>
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/25">Избери профил</a>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
                        @foreach($toolGroups as $group)
                            <article class="rounded-[1.25rem] border border-white/10 bg-slate-950/45 p-5 sm:rounded-[1.75rem]">
                                <div class="h-1.5 w-16 rounded-full bg-gradient-to-r {{ $group['accent'] }}"></div>
                                <p class="mt-5 text-xs font-black uppercase tracking-[0.18em] text-white/40">{{ $group['label'] }}</p>
                                <h3 class="mt-2 text-xl font-black">{{ $group['title'] }}</h3>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach($group['items'] as $item)
                                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-black text-slate-300">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <div class="mt-8 rounded-[1.45rem] border border-white/70 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-2xl shadow-violet-500/20 sm:mt-12 sm:rounded-[2rem] sm:p-8">
                    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-white/70">Започни от dashboard-а</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">Финансовият анализ вече е част от business dashboard-а.</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-white/75">
                                Влез като бизнес профил и отвори “Финансов анализ”, за да запишеш първия месечен отчет.
                            </p>
                        </div>
                        @auth
                            <a href="{{ auth()->user()->isBusiness() ? route('business.insights.index') : route('bon.onboarding') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl">Отвори инструментите</a>
                        @endauth
                        @guest
                            <a href="{{ route('register') }}" data-track="cta_business_signup" onclick="window.trackBonEvent('business_registration_start', { source: 'tools_page' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl">Създай бизнес профил</a>
                        @endguest
                    </div>
                </div>
            </section>

            @include('partials.public-footer')
        </div>
    </main>
</body>
</html>

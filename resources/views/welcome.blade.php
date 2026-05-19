<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixNow.bg | Проверени изпълнители и заявки за оферти</title>
    <meta name="description" content="FixNow.bg помага да намерите проверен локален изпълнител или да пуснете заявка и да получите оферти за услуги във вашия град.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fnFadeUp {
            from {
                opacity: 0;
                transform: translate3d(0, 14px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes fnGlowDrift {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
            }
            50% {
                transform: translate3d(12px, -10px, 0) scale(1.025);
            }
        }

        @keyframes fnBadgeShine {
            0%, 55% {
                transform: translateX(-140%) skewX(-18deg);
            }
            78%, 100% {
                transform: translateX(140%) skewX(-18deg);
            }
        }

        @keyframes fnPulseDot {
            0%, 100% {
                opacity: .78;
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(34, 211, 238, .36);
            }
            50% {
                opacity: 1;
                transform: scale(1.12);
                box-shadow: 0 0 0 7px rgba(34, 211, 238, 0);
            }
        }

        .fn-fade-up,
        .fn-section-fade {
            animation: fnFadeUp 0.62s cubic-bezier(.2, .8, .2, 1) both;
        }

        .fn-delay-1 {
            animation-delay: 90ms;
        }

        .fn-delay-2 {
            animation-delay: 180ms;
        }

        .fn-delay-3 {
            animation-delay: 260ms;
        }

        .fn-delay-4 {
            animation-delay: 340ms;
        }

        .fn-glow-drift {
            animation: fnGlowDrift 18s ease-in-out infinite;
            will-change: transform;
        }

        .fn-cta-glow {
            transition: transform 220ms ease, box-shadow 220ms ease, filter 220ms ease;
        }

        .fn-cta-glow:hover {
            box-shadow: 0 0 0 1px rgba(125, 211, 252, .22), 0 18px 48px rgba(59, 130, 246, .32);
            filter: saturate(1.08);
            transform: translateY(-1px);
        }

        .fn-hover-lift,
        .fn-category-card {
            transition: transform 240ms ease, border-color 240ms ease, box-shadow 240ms ease, background-color 240ms ease;
            will-change: transform;
        }

        .fn-hover-lift:hover {
            border-color: rgba(125, 211, 252, .34);
            box-shadow: 0 22px 58px rgba(2, 8, 18, .42), 0 0 0 1px rgba(59, 130, 246, .10);
            transform: translateY(-4px);
        }

        .fn-category-card:hover {
            border-color: rgba(125, 211, 252, .32);
            box-shadow: 0 16px 44px rgba(2, 8, 18, .34);
            transform: scale(1.02);
        }

        .fn-premium-shine {
            position: relative;
            isolation: isolate;
            overflow: hidden;
        }

        .fn-premium-shine::after {
            content: "";
            position: absolute;
            inset: -20%;
            z-index: -1;
            background: linear-gradient(110deg, transparent 0%, rgba(255, 255, 255, .24) 45%, transparent 70%);
            animation: fnBadgeShine 3.8s ease-in-out infinite;
        }

        .fn-pulse-dot {
            animation: fnPulseDot 1.8s ease-in-out infinite;
        }

        .fn-mega-panel {
            display: none;
            left: 50%;
            top: calc(100% + .75rem);
            background-color: rgba(2, 6, 23, .98);
            box-shadow: 0 26px 80px rgba(0, 0, 0, .58), 0 0 0 1px rgba(125, 211, 252, .08);
            transform: translate3d(-50%, 8px, 0);
        }

        .fn-mega-trigger.is-active > .fn-mega-panel {
            display: block;
            transform: translate3d(-50%, 0, 0);
        }

        .fn-mega-panel-wide {
            width: min(720px, calc(100vw - 2rem));
        }

        .fn-mega-panel-compact {
            width: min(560px, calc(100vw - 2rem));
        }

        @media (max-width: 1023px) {
            .fn-mega-panel {
                display: none !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .fn-fade-up,
            .fn-section-fade,
            .fn-glow-drift,
            .fn-pulse-dot,
            .fn-premium-shine::after {
                animation: none !important;
            }

            .fn-cta-glow,
            .fn-hover-lift,
            .fn-category-card,
            .fn-mega-panel {
                transition: none !important;
            }

            .fn-cta-glow:hover,
            .fn-hover-lift:hover,
            .fn-category-card:hover {
                transform: none !important;
            }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-28 text-white md:pb-0">
    @php
        $topBusinesses = $topBusinesses ?? collect();
        $mostRecommendedBusinesses = $mostRecommendedBusinesses ?? collect();
        $verifiedBusinesses = $verifiedBusinesses ?? collect();
        $newestBusinesses = $newestBusinesses ?? collect();
        $popularCategories = $popularCategories ?? collect();
        $latestReviews = $latestReviews ?? collect();
        $featuredBusinesses = $featuredBusinesses ?? collect();
        $heroStats = array_merge([
            'requests_this_month' => 0,
            'active_categories' => $popularCategories->count(),
            'businesses' => $featuredBusinesses->count(),
            'premium_businesses' => $featuredBusinesses->filter(fn ($business) => $business->isPremium())->count(),
        ], $heroStats ?? []);
        $popularSearches = ['ВиК услуги в Плевен', 'Майстор за баня в Плевен', 'Автосервиз в Плевен', 'Почистване в Плевен', 'Електротехник в Плевен', 'Хамали в Плевен'];
        $categoryFallbacks = ['Ремонти и строителство', 'Спешни домашни услуги', 'Почистване', 'Автосервизи', 'Ремонт на техника', 'Услуги за малки бизнеси'];
        $serviceMenu = [
            ['title' => 'Ремонти и майстори', 'description' => 'Ремонт, довършителни работи и домашни задачи.', 'url' => route('services.index', ['category' => 'Ремонти и строителство']), 'icon' => '01'],
            ['title' => 'ВиК услуги', 'description' => 'Течове, канали, санитария и аварийни ремонти.', 'url' => route('services.index', ['category' => 'ВиК услуги']), 'icon' => '02'],
            ['title' => 'Електроуслуги', 'description' => 'Електротехници, табла, контакти и осветление.', 'url' => route('services.index', ['category' => 'Електроуслуги']), 'icon' => '03'],
            ['title' => 'Почистване', 'description' => 'Домове, офиси, след ремонт и абонаментна поддръжка.', 'url' => route('services.index', ['category' => 'Почистване']), 'icon' => '04'],
            ['title' => 'Автосервизи', 'description' => 'Сервизи, диагностика, гуми и авто услуги.', 'url' => route('services.index', ['category' => 'Автосервизи']), 'icon' => '05'],
            ['title' => 'Виж всички услуги', 'description' => 'Разгледайте целия каталог с локални категории.', 'url' => route('services.index'), 'icon' => '→'],
        ];
        $clientMenu = [
            ['title' => 'Пусни заявка', 'description' => 'Опишете задачата и получете оферти от подходящи изпълнители.', 'url' => route('request.service'), 'icon' => '01'],
            ['title' => 'Как работи', 'description' => 'Вижте процеса от заявка до избран изпълнител.', 'url' => url('/how-it-works'), 'icon' => '02'],
            ['title' => 'Намери изпълнител', 'description' => 'Търсете директно по услуга, град, рейтинг и доверие.', 'url' => route('businesses.index'), 'icon' => '03'],
            ['title' => 'Топ изпълнители', 'description' => 'Профили с рейтинг, препоръки и активна видимост.', 'url' => route('top.businesses'), 'icon' => '04'],
        ];
        $executorMenu = [
            ['title' => 'Добави профил', 'description' => 'Създайте профил на изпълнител и започнете да се показвате.', 'url' => route('business.landing'), 'icon' => '01'],
            ['title' => 'Получавай заявки', 'description' => 'Виждайте релевантни заявки по град и категория.', 'url' => route('business.landing'), 'icon' => '02'],
            ['title' => 'Изпращай оферти', 'description' => 'Използвайте точки, за да изпращате цена и срок.', 'url' => route('business.landing'), 'icon' => '03'],
            ['title' => 'Планове', 'description' => 'Standard и Premium с ясни лимити и видимост.', 'url' => route('plans'), 'icon' => '04'],
        ];
        $seoPopularLinks = [
            ['label' => 'Автосервизи в Плевен', 'url' => route('seo.city.category', ['city' => 'pleven', 'category' => 'avtoservizi'])],
            ['label' => 'Майстори в Плевен', 'url' => route('seo.city.category', ['city' => 'pleven', 'category' => 'maistori'])],
            ['label' => 'Почистване в Плевен', 'url' => route('seo.city.category', ['city' => 'pleven', 'category' => 'pochistvane'])],
            ['label' => 'Електротехници в Плевен', 'url' => route('seo.city.category', ['city' => 'pleven', 'category' => 'elektrouslugi'])],
            ['label' => 'Услуги в Плевен', 'url' => route('seo.city', ['city' => 'pleven'])],
        ];
    @endphp

    <div class="fixed inset-0 -z-10">
        <div class="fn-glow-drift absolute inset-0 bg-[radial-gradient(circle_at_16%_18%,rgba(37,99,235,0.24),transparent_28%),radial-gradient(circle_at_82%_10%,rgba(168,85,247,0.18),transparent_30%),linear-gradient(180deg,#030712_0%,#05111f_48%,#020812_100%)]"></div>
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-cyan-300/50 to-transparent"></div>
    </div>

    @include('partials.public-header')

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0">
                <div class="fn-glow-drift absolute inset-y-0 right-0 hidden w-1/2 bg-[radial-gradient(circle_at_55%_35%,rgba(59,130,246,0.35),transparent_26%),radial-gradient(circle_at_80%_55%,rgba(168,85,247,0.28),transparent_30%)] lg:block"></div>
                <div class="absolute bottom-10 right-10 hidden h-[360px] w-[620px] opacity-80 lg:block">
                    <div class="absolute bottom-0 left-10 h-44 w-16 rounded-t-lg bg-slate-950/72 ring-1 ring-blue-300/10"></div>
                    <div class="absolute bottom-0 left-32 h-72 w-24 rounded-t-2xl bg-slate-900/72 ring-1 ring-blue-300/10"></div>
                    <div class="absolute bottom-0 left-72 h-56 w-20 rounded-t-xl bg-slate-950/78 ring-1 ring-blue-300/10"></div>
                    <div class="absolute bottom-0 left-[430px] h-80 w-24 rounded-t-2xl bg-slate-900/74 ring-1 ring-blue-300/10"></div>
                    <svg class="absolute bottom-8 left-20 h-40 w-[500px] overflow-visible" viewBox="0 0 500 150">
                        <path d="M15 98 C90 32, 148 136, 225 78 S345 42, 485 104" fill="none" stroke="rgba(59,130,246,0.88)" stroke-width="3" stroke-dasharray="8 10"/>
                        <path d="M117 42c-18 0-32 14-32 32 0 24 32 58 32 58s32-34 32-58c0-18-14-32-32-32Z" fill="#3b82f6"/>
                        <circle cx="117" cy="74" r="10" fill="#06111f"/>
                        <path d="M455 70c-18 0-32 14-32 32 0 24 32 58 32 58s32-34 32-58c0-18-14-32-32-32Z" fill="#a855f7"/>
                        <circle cx="455" cy="102" r="10" fill="#06111f"/>
                    </svg>
                </div>
            </div>

            <div class="relative mx-auto grid max-w-[1500px] gap-8 px-4 pb-8 pt-7 sm:gap-10 sm:px-6 sm:pb-10 sm:pt-10 lg:min-h-[500px] lg:grid-cols-2 lg:items-start lg:px-12 lg:pb-10 lg:pt-8">
                <div class="min-w-0 max-w-4xl">
                    <p class="fn-fade-up inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-black text-cyan-100">Жива платформа за локални услуги</p>
                    <h1 class="fn-fade-up fn-delay-1 mt-5 max-w-[760px] text-4xl font-black leading-[1.12] tracking-normal text-white sm:mt-6 sm:text-5xl lg:text-[3.55rem]">
                        Пуснете заявка веднъж.
                        <span class="block bg-gradient-to-r from-cyan-300 via-blue-400 to-violet-500 bg-clip-text text-transparent">Получете оферти от подходящи изпълнители.</span>
                    </h1>

                    <p class="fn-fade-up fn-delay-2 mt-5 max-w-[680px] text-base leading-7 text-white/70 sm:mt-6 sm:text-lg sm:leading-8">FixNow.bg свързва клиенти с проверени изпълнители по град, категория и реална нужда — от първа заявка до избран изпълнител.</p>

                    <div class="fn-fade-up fn-delay-2 mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="fn-cta-glow inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-7 py-4 text-center font-black text-white shadow-lg shadow-blue-600/25">Пусни заявка</a>
                        <a href="{{ route('businesses.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-7 py-4 text-center font-black text-white hover:bg-white/15">Разгледай изпълнители</a>
                    </div>

                    <div class="fn-fade-up fn-delay-3 mt-5 flex flex-wrap gap-2">
                        @foreach(['Проверени изпълнители', 'Заявки по градове', 'Standard и Premium профили', 'Активните абонаменти се показват публично'] as $trustItem)
                            <span class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-black text-white/75 backdrop-blur-xl">{{ $trustItem }}</span>
                        @endforeach
                    </div>

                    <div class="hidden">
                        <a href="{{ route('request.service') }}" class="fn-hover-lift rounded-[28px] border border-cyan-300/20 bg-cyan-400/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                            <span class="inline-flex rounded-full bg-cyan-300/10 px-3 py-1 text-xs font-black text-cyan-100">Получете оферти</span>
                            <h2 class="mt-4 text-2xl font-black">Пусни заявка</h2>
                            <p class="mt-2 text-sm leading-6 text-white/65">За ремонт, ВиК, електро, почистване, хамали, техника и други услуги. Опишете какво ви трябва и получете оферти.</p>
                            <span class="mt-5 inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-5 py-3 text-sm font-black text-white">Пусни заявка</span>
                        </a>
                        <a href="{{ route('businesses.index') }}" class="fn-hover-lift rounded-[28px] border border-violet-300/20 bg-violet-400/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                            <span class="inline-flex rounded-full bg-violet-300/10 px-3 py-1 text-xs font-black text-violet-100">Директно търсене</span>
                            <h2 class="mt-4 text-2xl font-black">Намери изпълнител</h2>
                            <p class="mt-2 text-sm leading-6 text-white/65">Търсете автосервизи, счетоводители, фризьори, адвокати, магазини и други локални изпълнители.</p>
                            <span class="mt-5 inline-flex min-h-11 items-center rounded-2xl bg-white/10 px-5 py-3 text-sm font-black text-white">Намери изпълнител</span>
                        </a>
                    </div>

                    <form method="GET" action="{{ route('services.index') }}" class="fn-fade-up fn-delay-3 mt-6 grid gap-3 rounded-3xl border border-white/10 bg-white/10 p-3 shadow-[0_18px_60px_rgba(0,0,0,0.32)] backdrop-blur-2xl sm:mt-7 md:grid-cols-[1.15fr_1fr_0.65fr_auto]">
                        <label class="rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 md:border-x-0 md:border-y-0 md:border-r md:bg-transparent md:px-3">
                            <span class="block text-xs font-black text-white">Каква услуга търсите?</span>
                            <input type="text" name="category" class="mt-2 min-h-12 w-full bg-transparent text-base text-white outline-none placeholder:text-white/50 md:min-h-0 md:text-sm" placeholder="ВиК, ремонт, автосервиз, почистване...">
                        </label>
                        <label class="rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 md:border-x-0 md:border-y-0 md:border-r md:bg-transparent md:px-3">
                            <span class="block text-xs font-black text-white">Къде?</span>
                            <input type="text" name="city" class="mt-2 min-h-12 w-full bg-transparent text-base text-white outline-none placeholder:text-white/50 md:min-h-0 md:text-sm" placeholder="Град или район">
                        </label>
                        <label class="rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 md:border-x-0 md:border-y-0 md:border-r md:bg-transparent md:px-3">
                            <span class="block text-xs font-black text-white">Радиус</span>
                            <select name="radius" class="mt-2 min-h-12 w-full bg-transparent text-base text-white/85 outline-none md:min-h-0 md:text-sm">
                                <option class="bg-slate-950" value="25">25 км</option>
                                <option class="bg-slate-950" value="10">10 км</option>
                                <option class="bg-slate-950" value="50">50 км</option>
                                <option class="bg-slate-950" value="all">Навсякъде</option>
                            </select>
                        </label>
                        <button type="submit" class="fn-cta-glow rounded-2xl bg-gradient-to-r from-blue-500 to-fuchsia-600 px-8 py-4 text-base font-black text-white shadow-lg shadow-blue-600/25 md:py-3 md:text-sm">Търси</button>
                    </form>

                    <div class="fn-fade-up fn-delay-4 mt-5 hidden flex-wrap items-center gap-3 text-sm sm:flex">
                        <span class="font-medium text-white/60">Популярни търсения:</span>
                        @foreach($popularSearches as $search)
                            <a href="{{ route('services.index', ['category' => $search]) }}" class="rounded-xl border border-white/15 bg-white/10 px-4 py-2 text-white/80 transition duration-200 hover:-translate-y-0.5 hover:border-blue-300/50 hover:bg-blue-400/10 hover:text-blue-100">{{ $search }}</a>
                        @endforeach
                    </div>
                </div>

                <aside class="fn-fade-up fn-delay-3 relative min-w-0 lg:pt-4">
                    <div class="absolute -inset-6 rounded-[42px] bg-gradient-to-br from-cyan-400/12 via-blue-500/10 to-violet-600/18 blur-2xl"></div>
                    <div class="relative overflow-hidden rounded-[30px] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/35 backdrop-blur-2xl sm:rounded-[34px] sm:p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Marketplace flow</p>
                                <h2 class="mt-2 text-xl font-black sm:text-2xl">От заявка до избран изпълнител</h2>
                                <p class="mt-2 hidden text-sm leading-6 text-white/60 sm:block">FixNow показва нуждата, matching-а, офертите и избора в един ясен процес.</p>
                            </div>
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-cyan-300/10 text-cyan-100 ring-1 ring-cyan-300/20">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            </span>
                        </div>

                        <div class="mt-6 grid gap-3">
                            <div class="fn-hover-lift rounded-3xl border border-cyan-300/20 bg-cyan-400/10 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="fn-pulse-dot mt-1 h-3 w-3 shrink-0 rounded-full bg-cyan-300"></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black uppercase tracking-[0.2em] text-cyan-100/80">Нова заявка</p>
                                        <h3 class="mt-2 text-lg font-black">Ремонт на баня · Плевен</h3>
                                        <p class="mt-1 text-sm text-white/60">Бюджет: до 2000 лв.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="fn-hover-lift rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-200/75">Matching</p>
                                    <p class="mt-2 text-sm font-black leading-5">3 подходящи изпълнители</p>
                                </div>
                                <div class="fn-hover-lift rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-200/75">Получени оферти</p>
                                    <p class="mt-2 text-sm font-black leading-5">2 оферти с цена и срок</p>
                                </div>
                                <div class="fn-hover-lift rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-200/75">Избран изпълнител</p>
                                    <p class="mt-2 text-sm font-black leading-5">Проверен профил · <span class="text-violet-200">Premium badge</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 hidden grid-cols-2 gap-3 sm:grid">
                            <div class="rounded-3xl border border-cyan-300/20 bg-cyan-400/10 p-4">
                                <p class="text-3xl font-black">{{ $heroStats['requests_this_month'] }}</p>
                                <p class="mt-1 text-xs font-bold leading-5 text-white/60">Нови заявки този месец</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                                <p class="text-3xl font-black">{{ $heroStats['active_categories'] }}</p>
                                <p class="mt-1 text-xs font-bold leading-5 text-white/60">Активни категории</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                                <p class="text-3xl font-black">{{ $heroStats['businesses'] }}</p>
                                <p class="mt-1 text-xs font-bold leading-5 text-white/60">Изпълнители в платформата</p>
                            </div>
                            <div class="rounded-3xl border border-violet-300/20 bg-violet-400/10 p-4">
                                <p class="text-3xl font-black">{{ $heroStats['premium_businesses'] }}</p>
                                <p class="mt-1 text-xs font-bold leading-5 text-white/60">Premium с приоритет</p>
                            </div>
                        </div>

                        <div class="mt-5 hidden rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-4 sm:block">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-300 shadow-[0_0_18px_rgba(110,231,183,0.75)]"></span>
                                <p class="text-sm leading-6 text-white/70">
                                    Premium профилите се показват с приоритет, а expired/cancelled профилите остават скрити от публичните резултати.
                                </p>
                            </div>
                        </div>

                        <div class="hidden">
                            @forelse($featuredBusinesses->take(3) as $business)
                                <a href="{{ route('businesses.show', $business) }}" class="group rounded-3xl border border-white/10 bg-slate-950/50 p-4 transition duration-200 hover:-translate-y-0.5 hover:border-cyan-300/30 hover:bg-white/10">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black text-white">
                                            {{ strtoupper(mb_substr($business->business_name ?: $business->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="truncate font-black">{{ $business->business_name ?: $business->name }}</p>
                                                @if($business->isPremium())
                                                    <span class="rounded-full bg-violet-400/15 px-2 py-0.5 text-[11px] font-black text-violet-100">Premium</span>
                                                @endif
                                                @if($business->is_verified)
                                                    <span class="rounded-full bg-emerald-400/15 px-2 py-0.5 text-[11px] font-black text-emerald-100">Потвърден</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm text-cyan-200">{{ $business->business_category ?: 'Локален изпълнител' }}</p>
                                            <p class="mt-1 text-xs text-white/50">{{ implode(', ', array_slice($business->serviceCities(), 0, 2)) ?: 'България' }}</p>
                                        </div>
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-300 shadow-[0_0_18px_rgba(110,231,183,0.8)]"></span>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-3xl border border-dashed border-cyan-300/25 bg-slate-950/45 p-5 text-center">
                                    <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 text-xl font-black">F</p>
                                    <h3 class="mt-4 text-xl font-black">Очакваме първите изпълнители</h3>
                                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-white/60">Когато има active или trial профили, тук ще се покажат препоръчани изпълнители около клиента.</p>
                                    <a href="{{ route('business.landing') }}" class="mt-5 inline-flex rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-5 py-3 text-sm font-black text-white">Добави профил на изпълнител</a>
                                </div>
                            @endforelse
                        </div>

                        <div class="hidden">
                            @foreach(['Плевен', 'София', 'Варна'] as $city)
                                <a href="{{ route('businesses.index', ['city' => $city]) }}" class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-center text-xs font-black text-white/70 hover:border-cyan-300/30 hover:text-cyan-100">
                                    <span class="mx-auto mb-2 block h-2 w-2 rounded-full bg-cyan-300"></span>
                                    {{ $city }}
                                </a>
                            @endforeach
                        </div>

                        <div class="hidden">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-black text-white">Топ категории</p>
                                <a href="{{ url('/categories') }}" class="text-xs font-black text-cyan-200 hover:text-white">Виж всички</a>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach(($popularCategories->pluck('name')->take(4)->values()->all() ?: array_slice($categoryFallbacks, 0, 4)) as $category)
                                    <a href="{{ route('businesses.index', ['category' => $category]) }}" class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-white/70 hover:bg-cyan-300/10 hover:text-cyan-100">{{ $category }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section data-testid="homepage-soft-launch-note" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-6 sm:px-6 lg:px-12">
            <div class="rounded-[30px] border border-cyan-300/20 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Soft launch</p>
                        <h2 class="mt-2 text-2xl font-black">FixNow.bg стартира поетапно</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-white/65">В момента добавяме първите проверени изпълнители по градове и категории, за да изградим качествена мрежа от реални локални изпълнители.</p>
                    </div>
                    <a href="{{ route('business.landing') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-sm font-black text-white hover:bg-white/15">Стани част от старта</a>
                </div>
            </div>
        </section>

        <section data-testid="homepage-offer-cta" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-6 sm:px-6 lg:px-12">
            <div class="fn-hover-lift grid gap-6 rounded-[32px] border border-cyan-300/20 bg-gradient-to-br from-cyan-400/12 via-blue-500/10 to-violet-600/15 p-6 shadow-2xl shadow-blue-950/25 backdrop-blur-2xl lg:grid-cols-[1fr_auto] lg:items-center lg:p-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Заяви оферта</p>
                    <h2 class="mt-3 text-3xl font-black">Опишете какво търсите и стигнете до подходящ изпълнител</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-white/65">Подходящо за ремонт, спешна услуга, сервизна диагностика, почистване или оферта от локален професионалист.</p>
                </div>
                <a href="{{ route('request.service') }}" class="fn-cta-glow rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-7 py-4 text-center font-black text-white">Заяви оферта</a>
            </div>
        </section>

        <section data-testid="homepage-how-it-works" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Как работи</p>
                        <h2 class="mt-2 text-2xl font-black sm:text-3xl">От заявка до избран изпълнител</h2>
                    </div>
                    <a href="{{ route('request.service') }}" class="text-sm font-bold text-blue-300 transition hover:text-cyan-200">Пусни заявка →</a>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    @foreach([
                        ['step' => '01', 'badge' => '1 минута', 'title' => 'Опишете нуждата', 'text' => 'Изберете категория и град, добавете описание, бюджет и срок, за да е ясно какво търсите.'],
                        ['step' => '02', 'badge' => 'Оферти', 'title' => 'Получете оферти', 'text' => 'Подходящи изпълнители виждат заявката и изпращат предложение с цена, срок и контакт.'],
                        ['step' => '03', 'badge' => 'Избор', 'title' => 'Изберете изпълнител', 'text' => 'Сравнете профили, badge-и, рейтинг и оферти, след което изберете изпълнителя за задачата.'],
                    ] as $item)
                        <article class="fn-hover-lift rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-400/10 text-sm font-black text-cyan-100 ring-1 ring-cyan-300/15">{{ $item['step'] }}</span>
                                <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-black text-white/60">{{ $item['badge'] }}</span>
                            </div>
                            <h3 class="mt-5 text-xl font-black">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-white/60">{{ $item['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section data-testid="homepage-trust-section" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="mb-5 flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Доверие</p>
                        <h2 class="text-2xl font-black sm:text-3xl">Кратки сигнали, които помагат при избора</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-6 text-white/60">Без излишен шум: профили, оферти, badge-и и модерация, които правят marketplace flow-а по-ясен.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach([
                        'Проверени изпълнители',
                        'Оферти с цена и срок',
                        'Публични профили',
                        'Admin контрол и moderation',
                        'Premium/Verified badges',
                    ] as $trustSignal)
                        <div class="fn-hover-lift rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                            <span class="mb-4 block h-2 w-10 rounded-full bg-gradient-to-r from-cyan-300 to-violet-400"></span>
                            <p class="text-sm font-black leading-5 text-white/80">{{ $trustSignal }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section data-testid="homepage-client-business-benefits" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="grid gap-5 lg:grid-cols-3">
                <article class="fn-hover-lift rounded-[32px] border border-cyan-300/20 bg-cyan-400/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">За клиенти</p>
                    <h2 class="mt-3 text-2xl font-black">По-малко търсене, повече сигурност</h2>
                    <p class="mt-4 text-sm leading-6 text-white/65">FixNow помага да намерите активни изпълнители, да сравните доверие и да направите директен контакт без дълго обикаляне из случайни каталози.</p>
                    <a href="{{ route('services.index') }}" class="mt-5 inline-flex min-h-11 items-center rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950">Намери услуга</a>
                </article>

                <article class="fn-hover-lift rounded-[32px] border border-violet-300/20 bg-violet-400/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-200/80">За изпълнители</p>
                    <h2 class="mt-3 text-2xl font-black">Профилът ти трябва да бъде откриваем</h2>
                    <p class="mt-4 text-sm leading-6 text-white/65">Профил във FixNow показва услуги, градове, телефон, отзиви, препоръки, статистика и план, който дава реална видимост пред локални клиенти.</p>
                    <a href="{{ route('business.landing') }}" class="mt-5 inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-5 py-3 text-sm font-black text-white">Стани изпълнител</a>
                </article>

                <article class="fn-hover-lift rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-white/50">Защо FixNow.bg</p>
                    <h2 class="mt-3 text-2xl font-black">Не просто каталог, а growth платформа</h2>
                    <p class="mt-4 text-sm leading-6 text-white/65">Публична видимост, Premium ranking, verified badge, reviews, analytics, заявки и top секции работят заедно, за да създадат доверие.</p>
                    <a href="{{ route('plans') }}" class="mt-5 inline-flex min-h-11 items-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-sm font-black text-white hover:bg-white/15">Виж планове</a>
                </article>
            </div>
        </section>

        <section data-testid="homepage-business-acquisition" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="fn-hover-lift overflow-hidden rounded-[34px] border border-violet-300/20 bg-gradient-to-br from-slate-950/80 via-blue-950/35 to-violet-950/45 p-6 shadow-2xl shadow-blue-950/25 backdrop-blur-xl sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_420px] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.24em] text-violet-200/80">Растеж за изпълнители</p>
                        <h2 class="mt-4 max-w-3xl text-3xl font-black leading-tight sm:text-4xl">Повече видимост, повече доверие, повече директни запитвания</h2>
                        <p class="mt-4 max-w-2xl text-base leading-8 text-white/68">Standard е стабилен публичен профил за локално откриване. Premium добавя препоръчан badge, по-високо подреждане, повече градове, повече снимки и по-силен шанс в matching на заявки.</p>
                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach([
                                'Получавате видимост',
                                'Получавате релевантни заявки',
                                'Изпращате оферти',
                                'Premium ви позиционира по-високо',
                                'Dashboard показва views, clicks и заявки',
                            ] as $benefit)
                                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-black text-white/75">{{ $benefit }}</div>
                            @endforeach
                        </div>
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('business.landing') }}" class="fn-cta-glow inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white">Добави профил</a>
                            <a href="{{ route('plans') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-6 py-4 font-black text-white hover:bg-white/15">Виж плановете</a>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        <div class="rounded-3xl border border-cyan-300/20 bg-cyan-400/10 p-5">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-sm font-black text-cyan-100">Standard</p>
                                    <p class="mt-2 text-3xl font-black">18,99 €</p>
                                </div>
                                <p class="text-sm text-white/60">/месец</p>
                            </div>
                            <p class="mt-3 text-sm text-white/60">До 2 града, 2 категории/услуги и 5 снимки.</p>
                        </div>
                        <div class="rounded-3xl border border-violet-300/30 bg-violet-400/15 p-5 ring-1 ring-violet-300/15">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-sm font-black text-violet-100">Premium</p>
                                    <p class="mt-2 text-3xl font-black">24,99 €</p>
                                </div>
                                <p class="text-sm text-white/60">/месец</p>
                            </div>
                            <p class="mt-3 text-sm text-white/60">До 5 града, 5 категории/услуги, 15 снимки и Premium позициониране.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section data-testid="homepage-seo-links" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Популярни градове и услуги</p>
                        <h2 class="mt-2 text-2xl font-black">Търсения, които клиентите използват</h2>
                    </div>
                    <a href="{{ route('seo.city', ['city' => 'pleven']) }}" class="text-sm font-bold text-blue-300 transition hover:text-cyan-200">Всички услуги в Плевен →</a>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach($seoPopularLinks as $link)
                        <a href="{{ $link['url'] }}" class="rounded-2xl border border-white/10 bg-slate-950/45 px-5 py-3 text-sm font-black text-white/75 transition hover:-translate-y-0.5 hover:border-cyan-300/30 hover:text-cyan-100">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </div>
        </section>

        @php
            $businessSections = [
                ['testid' => 'homepage-top-businesses', 'title' => 'Топ изпълнители', 'subtitle' => 'Подредени по план, потвърждение, рейтинг, препоръки и активност.', 'items' => $topBusinesses, 'url' => route('top.businesses')],
                ['testid' => 'homepage-most-recommended', 'title' => 'Най-препоръчвани', 'subtitle' => 'Изпълнители, които клиенти препоръчват с един клик.', 'items' => $mostRecommendedBusinesses, 'url' => route('top.businesses')],
                ['testid' => 'homepage-verified-businesses', 'title' => 'Проверени изпълнители', 'subtitle' => 'Профили с отделен badge за потвърдение.', 'items' => $verifiedBusinesses, 'url' => route('businesses.index', ['verified' => 1])],
                ['testid' => 'homepage-newest-businesses', 'title' => 'Най-нови изпълнители', 'subtitle' => 'Свежи active и trial профили във FixNow.', 'items' => $newestBusinesses, 'url' => route('businesses.index')],
            ];
        @endphp

        @foreach($businessSections as $section)
            <section data-testid="{{ $section['testid'] }}" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black">{{ $section['title'] }}</h2>
                        <p class="mt-2 text-sm text-white/55">{{ $section['subtitle'] }}</p>
                    </div>
                    <a href="{{ $section['url'] }}" class="hidden text-sm font-bold text-blue-300 transition hover:text-cyan-200 sm:block">Виж всички →</a>
                </div>

                @if($section['items']->isNotEmpty())
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($section['items']->take(4) as $business)
                            @include('partials.business-card', ['business' => $business])
                        @endforeach
                    </div>
                @else
                    <div class="fn-hover-lift rounded-3xl border border-white/10 bg-white/10 p-6 text-center shadow-xl shadow-black/20 backdrop-blur-xl">
                        <h3 class="text-xl font-black">Очакваме първите активни профили</h3>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Когато има реални профили на изпълнители, тази секция ще се попълва автоматично без фалшиви карти.</p>
                    </div>
                @endif
            </section>
        @endforeach

        <section data-testid="homepage-popular-categories" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black">Популярни категории</h2>
                    <p class="mt-2 text-sm text-white/55">Базирани на реално публикувани профили на изпълнители и услуги.</p>
                </div>
                <a href="{{ url('/categories') }}" class="hidden text-sm font-bold text-blue-300 transition hover:text-cyan-200 sm:block">Всички категории →</a>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-8">
                @forelse($popularCategories as $category)
                    <a href="{{ route('businesses.index', ['category' => $category['name']]) }}" class="fn-category-card rounded-3xl border border-white/10 bg-white/10 p-5 shadow-lg shadow-black/20 backdrop-blur-xl">
                        <p class="text-lg font-black">{{ $category['name'] }}</p>
                        <p class="mt-2 text-sm text-white/55">{{ $category['count'] }} активни</p>
                    </a>
                @empty
                    @foreach($categoryFallbacks as $category)
                        <a href="{{ route('businesses.index', ['category' => $category]) }}" class="fn-category-card rounded-3xl border border-white/10 bg-white/10 p-5 shadow-lg shadow-black/20 backdrop-blur-xl">
                            <p class="text-lg font-black">{{ $category }}</p>
                            <p class="mt-2 text-sm text-white/55">Очаква данни</p>
                        </a>
                    @endforeach
                @endforelse
            </div>
        </section>

        <section data-testid="homepage-latest-reviews" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="mb-4">
                <h2 class="text-2xl font-black">Последни отзиви</h2>
                <p class="mt-2 text-sm text-white/55">Показват се само одобрени мнения към публично видими изпълнители.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse($latestReviews as $review)
                    <article class="fn-hover-lift rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-black">{{ $review->reviewer_name }}</p>
                                <p class="mt-1 text-sm text-amber-200">{{ str_repeat('★', (int) $review->rating) }} <span class="text-white/45">· {{ $review->rating }}/5</span></p>
                            </div>
                            @if($review->business)
                                <a href="{{ route('businesses.show', $review->business) }}" class="text-xs font-black text-cyan-200 hover:text-white">Профил</a>
                            @endif
                        </div>
                        <p class="mt-4 line-clamp-3 text-sm leading-6 text-white/70">{{ $review->comment }}</p>
                        <p class="mt-4 text-xs text-white/45">{{ $review->business?->business_name ?: $review->business?->name }}</p>
                    </article>
                @empty
                    <div class="fn-hover-lift rounded-3xl border border-white/10 bg-white/10 p-6 text-center shadow-xl shadow-black/20 backdrop-blur-xl md:col-span-2 xl:col-span-4">
                        <h3 class="text-xl font-black">Все още няма одобрени отзиви</h3>
                        <p class="mt-2 text-sm text-white/60">След първите реални мнения те ще се показват тук автоматично.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section data-testid="homepage-first-50" class="fn-section-fade mx-auto max-w-[1500px] px-4 py-10 sm:px-6 lg:px-12">
            <div class="fn-hover-lift grid gap-8 overflow-hidden rounded-[34px] border border-violet-300/20 bg-gradient-to-br from-blue-500/15 via-cyan-400/10 to-fuchsia-600/15 p-6 shadow-2xl shadow-blue-950/25 backdrop-blur-2xl lg:grid-cols-[1fr_auto] lg:items-center lg:p-10">
                <div>
                    <p class="text-sm font-black uppercase text-cyan-200/80">За изпълнители</p>
                    <h2 class="mt-3 max-w-3xl text-3xl font-black leading-tight sm:text-4xl">Първите 50 изпълнители получават стартово предимство</h2>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-white/70">Създайте професионален профил, споделете го с клиентите си и започнете да събирате преглеждания, кликове, отзиви и препоръки.</p>
                </div>
                <a href="{{ route('business.landing') }}" class="fn-cta-glow rounded-2xl bg-gradient-to-r from-blue-500 to-fuchsia-600 px-8 py-4 text-center text-base font-black text-white shadow-xl shadow-blue-600/25">Стани изпълнител</a>
            </div>
        </section>
    </main>

    @include('partials.public-footer')

    {{--
    <footer class="hidden border-t border-white/10 bg-[#030712]/85">
        <div class="mx-auto grid max-w-[1500px] gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.3fr_1fr_1fr_1fr] lg:px-12">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black">F</div>
                    <span class="text-xl font-black">FixNow.bg</span>
                </div>
                <p class="mt-4 max-w-md text-sm leading-7 text-white/60">Premium marketplace за локални услуги, изпълнители, сервизи, майстори и професионалисти.</p>
            </div>
            <div class="grid gap-3 text-sm text-white/60">
                <p class="font-black text-white">Навигация</p>
                <a href="{{ route('top.businesses') }}" class="hover:text-cyan-200">Топ изпълнители</a>
                <a href="{{ route('services.index') }}" class="hover:text-cyan-200">Услуги</a>
                <a href="{{ route('businesses.index') }}" class="hover:text-cyan-200">Изпълнители</a>
            </div>
            <div class="grid gap-3 text-sm text-white/60">
                <p class="font-black text-white">Изпълнители</p>
                <a href="{{ route('business.landing') }}" class="hover:text-cyan-200">За изпълнители</a>
                <a href="{{ route('request.service') }}" class="hover:text-cyan-200">Заяви оферта</a>
                <a href="{{ url('/contact') }}" class="hover:text-cyan-200">Контакт</a>
            </div>
            <div class="grid gap-3 text-sm text-white/60">
                <p class="font-black text-white">Профил</p>
                @guest
                    <a href="{{ route('login') }}" class="hover:text-cyan-200">Вход</a>
                    <a href="{{ route('register') }}" class="hover:text-cyan-200">Регистрация</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-cyan-200">Табло</a>
                @endauth
            </div>
        </div>
        <div class="border-t border-white/10 px-4 py-5 text-center text-xs text-white/40">© {{ date('Y') }} FixNow.bg. Всички права запазени.</div>
    </footer>

    --}}
    @include('partials.mobile-bottom-nav')
</body>
</html>

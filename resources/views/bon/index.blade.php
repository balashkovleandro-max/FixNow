@php
    $navItems = [
        ['label' => 'Начало', 'href' => route('home')],
        ['label' => 'За бизнеси', 'href' => route('business.landing')],
        ['label' => 'Фрилансъри', 'href' => route('bon.freelancers')],
        ['label' => 'Инструменти', 'href' => route('bon.tools')],
        ['label' => 'Планове', 'href' => route('plans')],
    ];

    $businessTools = [
        [
            'title' => 'Финансов анализ',
            'text' => 'Въведи оборот, разходи, персонал, наем и маркетинг, за да видиш реална печалба, маржове и къде бизнесът губи пари.',
            'icon' => '⌁',
            'color' => 'from-blue-500 to-cyan-400',
        ],
        [
            'title' => 'Калкулатор “Колко клиенти ми трябват?”',
            'text' => 'Изчислява колко клиенти или поръчки са нужни на месец, за да покриеш разходите и да постигнеш желаната печалба.',
            'icon' => '◎',
            'color' => 'from-violet-500 to-purple-500',
        ],
        [
            'title' => 'Visibility Score',
            'text' => 'Показва колко добре е представен бизнесът онлайн — снимки, описание, услуги, градове, отзиви, активност и Premium видимост.',
            'icon' => '◈',
            'color' => 'from-fuchsia-500 to-pink-500',
        ],
        [
            'title' => 'Калкулатор за ценообразуване',
            'text' => 'Помага да разбереш дали дадена услуга или продукт е на правилна цена според себестойност, време, труд и желан марж.',
            'icon' => '▤',
            'color' => 'from-cyan-400 to-emerald-400',
        ],
        [
            'title' => 'Репутация и отзиви',
            'text' => 'Помага на бизнеса да събира повече отзиви, да следи средния рейтинг и да разбира какво влияе на доверието.',
            'icon' => '✦',
            'color' => 'from-blue-600 to-violet-600',
        ],
        [
            'title' => 'Месечен бизнес доклад',
            'text' => 'Обобщава резултатите за месеца — видимост, профил, финанси, отзиви и конкретни следващи стъпки.',
            'icon' => '↗',
            'color' => 'from-violet-600 to-fuchsia-500',
        ],
    ];

    $plans = [
        [
            'name' => 'Standard',
            'price' => '18.99 €',
            'note' => 'на месец',
            'positioning' => 'За бизнеси, които искат професионално онлайн присъствие и базови инструменти за видимост.',
            'features' => [
                'Професионален BON профил',
                'Visibility Score за онлайн присъствието',
                'Основен финансов анализ',
                'Business Health Score',
                'Базови статистики за профила',
                'Отзиви и репутация',
                'Месечен mini report',
                'Основни препоръки за подобрение',
                'Достъп до базовите BON инструменти',
            ],
            'highlight' => false,
        ],
        [
            'name' => 'Premium',
            'price' => '24.99 €',
            'note' => 'на месец',
            'positioning' => 'За бизнеси, които искат повече анализ, по-добра видимост и конкретни препоръки за растеж.',
            'features' => [
                'Всичко от Standard',
                'Разширен финансов анализ',
                'По-подробен Business Health Score',
                'Калкулатор “Колко клиенти ми трябват?”',
                'Калкулатор за ценообразуване',
                'Разширени статистики',
                'Месечен бизнес доклад',
                'Premium препоръки за растеж',
                'Premium / Препоръчан badge',
                'По-добра видимост в BON',
                'Приоритетна поддръжка',
                'Подготвена логика за бъдещи AI/business advisor препоръки',
            ],
            'highlight' => true,
        ],
    ];

    $recommendedSpecialists = $recommendedSpecialists ?? collect();
    $smartJobs = $smartJobs ?? collect();
    $smartCategory = $smartCategory ?? request('category');
    $smartCategories = $smartCategories ?? [
        'Web Design',
        'Development',
        'Marketing',
        'Ремонти',
        'Почистване',
        'Красота',
        'Ресторанти',
        'Хотели',
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BON | Бизнес видимост, анализ и растеж</title>
    <meta name="description" content="BON помага на локалните бизнеси да изградят по-силно онлайн присъствие, да следят ключови показатели, да подобряват репутацията си и да взимат по-добри решения за растеж.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')

    <style>
        .bon-grid {
            background-image:
                linear-gradient(to right, rgba(37, 99, 235, .075) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, .075) 1px, transparent 1px);
            background-size: 72px 72px;
            mask-image: radial-gradient(circle at 50% 24%, black 0%, transparent 78%);
        }

        .bon-dot-field {
            background-image: radial-gradient(rgba(37, 99, 235, .34) 1.4px, transparent 1.4px);
            background-size: 16px 16px;
        }

        .bon-float {
            animation: bon-float 7s ease-in-out infinite;
        }

        .bon-pulse {
            animation: bon-pulse 6s ease-in-out infinite;
        }

        .bon-globe-stage {
            position: relative;
            display: flex;
            height: 360px;
            min-height: 360px;
            align-items: center;
            justify-content: center;
        }

        .bon-globe-glow {
            position: absolute;
            height: 310px;
            width: 310px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(96, 165, 250, .38), rgba(124, 58, 237, .24) 42%, rgba(236, 72, 153, .18) 72%, transparent 100%);
            filter: blur(34px);
        }

        .bon-globe-shell {
            position: absolute;
            height: 285px;
            width: 285px;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .86);
            background: radial-gradient(circle at 35% 20%, rgba(255, 255, 255, .78), rgba(255, 255, 255, .24) 44%, rgba(124, 58, 237, .08) 100%);
            box-shadow: 0 34px 90px rgba(79, 70, 229, .16), inset 0 1px 18px rgba(255, 255, 255, .58);
            backdrop-filter: blur(18px);
        }

        .bon-globe-ring {
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
        }

        .bon-globe-ring-outer {
            height: 305px;
            width: 305px;
            background: conic-gradient(from 0deg, transparent 0 16%, rgba(37, 99, 235, .54) 24%, transparent 35% 52%, rgba(236, 72, 153, .58) 61%, transparent 72% 100%);
            mask: radial-gradient(circle, transparent 66%, black 67% 70%, transparent 71%);
            animation: bon-spin 16s linear infinite;
        }

        .bon-globe-ring-inner {
            height: 235px;
            width: 235px;
            background: conic-gradient(from 90deg, transparent 0 18%, rgba(34, 211, 238, .48) 27%, transparent 40% 58%, rgba(139, 92, 246, .5) 67%, transparent 80% 100%);
            mask: radial-gradient(circle, transparent 64%, black 65% 69%, transparent 70%);
            animation: bon-spin-reverse 22s linear infinite;
        }

        .bon-globe-core {
            position: relative;
            display: grid;
            height: 210px;
            width: 210px;
            place-items: center;
            overflow: hidden;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .52);
            background:
                radial-gradient(circle at 32% 22%, rgba(255, 255, 255, .88) 0 8%, transparent 18%),
                radial-gradient(circle at 65% 72%, rgba(236, 72, 153, .72), transparent 35%),
                radial-gradient(circle at 30% 70%, rgba(34, 211, 238, .55), transparent 35%),
                linear-gradient(135deg, #2563eb 0%, #5b5ff0 42%, #8b5cf6 68%, #ec4899 100%);
            box-shadow: 0 24px 70px rgba(79, 70, 229, .36), inset 0 18px 34px rgba(255, 255, 255, .26), inset 0 -28px 44px rgba(30, 41, 59, .2);
        }

        .bon-globe-core::before {
            content: "";
            position: absolute;
            inset: 16px;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .36);
            background:
                linear-gradient(90deg, transparent 45%, rgba(255, 255, 255, .22) 50%, transparent 55%),
                linear-gradient(0deg, transparent 44%, rgba(255, 255, 255, .16) 50%, transparent 56%),
                repeating-radial-gradient(ellipse at center, transparent 0 18px, rgba(255, 255, 255, .13) 19px 20px, transparent 21px 42px);
            opacity: .72;
            animation: bon-spin 18s linear infinite;
        }

        .bon-globe-core::after {
            content: "";
            position: absolute;
            left: 34px;
            top: 26px;
            height: 44px;
            width: 86px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, .42);
            filter: blur(8px);
            transform: rotate(-18deg);
        }

        .bon-globe-letter {
            position: relative;
            z-index: 2;
            color: white;
            font-size: 5.4rem;
            font-weight: 950;
            line-height: 1;
            text-shadow: 0 12px 28px rgba(15, 23, 42, .32);
        }

        .bon-globe-halo {
            position: absolute;
            bottom: 30px;
            left: 50%;
            height: 34px;
            width: 235px;
            transform: translateX(-50%);
            border-radius: 9999px;
            background: radial-gradient(ellipse, rgba(139, 92, 246, .34), rgba(34, 211, 238, .16) 48%, transparent 74%);
            filter: blur(10px);
        }

        .bon-globe-halo-ring {
            position: absolute;
            bottom: 48px;
            left: 50%;
            height: 44px;
            width: 260px;
            transform: translateX(-50%);
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .72);
            box-shadow: 0 0 28px rgba(124, 58, 237, .2);
        }

        .bon-connection-line {
            position: absolute;
            top: 50%;
            height: 1px;
            width: min(21vw, 260px);
            transform: translateY(-50%);
            opacity: .78;
        }

        .bon-connection-line-left {
            right: calc(50% + 116px);
            background: linear-gradient(to left, rgba(37, 99, 235, .52), rgba(37, 99, 235, .12), transparent);
        }

        .bon-connection-line-right {
            left: calc(50% + 116px);
            background: linear-gradient(to right, rgba(236, 72, 153, .48), rgba(139, 92, 246, .16), transparent);
        }

        .bon-connection-dot {
            position: absolute;
            top: 50%;
            height: 11px;
            width: 11px;
            transform: translateY(-50%);
            border-radius: 9999px;
        }

        .bon-orbit-card {
            position: absolute;
            z-index: 4;
            display: flex;
            min-width: 12rem;
            align-items: center;
            gap: .75rem;
            border: 1px solid rgba(255, 255, 255, .76);
            border-radius: 1.35rem;
            background: rgba(255, 255, 255, .76);
            padding: .75rem .9rem;
            box-shadow: 0 22px 60px rgba(30, 64, 175, .10);
            backdrop-filter: blur(22px);
            animation: bon-card-drift 8s ease-in-out infinite;
        }

        .bon-orbit-icon {
            display: grid;
            height: 2.35rem;
            width: 2.35rem;
            flex-shrink: 0;
            place-items: center;
            border-radius: 1rem;
            color: white;
            font-weight: 900;
            box-shadow: 0 14px 34px rgba(79, 70, 229, .18);
        }

        .bon-orbit-status {
            margin-left: auto;
            height: .55rem;
            width: .55rem;
            flex-shrink: 0;
            border-radius: 9999px;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, .72);
        }

        .bon-orbit-line {
            position: absolute;
            z-index: 1;
            height: 1px;
            overflow: hidden;
            border-radius: 9999px;
            opacity: .72;
            filter: drop-shadow(0 0 10px rgba(99, 102, 241, .24));
        }

        .bon-orbit-line::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .96), transparent);
            animation: bon-flow 3.8s linear infinite;
        }

        .bon-modal-open {
            overflow: hidden;
        }

        @keyframes bon-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes bon-pulse {
            0%, 100% { opacity: .72; transform: scale(.98); }
            50% { opacity: 1; transform: scale(1.04); }
        }

        @keyframes bon-spin {
            to { transform: rotate(360deg); }
        }

        @keyframes bon-spin-reverse {
            to { transform: rotate(-360deg); }
        }

        @keyframes bon-flow {
            from { transform: translateX(-100%); }
            to { transform: translateX(100%); }
        }

        @keyframes bon-card-drift {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @media (max-width: 1023px) {
            .bon-globe-stage {
                height: 290px;
                min-height: 290px;
            }

            .bon-globe-glow,
            .bon-globe-ring-outer {
                height: 245px;
                width: 245px;
            }

            .bon-globe-shell {
                height: 225px;
                width: 225px;
            }

            .bon-globe-ring-inner {
                height: 185px;
                width: 185px;
            }

            .bon-globe-core {
                height: 168px;
                width: 168px;
            }

            .bon-globe-letter {
                font-size: 4.1rem;
            }

            .bon-connection-line,
            .bon-connection-dot,
            .bon-orbit-card,
            .bon-orbit-line {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .bon-grid {
                background-size: 46px 46px;
                opacity: .24;
            }

            .bon-globe-stage {
                height: 235px;
                min-height: 235px;
                margin-inline: auto;
                max-width: 100%;
            }

            .bon-globe-glow,
            .bon-globe-ring-outer {
                height: 210px;
                width: 210px;
            }

            .bon-globe-shell {
                height: 198px;
                width: 198px;
            }

            .bon-globe-ring-inner {
                height: 158px;
                width: 158px;
            }

            .bon-globe-core {
                height: 138px;
                width: 138px;
            }

            .bon-globe-letter {
                font-size: 3.45rem;
            }

            .bon-globe-halo {
                bottom: 20px;
                width: 170px;
            }

            .bon-globe-halo-ring {
                bottom: 35px;
                height: 32px;
                width: 190px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .bon-float,
            .bon-pulse,
            .bon-globe-ring-outer,
            .bon-globe-ring-inner,
            .bon-globe-core::before,
            .bon-orbit-card,
            .bon-orbit-line::after {
                animation: none;
            }
        }
    </style>
</head>

<body class="antialiased">
    <main class="relative min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.98)_0%,rgba(248,250,255,.78)_42%,rgba(248,250,255,1)_100%)]"></div>
        <div class="bon-grid pointer-events-none absolute inset-0 opacity-[.38]"></div>
        <div class="pointer-events-none absolute -top-40 left-[-12rem] h-[35rem] w-[35rem] rounded-full bg-blue-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-40 right-[-10rem] h-[35rem] w-[35rem] rounded-full bg-fuchsia-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[24rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-18rem] left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="bon-dot-field pointer-events-none absolute left-6 top-56 hidden h-40 w-36 opacity-30 lg:block"></div>
        <div class="bon-dot-field pointer-events-none absolute bottom-10 right-6 hidden h-40 w-36 opacity-25 lg:block" style="background-image: radial-gradient(rgba(236,72,153,.36) 1.4px, transparent 1.4px);"></div>

        <div class="relative z-10 px-4 pb-12 sm:px-6 sm:pb-16 lg:px-8">
            <header class="mx-auto mt-4 max-w-[1440px]">
                <div class="flex min-h-[68px] items-center justify-between rounded-[1.5rem] border border-white/70 bg-white/75 px-3 py-3 shadow-[0_24px_80px_rgba(30,41,100,.09)] backdrop-blur-2xl sm:min-h-[76px] sm:rounded-[1.75rem] sm:px-6">
                    <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 sm:gap-4">
                        <span class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25 sm:h-[52px] sm:w-[52px] sm:text-2xl">
                            <span class="absolute inset-0 rounded-2xl bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,.42),transparent_38%)]"></span>
                            <span class="relative z-10">B</span>
                        </span>
                        <span class="min-w-0 leading-tight">
                            <span class="block text-xl font-black tracking-tight text-[#070B1F] sm:text-[23px]">BON</span>
                            <span class="hidden truncate text-sm font-medium text-slate-500 sm:block">Business Operating Network</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-9 lg:flex">
                        @foreach ($navItems as $item)
                            <a href="{{ $item['href'] }}" class="text-[15px] font-semibold text-[#11183B] transition hover:-translate-y-0.5 hover:text-blue-600">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('login') }}" class="hidden rounded-2xl border border-slate-200/80 bg-white/70 px-5 py-3 text-sm font-bold text-[#070B1F] shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:inline-flex">
                            Вход
                        </a>
                        <a href="{{ route('register') }}" data-track="cta_business_signup" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-3.5 py-2.5 text-sm font-bold text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35 sm:px-6 sm:py-3">
                            Регистрация
                        </a>
                    </div>
                </div>
            </header>

            <section class="mx-auto max-w-[1440px] pt-9 sm:pt-12 lg:pt-16">
                <div class="mx-auto max-w-5xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-200/70 bg-white/80 px-3.5 py-2 text-xs font-semibold text-blue-700 shadow-sm shadow-blue-900/5 backdrop-blur-xl sm:px-4 sm:text-sm">
                        <span class="text-violet-600">✦</span>
                        BON Business OS
                    </div>

                    <h1 class="mx-auto mt-5 max-w-5xl text-[34px] font-black leading-[1.08] tracking-[-0.035em] text-[#070B1F] sm:mt-6 sm:text-[60px] sm:tracking-[-0.055em] lg:text-[76px]">
                        Управлявай <span class="bg-gradient-to-r from-blue-600 to-violet-600 bg-clip-text text-transparent">видимостта</span>, финансите и <span class="bg-gradient-to-r from-fuchsia-500 to-pink-500 bg-clip-text text-transparent">растежа</span> на бизнеса си от едно място.
                    </h1>

                    <p class="mx-auto mt-5 max-w-3xl text-base leading-7 text-slate-600 sm:mt-6 sm:text-xl sm:leading-8">
                        BON помага на локалните бизнеси да изградят по-силно онлайн присъствие, да следят ключови показатели,
                        да подобряват репутацията си и да взимат по-добри решения за растеж.
                    </p>

                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 sm:w-auto">
                            Добави своя бизнес
                        </a>
                        <a href="{{ route('plans') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:w-auto">
                            Виж плановете
                        </a>
                    </div>

                    <a href="#bon-tools" class="mt-5 inline-flex text-sm font-black text-blue-700 transition hover:text-violet-700">
                        Виж инструментите в BON →
                    </a>
                </div>

                <div class="relative mx-auto mt-6 max-w-full overflow-hidden sm:mt-12 sm:max-w-5xl">
                    <div class="bon-connection-line bon-connection-line-left hidden lg:block"></div>
                    <div class="bon-connection-line bon-connection-line-right hidden lg:block"></div>
                    <div class="bon-connection-dot left-[calc(50%_-_176px)] hidden bg-blue-400 shadow-lg shadow-blue-400/40 lg:block"></div>
                    <div class="bon-connection-dot right-[calc(50%_-_176px)] hidden bg-fuchsia-400 shadow-lg shadow-fuchsia-400/40 lg:block"></div>

                    <div class="bon-orbit-line hidden lg:block" style="left: 8%; top: 25%; width: 31%; transform: rotate(8deg); background: linear-gradient(90deg, rgba(37,99,235,.08), rgba(37,99,235,.48), rgba(37,99,235,.04));"></div>
                    <div class="bon-orbit-line hidden lg:block" style="left: 12%; top: 52%; width: 28%; transform: rotate(-2deg); background: linear-gradient(90deg, rgba(14,165,233,.08), rgba(14,165,233,.45), rgba(14,165,233,.04));"></div>
                    <div class="bon-orbit-line hidden lg:block" style="left: 18%; top: 74%; width: 24%; transform: rotate(-12deg); background: linear-gradient(90deg, rgba(79,70,229,.08), rgba(79,70,229,.38), rgba(79,70,229,.04));"></div>
                    <div class="bon-orbit-line hidden lg:block" style="right: 8%; top: 27%; width: 31%; transform: rotate(-8deg); background: linear-gradient(90deg, rgba(236,72,153,.04), rgba(236,72,153,.48), rgba(236,72,153,.08));"></div>
                    <div class="bon-orbit-line hidden lg:block" style="right: 12%; top: 52%; width: 28%; transform: rotate(2deg); background: linear-gradient(90deg, rgba(139,92,246,.04), rgba(139,92,246,.45), rgba(139,92,246,.08));"></div>
                    <div class="bon-orbit-line hidden lg:block" style="right: 18%; top: 74%; width: 24%; transform: rotate(12deg); background: linear-gradient(90deg, rgba(34,211,238,.04), rgba(236,72,153,.34), rgba(236,72,153,.08));"></div>

                    <div class="bon-orbit-card left-0 top-3 hidden lg:flex" style="animation-delay: -0.3s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-blue-600 to-cyan-400">↗</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Нови клиенти</span>
                            <span class="block text-xs font-semibold text-slate-500">повече интерес</span>
                        </span>
                        <span class="bon-orbit-status bg-emerald-400"></span>
                    </div>

                    <div class="bon-orbit-card left-10 top-[38%] hidden lg:flex" style="animation-delay: -1.4s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-sky-500 to-blue-600">✉</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Запитвания</span>
                            <span class="block text-xs font-semibold text-slate-500">реални сигнали</span>
                        </span>
                        <span class="bon-orbit-status bg-blue-400"></span>
                    </div>

                    <div class="bon-orbit-card bottom-12 left-4 hidden lg:flex" style="animation-delay: -2.1s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-violet-500 to-blue-500">◌</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Профилна видимост</span>
                            <span class="block text-xs font-semibold text-slate-500">ясно представяне</span>
                        </span>
                        <span class="bon-orbit-status bg-violet-400"></span>
                    </div>

                    <div class="bon-orbit-card left-[18%] top-[14%] hidden xl:flex" style="animation-delay: -3s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-cyan-400 to-emerald-400">✓</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Проверен бизнес</span>
                            <span class="block text-xs font-semibold text-slate-500">доверие</span>
                        </span>
                        <span class="bon-orbit-status bg-emerald-400"></span>
                    </div>

                    <div class="bon-globe-stage bon-float">
                        <div class="bon-globe-glow bon-pulse"></div>
                        <div class="bon-globe-halo"></div>
                        <div class="bon-globe-halo-ring"></div>
                        <div class="bon-globe-shell"></div>
                        <div class="bon-globe-ring bon-globe-ring-outer"></div>
                        <div class="bon-globe-ring bon-globe-ring-inner"></div>
                        <div class="bon-globe-core">
                            <span class="bon-globe-letter">B</span>
                        </div>
                    </div>

                    <div class="bon-orbit-card right-0 top-6 hidden lg:flex" style="animation-delay: -0.8s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-fuchsia-500 to-pink-500">★</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Premium позициониране</span>
                            <span class="block text-xs font-semibold text-slate-500">по-силна видимост</span>
                        </span>
                        <span class="bon-orbit-status bg-pink-400"></span>
                    </div>

                    <div class="bon-orbit-card right-10 top-[39%] hidden lg:flex" style="animation-delay: -1.8s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-violet-600 to-fuchsia-500">⌁</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Бизнес анализ</span>
                            <span class="block text-xs font-semibold text-slate-500">ключови показатели</span>
                        </span>
                        <span class="bon-orbit-status bg-violet-400"></span>
                    </div>

                    <div class="bon-orbit-card bottom-14 right-4 hidden lg:flex" style="animation-delay: -2.7s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-pink-500 to-rose-500">☎</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Повече обаждания</span>
                            <span class="block text-xs font-semibold text-slate-500">по-лесен контакт</span>
                        </span>
                        <span class="bon-orbit-status bg-rose-400"></span>
                    </div>

                    <div class="bon-orbit-card right-[18%] top-[15%] hidden xl:flex" style="animation-delay: -3.4s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-cyan-400 to-blue-500">↗</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Месечен доклад</span>
                            <span class="block text-xs font-semibold text-slate-500">ясни следващи стъпки</span>
                        </span>
                        <span class="bon-orbit-status bg-cyan-300"></span>
                    </div>
                </div>
            </section>

            <section id="bon-tools" class="mx-auto mt-12 max-w-[1440px] sm:mt-16">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">BON Tools</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Инструменти в BON за анализ, видимост, доверие и растеж.</h2>
                    <p class="mt-4 max-w-3xl text-base leading-8 text-slate-600">
                        Практични бизнес инструменти, които помагат да виждаш числата, профила, репутацията и следващите действия по-ясно.
                    </p>
                </div>

                <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($businessTools as $tool)
                        @php
                            $modalId = 'bon-tool-modal-' . $loop->index;
                        @endphp
                        <button type="button" data-tool-open="{{ $modalId }}" class="group w-full rounded-[1.65rem] border border-white/70 bg-white/75 p-5 text-left shadow-2xl shadow-blue-900/5 backdrop-blur-2xl transition hover:-translate-y-1 hover:border-blue-200/80 hover:shadow-blue-900/10 sm:rounded-[2rem] sm:p-6">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br {{ $tool['color'] }} text-xl font-black text-white shadow-lg shadow-violet-500/20">
                                {{ $tool['icon'] }}
                            </div>
                            <h3 class="text-xl font-black tracking-tight">{{ $tool['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $tool['text'] }}</p>
                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-black text-blue-700 transition group-hover:text-violet-700">
                                Отвори инструмент
                                <span class="transition group-hover:translate-x-1">→</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                @foreach ($businessTools as $tool)
                    @php
                        $modalId = 'bon-tool-modal-' . $loop->index;
                    @endphp
                    <div id="{{ $modalId }}" data-tool-modal class="fixed inset-0 z-[100] hidden px-3 py-3 sm:px-6 sm:py-5" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="{{ $modalId }}-title">
                        <button type="button" data-tool-overlay class="absolute inset-0 bg-slate-950/45 opacity-0 backdrop-blur-md transition-opacity duration-300" aria-label="Затвори"></button>

                        <div class="relative z-10 mx-auto flex min-h-full max-w-3xl items-center justify-center">
                            <div data-tool-panel class="max-h-[min(calc(100vh-1.5rem),48rem)] w-full translate-y-4 scale-95 overflow-y-auto rounded-[1.5rem] border border-white/70 bg-white/[0.94] p-4 opacity-0 shadow-2xl shadow-blue-950/20 backdrop-blur-2xl transition duration-300 sm:rounded-[2rem] sm:p-7">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-16 w-16 items-center justify-center rounded-[1.35rem] bg-gradient-to-br {{ $tool['color'] }} text-3xl font-black text-white shadow-xl shadow-violet-500/20">
                                            {{ $tool['icon'] }}
                                        </div>
                                        <h3 id="{{ $modalId }}-title" class="mt-5 text-2xl font-black tracking-tight text-[#070B1F] sm:text-3xl">{{ $tool['title'] }}</h3>
                                        <p class="mt-3 text-sm leading-7 text-slate-600 sm:text-base">{{ $tool['text'] }}</p>
                                    </div>

                                    <button type="button" data-tool-close class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/80 text-xl font-black text-slate-500 shadow-sm transition hover:-translate-y-0.5 hover:text-[#070B1F]" aria-label="Затвори">
                                        ×
                                    </button>
                                </div>

                                <div class="mt-7 rounded-[1.5rem] border border-slate-200/70 bg-slate-50/70 p-4 sm:p-5">
                                    @switch($loop->index)
                                        @case(0)
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Оборот за месеца<input type="text" inputmode="decimal" placeholder="напр. 24 000 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Общи разходи<input type="text" inputmode="decimal" placeholder="напр. 17 400 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Персонал<input type="text" inputmode="decimal" placeholder="напр. 8 200 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Маркетинг<input type="text" inputmode="decimal" placeholder="напр. 1 000 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                            </div>
                                            @break

                                        @case(1)
                                            <div class="grid gap-4 sm:grid-cols-3">
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Месечни разходи<input type="text" inputmode="decimal" placeholder="18 000 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-violet-300 focus:ring-4 focus:ring-violet-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Желана печалба<input type="text" inputmode="decimal" placeholder="5 000 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-violet-300 focus:ring-4 focus:ring-violet-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Средна поръчка<input type="text" inputmode="decimal" placeholder="85 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-violet-300 focus:ring-4 focus:ring-violet-100"></label>
                                            </div>
                                            @break

                                        @case(2)
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                @foreach (['Снимки и визуално представяне', 'Ясно описание на бизнеса', 'Услуги и цени', 'Градове и локации', 'Отзиви и рейтинг', 'Premium видимост'] as $item)
                                                    <label class="flex items-center gap-3 rounded-2xl border border-white/80 bg-white/80 px-4 py-3 text-sm font-bold text-slate-700 shadow-sm">
                                                        <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                        {{ $item }}
                                                    </label>
                                                @endforeach
                                            </div>
                                            @break

                                        @case(3)
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Себестойност<input type="text" inputmode="decimal" placeholder="45 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Време за изпълнение<input type="text" placeholder="2 часа" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Разход за труд<input type="text" inputmode="decimal" placeholder="60 лв" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Желан марж<input type="text" inputmode="decimal" placeholder="30%" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100"></label>
                                            </div>
                                            @break

                                        @case(4)
                                            <div class="grid gap-4 sm:grid-cols-3">
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Среден рейтинг<input type="text" inputmode="decimal" placeholder="4.7" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Нови отзиви<input type="text" inputmode="numeric" placeholder="12" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                                <label class="grid gap-2 text-sm font-bold text-slate-700">Сигнали за реакция<input type="text" inputmode="numeric" placeholder="2" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></label>
                                            </div>
                                            @break

                                        @default
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                @foreach (['Видимост и профил', 'Финансови показатели', 'Отзиви и доверие', 'Следващи действия'] as $item)
                                                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 shadow-sm">
                                                        <p class="text-sm font-black text-[#070B1F]">{{ $item }}</p>
                                                        <p class="mt-2 text-sm leading-6 text-slate-500">Кратък месечен сигнал с ясно действие за подобрение.</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                    @endswitch
                                </div>

                                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-sm leading-6 text-slate-500">Това е визуален пример на инструмента. Пълната логика се отваря в бизнес профила.</p>
                                    <button type="button" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r {{ $tool['color'] }} px-6 text-sm font-black text-white shadow-xl shadow-violet-500/20 transition hover:-translate-y-0.5 sm:w-auto">
                                        {{ in_array($loop->index, [0, 1, 3], true) ? 'Изчисли' : ($loop->index === 4 ? 'Запази' : 'Виж резултат') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>

            <section id="recommended-specialists" class="mx-auto mt-12 max-w-[1440px] sm:mt-16">
                <div class="rounded-[2rem] border border-white/70 bg-white/75 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7 lg:p-8">
                    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-end">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Smart BON Home</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-4xl">Препоръчани специалисти според нуждата.</h2>
                            <p class="mt-4 max-w-3xl text-base leading-8 text-slate-600">
                                Избери категория и виж доверени бизнеси, фрийлансъри и активни обяви в същия контекст.
                                Подреждането дава предимство на Premium профили, Trust Score, рейтинг, завършени проекти и активност.
                            </p>
                        </div>

                        <form action="{{ route('home') }}#recommended-specialists" method="GET" class="rounded-[1.5rem] border border-white/80 bg-white/75 p-4 shadow-xl shadow-blue-900/5">
                            <label class="text-sm font-black text-slate-700" for="bon-smart-category">Категория</label>
                            <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_auto]">
                                <select id="bon-smart-category" name="category" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                    <option value="">Всички категории</option>
                                    @foreach($smartCategories as $category)
                                        <option value="{{ $category }}" @selected($smartCategory === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                                <button class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/20 transition hover:-translate-y-0.5">
                                    Покажи
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-7 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @forelse($recommendedSpecialists as $recommendedProfile)
                            @php
                                $profileName = $recommendedProfile->business_name ?: $recommendedProfile->name;
                                $profileType = ($recommendedProfile->specialist_type ?? $recommendedProfile->role) === 'freelancer' ? 'Фрийлансър' : 'Бизнес';
                                $profileUrl = ($recommendedProfile->specialist_type ?? $recommendedProfile->role) === 'freelancer'
                                    ? route('freelancers.show', $recommendedProfile)
                                    : route('businesses.show', $recommendedProfile);
                                $trustSummary = $recommendedProfile->trust_summary ?? [];
                                $trustScore = $trustSummary['trust_score'] ?? $recommendedProfile->trust_score ?? 0;
                                $rating = $trustSummary['average_rating'] ?? null;
                                $completed = $trustSummary['completed_projects_count'] ?? $recommendedProfile->trust_completed_projects_count ?? 0;
                                $completeness = $trustSummary['profile_completeness'] ?? null;
                                $profileInitial = \Illuminate\Support\Str::of($profileName ?: 'B')->substr(0, 1);
                            @endphp

                            <article class="group relative overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl transition hover:-translate-y-1 hover:shadow-blue-900/10">
                                <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-blue-400/60 to-transparent"></div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-lg font-black text-white shadow-lg shadow-violet-500/20">
                                        {{ $profileInitial }}
                                    </div>
                                    @include('partials.favorite-button', ['profile' => $recommendedProfile, 'variant' => 'light', 'compact' => true])
                                </div>

                                <div class="mt-5 flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $profileType }}</span>
                                    @if(method_exists($recommendedProfile, 'isPremium') && $recommendedProfile->isPremium())
                                        <span class="rounded-full bg-fuchsia-50 px-3 py-1 text-xs font-black text-fuchsia-700">Premium</span>
                                    @endif
                                </div>

                                <h3 class="mt-4 line-clamp-2 text-xl font-black tracking-tight">{{ $profileName }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500">
                                    {{ $recommendedProfile->business_category ?: $recommendedProfile->short_description ?: $recommendedProfile->description ?: 'Профил в BON с фокус върху доверие, видимост и реални резултати.' }}
                                </p>

                                <div class="mt-5 grid grid-cols-2 gap-2 text-xs font-bold text-slate-600">
                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <span class="block text-slate-400">Trust Score</span>
                                        <strong class="text-base text-[#070B1F]">{{ $trustScore }}/100</strong>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <span class="block text-slate-400">Рейтинг</span>
                                        <strong class="text-base text-[#070B1F]">{{ $rating ? number_format($rating, 1) : '—' }}</strong>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <span class="block text-slate-400">Проекти</span>
                                        <strong class="text-base text-[#070B1F]">{{ $completed }}</strong>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <span class="block text-slate-400">Профил</span>
                                        <strong class="text-base text-[#070B1F]">{{ $completeness !== null ? $completeness . '%' : '—' }}</strong>
                                    </div>
                                </div>

                                <a href="{{ $profileUrl }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-slate-950 px-4 text-sm font-black text-white transition group-hover:-translate-y-0.5">
                                    Виж профил
                                </a>
                            </article>
                        @empty
                            <div class="rounded-[1.75rem] border border-dashed border-slate-200 bg-white/70 p-6 text-center text-sm leading-6 text-slate-500 md:col-span-2 xl:col-span-4">
                                Все още няма достатъчно профили за тази категория. След миграции и първи регистрации тук ще се появят препоръчани бизнеси и фрийлансъри.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-7 grid gap-4 lg:grid-cols-[1fr_0.9fr]">
                        <div class="rounded-[1.75rem] border border-white/80 bg-white/75 p-5 shadow-xl shadow-blue-900/5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-xl font-black">Последни обяви{{ $smartCategory ? ' в ' . $smartCategory : '' }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">Проекти, по които специалистите могат да кандидатстват с кредити.</p>
                                </div>
                                <a href="{{ route('freelancer.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-4 text-sm font-black text-slate-700">Виж обяви</a>
                            </div>

                            <div class="mt-5 grid gap-3">
                                @forelse($smartJobs as $job)
                                    <article class="rounded-3xl border border-slate-100 bg-white/85 p-4">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <h4 class="font-black">{{ $job->title }}</h4>
                                                <p class="mt-1 text-sm text-slate-500">{{ $job->business?->business_name ?: $job->business?->name ?: 'BON бизнес' }} · {{ $job->category ?: 'Без категория' }}</p>
                                            </div>
                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">3 кредита</span>
                                        </div>
                                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $job->description }}</p>
                                    </article>
                                @empty
                                    <p class="rounded-3xl border border-dashed border-slate-200 bg-white/70 p-5 text-sm leading-6 text-slate-500">Няма активни обяви за избраната категория.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-white/80 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-6 text-white shadow-2xl shadow-violet-500/20">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-white/65">Публикувай нужда</p>
                            <h3 class="mt-3 text-2xl font-black">Искаш предложения от подходящи профили?</h3>
                            <p class="mt-3 text-sm leading-7 text-white/75">
                                Опиши проекта или нуждата си и BON ще помогне профилите в правилната категория да видят контекста.
                            </p>
                            <a href="{{ route('request.service', array_filter(['category' => $smartCategory])) }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-white px-5 text-sm font-black text-violet-700 shadow-xl shadow-violet-950/10 sm:w-auto">
                                Публикувай заявка
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-10 grid max-w-[1440px] gap-6 sm:mt-14 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Финансов анализ</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Виж реалната печалба, не само оборота.</h2>
                    <p class="mt-5 text-base leading-8 text-slate-600">
                        Въвеждаш месечен оборот, разходи, персонал, доставки, наем, маркетинг и софтуер.
                        BON изчислява нетна печалба, марж, разходи като процент от оборота и къде има риск.
                    </p>
                    @auth
                        <a href="{{ auth()->user()->isBusiness() ? route('business.insights.index') : route('bon.onboarding') }}" class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/25 sm:w-auto">
                            Отвори финансов анализ
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('register') }}" data-track="cta_business_signup" class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/25 sm:w-auto">
                            Създай бизнес профил
                        </a>
                    @endguest
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/78 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            ['label' => 'Оборот', 'value' => '12 400 лв'],
                            ['label' => 'Разходи', 'value' => '8 920 лв'],
                            ['label' => 'Нетна печалба', 'value' => '3 480 лв'],
                            ['label' => 'Марж', 'value' => '28.1%'],
                        ] as $metric)
                            <div class="rounded-3xl border border-slate-200/70 bg-white/80 p-5">
                                <p class="text-sm font-bold text-slate-500">{{ $metric['label'] }}</p>
                                <p class="mt-2 text-3xl font-black">{{ $metric['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 rounded-3xl bg-gradient-to-r from-blue-50 via-violet-50 to-pink-50 p-5">
                        <p class="text-sm font-black text-[#070B1F]">Препоръка</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Разходът за персонал е висок спрямо оборота. Прегледай графиците, натоварването и ролите преди да променяш бюджета.</p>
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-10 grid max-w-[1440px] gap-6 sm:mt-14 lg:grid-cols-2">
                <article class="rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/8 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Business Health Score</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">Оценка от 0 до 100 за финансовото здраве.</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        Score-ът комбинира печалба, марж, персонал като процент от оборота, фиксирани разходи и break-even риск.
                    </p>
                    <div class="mt-6 grid h-40 place-items-center rounded-[2rem] bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-white shadow-xl shadow-violet-500/20">
                        <div class="text-center">
                            <p class="text-6xl font-black">74</p>
                            <p class="mt-1 text-sm font-bold text-white/75">добра основа, има места за оптимизация</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/8 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">Visibility Score</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">Колко силно е представен бизнесът онлайн?</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        BON оценява снимки, описание, категории, градове, активност, отзиви, badges и Premium видимост.
                    </p>
                    <div class="mt-6 grid gap-3">
                        @foreach (['Снимки и галерия' => 86, 'Описание и услуги' => 72, 'Отзиви и доверие' => 68, 'Premium видимост' => 92] as $label => $score)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-4 text-sm font-black">
                                    <span>{{ $label }}</span>
                                    <span>{{ $score }}/100</span>
                                </div>
                                <div class="mt-3 h-2 rounded-full bg-slate-200">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-blue-600 to-fuchsia-500" style="width: {{ $score }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="mx-auto mt-10 max-w-[1440px] rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/8 backdrop-blur-2xl sm:mt-14 sm:rounded-[2rem] sm:p-8">
                <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Репутация и отзиви</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Доверието трябва да се вижда още преди първия контакт.</h2>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ([
                            ['title' => 'Проверен профил', 'text' => 'Ясен сигнал, че бизнесът е реален и поддържан.'],
                            ['title' => 'Отзиви', 'text' => 'Социално доказателство, което намалява колебанието.'],
                            ['title' => 'Premium badge', 'text' => 'По-силно позициониране и доверителен контекст.'],
                        ] as $card)
                            <article class="rounded-3xl border border-slate-200/70 bg-white/80 p-5">
                                <h3 class="font-black">{{ $card['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $card['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-10 max-w-[1440px] sm:mt-14">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Планове</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Започни с базови инструменти. Надгради с Premium анализ.</h2>
                    </div>
                    <a href="{{ route('plans') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:w-auto">Виж всички детайли</a>
                </div>

                <div class="mt-7 grid gap-5 lg:grid-cols-2">
                    @foreach ($plans as $plan)
                        <article class="relative rounded-[1.65rem] border {{ $plan['highlight'] ? 'border-violet-200 bg-white/85 shadow-violet-900/12' : 'border-white/70 bg-white/76 shadow-blue-900/5' }} p-5 shadow-2xl backdrop-blur-2xl sm:rounded-[2rem] sm:p-7">
                            @if ($plan['highlight'])
                                <span class="absolute right-6 top-6 rounded-full bg-gradient-to-r from-blue-600 to-fuchsia-500 px-3 py-1 text-xs font-black text-white">Препоръчан</span>
                            @endif
                            <h3 class="text-2xl font-black">{{ $plan['name'] }}</h3>
                            <p class="mt-4 text-4xl font-black">{{ $plan['price'] }} <span class="text-base font-bold text-slate-500">{{ $plan['note'] }}</span></p>
                            <p class="mt-4 text-sm leading-7 text-slate-600">{{ $plan['positioning'] }}</p>
                            <ul class="mt-6 grid gap-3 text-sm leading-6 text-slate-600">
                                @foreach ($plan['features'] as $feature)
                                    <li class="flex gap-3"><span class="font-black text-blue-600">✓</span>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="mx-auto mt-10 max-w-[1440px] rounded-[1.65rem] border border-white/70 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-2xl shadow-violet-500/20 sm:mt-14 sm:rounded-[2rem] sm:p-8">
                <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-white/70">Започни с BON</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Дай на бизнеса си по-професионално присъствие и по-ясна картина за растеж.</h2>
                    </div>
                    <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl sm:w-auto">
                        Добави своя бизнес
                    </a>
                </div>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openButtons = document.querySelectorAll('[data-tool-open]');
            const modals = document.querySelectorAll('[data-tool-modal]');
            let activeModal = null;

            const closeModal = (modal) => {
                if (!modal) {
                    return;
                }

                const overlay = modal.querySelector('[data-tool-overlay]');
                const panel = modal.querySelector('[data-tool-panel]');

                overlay?.classList.remove('opacity-100');
                overlay?.classList.add('opacity-0');
                panel?.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                panel?.classList.add('opacity-0', 'translate-y-4', 'scale-95');
                modal.setAttribute('aria-hidden', 'true');

                window.setTimeout(() => {
                    modal.classList.add('hidden');

                    if (activeModal === modal) {
                        activeModal = null;
                        document.body.classList.remove('bon-modal-open');
                    }
                }, 260);
            };

            const openModal = (id) => {
                const modal = document.getElementById(id);

                if (!modal) {
                    return;
                }

                if (activeModal && activeModal !== modal) {
                    closeModal(activeModal);
                }

                const overlay = modal.querySelector('[data-tool-overlay]');
                const panel = modal.querySelector('[data-tool-panel]');

                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('bon-modal-open');
                activeModal = modal;

                window.requestAnimationFrame(() => {
                    overlay?.classList.remove('opacity-0');
                    overlay?.classList.add('opacity-100');
                    panel?.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                    panel?.classList.add('opacity-100', 'translate-y-0', 'scale-100');
                });
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => openModal(button.dataset.toolOpen));
            });

            modals.forEach((modal) => {
                modal.querySelectorAll('[data-tool-close], [data-tool-overlay]').forEach((trigger) => {
                    trigger.addEventListener('click', () => closeModal(modal));
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && activeModal) {
                    closeModal(activeModal);
                }
            });
        });
    </script>
</body>
</html>

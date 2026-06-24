@php
    $navItems = [
        ['label' => 'Начало', 'href' => route('home')],
        ['label' => 'Търсене', 'href' => route('search')],
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
            'color' => 'from-violet-500 to-emerald-400',
        ],
        [
            'title' => 'Visibility Score',
            'text' => 'Показва колко добре е представен бизнесът онлайн — снимки, описание, услуги, градове, отзиви, активност и Premium видимост.',
            'icon' => '◈',
            'color' => 'from-amber-400 to-fuchsia-500',
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
            'color' => 'from-cyan-400 to-violet-500',
        ],
        [
            'title' => 'Месечен бизнес доклад',
            'text' => 'Обобщава резултатите за месеца — видимост, профил, финанси, отзиви и конкретни следващи стъпки.',
            'icon' => '↗',
            'color' => 'from-emerald-400 to-blue-500',
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
    $smartCategories = $smartCategories ?? \App\Support\CategoryCatalog::names()->all();
    $socialProofStats = array_merge([
        'businesses' => 0,
        'freelancers' => 0,
        'open_jobs' => 0,
        'reviews' => 0,
    ], $socialProofStats ?? []);
    $formatBonCount = fn ($value) => (int) $value > 0 ? number_format((int) $value, 0, '.', ' ') : 'Скоро';
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BON | Бизнес видимост, анализ и растеж</title>
    <meta name="description" content="BON помага на локалните бизнеси да изградят по-силно онлайн присъствие, да следят ключови показатели, да подобряват репутацията си и да взимат по-добри решения за растеж.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')

    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body {
            touch-action: auto;
            -webkit-overflow-scrolling: touch;
        }

        .bon-home-shell *,
        .bon-home-shell *::before,
        .bon-home-shell *::after {
            box-sizing: border-box;
        }

        .bon-grid {
            background-image:
                linear-gradient(to right, rgba(37, 99, 235, .075) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, .075) 1px, transparent 1px);
            background-size: 72px 72px;
            mask-image: radial-gradient(circle at 50% 24%, black 0%, transparent 78%);
            pointer-events: none;
        }

        .bon-dot-field {
            background-image: radial-gradient(rgba(37, 99, 235, .34) 1.4px, transparent 1.4px);
            background-size: 16px 16px;
            pointer-events: none;
        }

        .bon-home-aurora {
            overflow: hidden;
            background:
                radial-gradient(circle at 50% 0%, rgba(47, 140, 255, .24) 0%, rgba(124, 58, 237, .14) 38%, transparent 64%),
                radial-gradient(circle at 16% 18%, rgba(34, 211, 238, .16), transparent 34%),
                radial-gradient(circle at 84% 16%, rgba(217, 70, 239, .15), transparent 34%),
                radial-gradient(circle at 22% 72%, rgba(52, 211, 153, .10), transparent 32%),
                radial-gradient(circle at 78% 72%, rgba(245, 158, 11, .09), transparent 30%),
                linear-gradient(180deg, rgba(2, 6, 23, 1) 0%, rgba(6, 16, 31, .98) 54%, rgba(3, 7, 18, 1) 100%);
        }

        .bon-home-aurora::before,
        .bon-home-aurora::after {
            content: "";
            position: absolute;
            width: min(34rem, 58vw);
            height: min(34rem, 58vw);
            border-radius: 9999px;
            filter: blur(58px);
            opacity: .22;
            pointer-events: none;
            animation: bon-blob-drift 18s ease-in-out infinite;
        }

        .bon-home-aurora::before {
            left: -8%;
            top: 18%;
            background: radial-gradient(circle, rgba(34, 211, 238, .64), transparent 62%);
        }

        .bon-home-aurora::after {
            right: -10%;
            top: 42%;
            background: radial-gradient(circle, rgba(245, 158, 11, .34), rgba(168, 85, 247, .28), transparent 66%);
            animation-delay: -7s;
        }

        .bon-home-shell {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            background: #020617;
            color: #f8fbff;
        }

        .bon-home-shell [class*="text-[#070B1F]"],
        .bon-home-shell [class*="text-slate-900"],
        .bon-home-shell [class*="text-slate-800"] {
            color: #f8fbff !important;
        }

        .bon-home-shell [class*="text-slate-700"],
        .bon-home-shell [class*="text-slate-600"] {
            color: #d6deed !important;
        }

        .bon-home-shell [class*="text-slate-500"],
        .bon-home-shell [class*="text-slate-400"] {
            color: #9aa8bd !important;
        }

        .bon-home-shell [class*="text-blue-700"],
        .bon-home-shell [class*="text-blue-600"] {
            color: #60a5fa !important;
        }

        .bon-home-shell [class*="text-violet-700"],
        .bon-home-shell [class*="text-violet-600"] {
            color: #c4b5fd !important;
        }

        .bon-home-shell [class*="text-fuchsia-700"],
        .bon-home-shell [class*="text-fuchsia-600"] {
            color: #f0abfc !important;
        }

        .bon-home-shell [class*="bg-white/"],
        .bon-home-shell [class*="bg-white"],
        .bon-home-shell [class*="bg-slate-50"],
        .bon-home-shell [class*="bg-blue-50"],
        .bon-home-shell [class*="bg-fuchsia-50"] {
            background: rgba(13, 24, 46, .74) !important;
        }

        .bon-home-shell [class*="border-white/"],
        .bon-home-shell [class*="border-slate-"],
        .bon-home-shell [class*="border-blue-200"],
        .bon-home-shell [class*="border-violet-200"] {
            border-color: rgba(132, 154, 196, .22) !important;
        }

        .bon-home-shell input,
        .bon-home-shell select,
        .bon-home-shell textarea {
            border-color: rgba(132, 154, 196, .24) !important;
            background: rgba(7, 15, 30, .92) !important;
            color: #f8fbff !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
        }

        .bon-home-shell input::placeholder,
        .bon-home-shell textarea::placeholder {
            color: #6f7c93 !important;
        }

        .bon-home-shell input:focus,
        .bon-home-shell select:focus,
        .bon-home-shell textarea:focus {
            border-color: rgba(96, 165, 250, .68) !important;
            box-shadow: 0 0 0 4px rgba(47, 140, 255, .14) !important;
        }

        .bon-home-header-card,
        .bon-home-search,
        .bon-orbit-card,
        .bon-home-mobile-chips > div {
            border-color: rgba(132, 154, 196, .24) !important;
            background: rgba(12, 24, 46, .76) !important;
            box-shadow: 0 24px 80px rgba(0, 0, 0, .28), inset 0 1px 0 rgba(255, 255, 255, .05) !important;
        }

        .bon-home-shell article,
        .bon-home-shell section > div[class*="rounded-"],
        .bon-home-shell form[class*="rounded-"] {
            box-shadow: 0 24px 80px rgba(0, 0, 0, .24), inset 0 1px 0 rgba(255, 255, 255, .04);
        }

        .bon-home-shell > .relative > section {
            position: relative;
            isolation: isolate;
        }

        .bon-home-shell > .relative > section::before {
            content: "";
            position: absolute;
            inset: -1.75rem;
            z-index: -1;
            border-radius: 2rem;
            pointer-events: none;
            opacity: .36;
            background: var(--bon-section-glow, radial-gradient(circle at 50% 0%, rgba(47, 140, 255, .16), transparent 54%));
        }

        .bon-home-shell > .relative > section:nth-of-type(2) {
            --bon-section-glow: radial-gradient(circle at 12% 18%, rgba(34, 211, 238, .18), transparent 42%), radial-gradient(circle at 88% 30%, rgba(124, 58, 237, .14), transparent 44%);
        }

        .bon-home-shell > .relative > section:nth-of-type(3) {
            --bon-section-glow: radial-gradient(circle at 18% 28%, rgba(52, 211, 153, .14), transparent 42%), radial-gradient(circle at 82% 34%, rgba(34, 211, 238, .12), transparent 40%);
        }

        .bon-home-shell > .relative > section:nth-of-type(4) {
            --bon-section-glow: radial-gradient(circle at 12% 28%, rgba(59, 130, 246, .14), transparent 42%), radial-gradient(circle at 88% 36%, rgba(245, 158, 11, .10), transparent 40%);
        }

        .bon-home-shell > .relative > section:nth-of-type(5) {
            --bon-section-glow: radial-gradient(circle at 18% 24%, rgba(139, 92, 246, .16), transparent 42%), radial-gradient(circle at 84% 50%, rgba(52, 211, 153, .10), transparent 40%);
        }

        .bon-home-shell > .relative > section:nth-of-type(6) {
            --bon-section-glow: radial-gradient(circle at 18% 24%, rgba(245, 158, 11, .12), transparent 42%), radial-gradient(circle at 84% 50%, rgba(34, 211, 238, .12), transparent 40%);
        }

        .bon-home-shell article,
        .bon-home-shell button[data-tool-open],
        .bon-home-stat-card {
            position: relative;
            overflow: hidden;
        }

        .bon-home-shell article::before,
        .bon-home-shell button[data-tool-open]::before,
        .bon-home-stat-card::before {
            content: "";
            position: absolute;
            inset: 0 1.25rem auto;
            height: 2px;
            border-radius: 9999px;
            background: var(--bon-card-gradient, linear-gradient(90deg, #38bdf8, #8b5cf6, #d946ef));
            opacity: .78;
            pointer-events: none;
        }

        .bon-home-shell article:nth-of-type(4n+1),
        .bon-home-shell button[data-tool-open]:nth-of-type(4n+1),
        .bon-home-stat-card:nth-child(4n+1) {
            --bon-card-gradient: linear-gradient(90deg, #22d3ee, #3b82f6);
        }

        .bon-home-shell article:nth-of-type(4n+2),
        .bon-home-shell button[data-tool-open]:nth-of-type(4n+2),
        .bon-home-stat-card:nth-child(4n+2) {
            --bon-card-gradient: linear-gradient(90deg, #8b5cf6, #34d399);
        }

        .bon-home-shell article:nth-of-type(4n+3),
        .bon-home-shell button[data-tool-open]:nth-of-type(4n+3),
        .bon-home-stat-card:nth-child(4n+3) {
            --bon-card-gradient: linear-gradient(90deg, #f59e0b, #fb7185);
        }

        .bon-home-shell article:nth-of-type(4n+4),
        .bon-home-shell button[data-tool-open]:nth-of-type(4n+4),
        .bon-home-stat-card:nth-child(4n+4) {
            --bon-card-gradient: linear-gradient(90deg, #34d399, #22d3ee, #a855f7);
        }

        .bon-home-shell article:hover,
        .bon-home-shell button[data-tool-open]:hover,
        .bon-home-stat-card:hover {
            border-color: rgba(125, 211, 252, .35) !important;
            box-shadow: 0 28px 90px rgba(0, 0, 0, .30), 0 0 36px rgba(47, 140, 255, .10), inset 0 1px 0 rgba(255, 255, 255, .06) !important;
        }

        .bon-home-title {
            overflow-wrap: break-word;
            text-wrap: balance;
        }

        .bon-home-title span {
            display: inline-block;
            max-width: 100%;
            overflow-wrap: break-word;
        }

        .bon-home-shell a,
        .bon-home-shell button,
        .bon-home-shell input,
        .bon-home-shell select {
            touch-action: manipulation;
        }

        .bon-home-shell a[class*="rounded-2xl"],
        .bon-home-shell button[class*="rounded-2xl"] {
            transition-duration: 240ms;
            transition-timing-function: cubic-bezier(.2, .8, .2, 1);
        }

        .bon-home-shell a[class*="bg-gradient-to-r"]:hover,
        .bon-home-shell button[class*="bg-gradient-to-r"]:hover,
        .bon-home-shell a[class*="bg-white"]:hover,
        .bon-home-shell button[class*="bg-white"]:hover {
            filter: saturate(1.08);
            transform: translateY(-2px);
        }

        .bon-icon-pulse {
            position: relative;
            isolation: isolate;
        }

        .bon-icon-pulse::after {
            content: "";
            position: absolute;
            inset: -4px;
            z-index: -1;
            border-radius: inherit;
            background: inherit;
            opacity: .34;
            filter: blur(10px);
            animation: bon-icon-glow 3.8s ease-in-out infinite;
        }

        .bon-step-card {
            position: relative;
        }

        .bon-step-card::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.08) 38%, transparent 64%);
            opacity: 0;
            transform: translateX(-42%);
            transition: opacity 240ms ease, transform 560ms ease;
            pointer-events: none;
        }

        .bon-step-card:hover::after {
            opacity: 1;
            transform: translateX(42%);
        }

        .bon-category-chip-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .55rem;
        }

        .bon-category-chip {
            display: flex;
            min-height: 3rem;
            align-items: center;
            gap: .65rem;
            border: 1px solid rgba(132, 154, 196, .22);
            border-radius: 1rem;
            background: rgba(7, 15, 30, .78);
            padding: .72rem .8rem;
            color: #d6deed;
            font-size: .78rem;
            font-weight: 850;
            line-height: 1.25;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
            transition: border-color .18s ease, background .18s ease, transform .18s ease;
        }

        .bon-category-chip::before {
            content: "";
            height: .55rem;
            width: .55rem;
            flex: 0 0 auto;
            border-radius: 9999px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6 58%, #d946ef);
            box-shadow: 0 0 18px rgba(96, 165, 250, .28);
        }

        .bon-category-chip:nth-child(5n+2)::before {
            background: linear-gradient(135deg, #22d3ee, #34d399);
            box-shadow: 0 0 18px rgba(34, 211, 238, .24);
        }

        .bon-category-chip:nth-child(5n+3)::before {
            background: linear-gradient(135deg, #8b5cf6, #d946ef);
            box-shadow: 0 0 18px rgba(168, 85, 247, .24);
        }

        .bon-category-chip:nth-child(5n+4)::before {
            background: linear-gradient(135deg, #f59e0b, #fb7185);
            box-shadow: 0 0 18px rgba(245, 158, 11, .22);
        }

        .bon-category-chip:nth-child(5n+5)::before {
            background: linear-gradient(135deg, #38bdf8, #6366f1);
            box-shadow: 0 0 18px rgba(56, 189, 248, .22);
        }

        .bon-category-chip:hover {
            transform: translateY(-1px);
            border-color: rgba(96, 165, 250, .5);
            background: rgba(15, 27, 52, .9);
        }

        @media (min-width: 390px) {
            .bon-category-chip-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 768px) {
            .bon-category-chip-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .bon-category-chip-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
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
            height: 470px;
            min-height: 470px;
            align-items: center;
            justify-content: center;
            isolation: isolate;
            pointer-events: none;
            touch-action: auto;
        }

        .bon-globe-stage *,
        .bon-globe-link,
        .bon-globe-node,
        .bon-orbit-card,
        .bon-orbit-line,
        .bon-connection-line,
        .bon-connection-dot {
            pointer-events: none;
        }

        .bon-globe-glow {
            position: absolute;
            height: 390px;
            width: 390px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(59, 130, 246, .58), rgba(124, 58, 237, .34) 42%, rgba(217, 70, 239, .24) 72%, transparent 100%);
            filter: blur(38px);
        }

        .bon-globe-shell {
            position: absolute;
            height: 342px;
            width: 342px;
            border-radius: 9999px;
            border: 1px solid rgba(148, 163, 255, .28);
            background:
                radial-gradient(circle at 35% 20%, rgba(255, 255, 255, .20), transparent 30%),
                radial-gradient(circle at 60% 70%, rgba(124, 58, 237, .30), transparent 46%),
                rgba(9, 18, 38, .58);
            box-shadow: 0 34px 100px rgba(47, 140, 255, .22), inset 0 1px 24px rgba(255, 255, 255, .08);
            backdrop-filter: blur(18px);
        }

        .bon-globe-ring {
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
        }

        .bon-globe-ring-outer {
            height: 370px;
            width: 370px;
            background: conic-gradient(from 0deg, transparent 0 16%, rgba(37, 99, 235, .54) 24%, transparent 35% 52%, rgba(236, 72, 153, .58) 61%, transparent 72% 100%);
            mask: radial-gradient(circle, transparent 66%, black 67% 70%, transparent 71%);
            animation: bon-spin 16s linear infinite;
        }

        .bon-globe-ring-inner {
            height: 292px;
            width: 292px;
            background: conic-gradient(from 90deg, transparent 0 18%, rgba(34, 211, 238, .48) 27%, transparent 40% 58%, rgba(139, 92, 246, .5) 67%, transparent 80% 100%);
            mask: radial-gradient(circle, transparent 64%, black 65% 69%, transparent 70%);
            animation: bon-spin-reverse 22s linear infinite;
        }

        .bon-globe-core {
            position: relative;
            display: grid;
            height: 246px;
            width: 246px;
            place-items: center;
            overflow: hidden;
            border-radius: 9999px;
            border: 1px solid rgba(191, 219, 254, .34);
            background:
                radial-gradient(circle at 32% 22%, rgba(255, 255, 255, .52) 0 7%, transparent 18%),
                radial-gradient(circle at 65% 72%, rgba(217, 70, 239, .78), transparent 35%),
                radial-gradient(circle at 30% 70%, rgba(34, 211, 238, .58), transparent 35%),
                linear-gradient(135deg, #0b4fc9 0%, #2563eb 30%, #6d28d9 68%, #d946ef 100%);
            box-shadow: 0 26px 80px rgba(59, 130, 246, .34), inset 0 18px 34px rgba(255, 255, 255, .18), inset 0 -28px 44px rgba(2, 6, 23, .38);
        }

        .bon-globe-core::before {
            content: "";
            position: absolute;
            inset: 16px;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .24);
            background:
                linear-gradient(90deg, transparent 45%, rgba(255, 255, 255, .18) 50%, transparent 55%),
                linear-gradient(0deg, transparent 44%, rgba(255, 255, 255, .12) 50%, transparent 56%),
                repeating-radial-gradient(ellipse at center, transparent 0 18px, rgba(255, 255, 255, .10) 19px 20px, transparent 21px 42px);
            opacity: .78;
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
            background: rgba(255, 255, 255, .35);
            filter: blur(8px);
            transform: rotate(-18deg);
        }

        .bon-globe-letter {
            position: relative;
            z-index: 2;
            color: white;
            font-size: 6.2rem;
            font-weight: 950;
            line-height: 1;
            text-shadow: 0 12px 28px rgba(15, 23, 42, .32);
        }

        .bon-globe-network {
            position: absolute;
            inset: 22px;
            z-index: 1;
            border-radius: 9999px;
            opacity: .88;
        }

        .bon-globe-link {
            position: absolute;
            height: 1px;
            transform-origin: left center;
            border-radius: 9999px;
            background: linear-gradient(90deg, transparent, rgba(191, 219, 254, .42), rgba(217, 70, 239, .25), transparent);
            filter: drop-shadow(0 0 8px rgba(96, 165, 250, .28));
        }

        .bon-globe-node {
            position: absolute;
            height: .55rem;
            width: .55rem;
            border-radius: 9999px;
            background: #bfdbfe;
            box-shadow: 0 0 0 5px rgba(96, 165, 250, .10), 0 0 18px rgba(96, 165, 250, .86);
            animation: bon-node-pulse 4.8s ease-in-out infinite;
        }

        .bon-globe-halo {
            position: absolute;
            bottom: 30px;
            left: 50%;
            height: 34px;
            width: 280px;
            transform: translateX(-50%);
            border-radius: 9999px;
            background: radial-gradient(ellipse, rgba(139, 92, 246, .46), rgba(34, 211, 238, .20) 48%, transparent 74%);
            filter: blur(10px);
        }

        .bon-globe-halo-ring {
            position: absolute;
            bottom: 48px;
            left: 50%;
            height: 44px;
            width: 310px;
            transform: translateX(-50%);
            border-radius: 9999px;
            border: 1px solid rgba(148, 163, 255, .26);
            box-shadow: 0 0 30px rgba(124, 58, 237, .32);
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
            min-width: 11.5rem;
            align-items: center;
            gap: .75rem;
            border: 1px solid rgba(132, 154, 196, .26);
            border-radius: 1.35rem;
            background: rgba(13, 24, 46, .78);
            padding: .75rem .9rem;
            box-shadow: 0 22px 70px rgba(0, 0, 0, .30), inset 0 1px 0 rgba(255, 255, 255, .05);
            backdrop-filter: blur(22px);
            animation: bon-card-drift 10s ease-in-out infinite;
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
            box-shadow: 0 0 0 4px rgba(15, 23, 42, .72), 0 0 18px currentColor;
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
            touch-action: none;
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
            50% { transform: translateY(-6px); }
        }

        @keyframes bon-node-pulse {
            0%, 100% { opacity: .58; transform: scale(.92); }
            50% { opacity: 1; transform: scale(1.18); }
        }

        @keyframes bon-blob-drift {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(2.2rem, -1.4rem, 0) scale(1.08); }
        }

        @keyframes bon-icon-glow {
            0%, 100% { opacity: .22; transform: scale(.96); }
            50% { opacity: .5; transform: scale(1.08); }
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

            .bon-globe-network {
                inset: 16px;
            }

            .bon-globe-node {
                height: .44rem;
                width: .44rem;
            }

            .bon-connection-line,
            .bon-connection-dot,
            .bon-orbit-card,
            .bon-orbit-line {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .bon-home-shell {
                overflow-x: hidden;
            }

            .bon-home-title {
                max-width: min(100%, 38rem);
                font-size: clamp(1.85rem, 7.2vw, 3rem);
                line-height: 1.12;
                letter-spacing: 0;
            }

            .bon-home-subtitle {
                max-width: min(100%, 35rem);
                line-height: 1.65;
            }

            .bon-home-actions {
                gap: .75rem;
            }

            .bon-home-actions a {
                min-height: 3.1rem;
            }
        }

        @media (max-width: 640px) {
            .bon-home-shell {
                max-width: 100vw;
            }

            .bon-home-shell > .relative {
                width: 100%;
                max-width: 100%;
                padding-left: .75rem;
                padding-right: .75rem;
            }

            .bon-home-shell header,
            .bon-home-shell section,
            .bon-home-search {
                width: calc(100vw - 1.5rem);
                max-width: calc(100vw - 1.5rem);
                margin-left: auto;
                margin-right: auto;
            }

            .bon-home-header-card {
                width: 100%;
                min-height: 60px;
                border-radius: 1.25rem;
                padding: .65rem;
            }

            .bon-home-header-card > a {
                max-width: calc(100% - 3.25rem);
            }

            .bon-home-header-card details {
                display: block;
                flex-shrink: 0;
            }

            .bon-home-title {
                max-width: 21rem;
                margin-top: 1rem;
                font-size: clamp(1.78rem, 7.6vw, 2.15rem);
                line-height: 1.14;
                letter-spacing: 0;
            }

            .bon-home-subtitle {
                max-width: 20.5rem;
                margin-top: 1rem;
                font-size: .95rem;
                line-height: 1.65;
            }

            .bon-home-actions,
            .bon-home-search,
            .bon-home-orb-wrap,
            .bon-home-mobile-chips {
                width: 100%;
                max-width: 100%;
            }

            .bon-home-search {
                overflow: hidden;
                border-radius: 1.25rem;
                gap: .8rem;
            }

            .bon-home-actions {
                margin-top: 1.25rem;
                gap: .75rem;
            }

            .bon-home-actions a {
                min-height: 3.15rem;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .bon-home-search label,
            .bon-home-search input,
            .bon-home-search select,
            .bon-home-search button {
                min-width: 0;
                width: 100%;
            }

            .bon-home-shell section {
                margin-top: 2.25rem;
            }

            .bon-home-shell section:first-of-type {
                margin-top: 0;
            }

            .bon-home-shell article,
            .bon-home-shell button,
            .bon-category-chip {
                overflow-wrap: anywhere;
            }

            .bon-home-shell a[class*="bg-gradient-to-r"]:hover,
            .bon-home-shell button[class*="bg-gradient-to-r"]:hover,
            .bon-home-shell a[class*="bg-white"]:hover,
            .bon-home-shell button[class*="bg-white"]:hover,
            .bon-category-chip:hover {
                transform: none;
            }

            .bon-home-mobile-chips {
                grid-template-columns: 1fr;
            }

            .bon-grid {
                background-size: 46px 46px;
                opacity: .24;
            }

            .bon-globe-stage {
                height: 196px;
                min-height: 196px;
                margin-inline: auto;
                max-width: 100%;
            }

            .bon-globe-glow,
            .bon-globe-ring-outer {
                height: 174px;
                width: 174px;
            }

            .bon-globe-shell {
                height: 164px;
                width: 164px;
            }

            .bon-globe-ring-inner {
                height: 132px;
                width: 132px;
            }

            .bon-globe-core {
                height: 116px;
                width: 116px;
            }

            .bon-globe-letter {
                font-size: 2.85rem;
            }

            .bon-globe-network {
                inset: 12px;
            }

            .bon-globe-node {
                height: .34rem;
                width: .34rem;
                box-shadow: 0 0 0 4px rgba(96, 165, 250, .10), 0 0 12px rgba(96, 165, 250, .76);
            }

            .bon-globe-halo {
                bottom: 18px;
                width: 138px;
            }

            .bon-globe-halo-ring {
                bottom: 30px;
                height: 26px;
                width: 154px;
            }

            .bon-float,
            .bon-pulse,
            .bon-globe-ring-outer,
            .bon-globe-ring-inner,
            .bon-globe-core::before,
            .bon-home-aurora::before,
            .bon-home-aurora::after,
            .bon-icon-pulse::after {
                animation-duration: 12s;
            }

            .bon-home-aurora::before,
            .bon-home-aurora::after {
                width: 15rem;
                height: 15rem;
                filter: blur(48px);
                opacity: .14;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .bon-float,
            .bon-pulse,
            .bon-globe-ring-outer,
            .bon-globe-ring-inner,
            .bon-globe-core::before,
            .bon-globe-node,
            .bon-orbit-card,
            .bon-orbit-line::after,
            .bon-home-aurora::before,
            .bon-home-aurora::after,
            .bon-icon-pulse::after {
                animation: none;
            }

            .bon-home-shell *,
            .bon-step-card::after {
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="bon-dark-page antialiased">
    <main class="bon-home-shell relative min-h-screen w-full max-w-full overflow-x-hidden bg-[#020617] text-slate-50">
        <div class="bon-home-aurora pointer-events-none absolute inset-0"></div>
        <div class="bon-grid pointer-events-none absolute inset-0 opacity-[.38]"></div>
        <div class="pointer-events-none absolute -top-40 left-[-12rem] h-[35rem] w-[35rem] rounded-full bg-blue-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-40 right-[-10rem] h-[35rem] w-[35rem] rounded-full bg-fuchsia-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[24rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-18rem] left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="bon-dot-field pointer-events-none absolute left-6 top-56 hidden h-40 w-36 opacity-30 lg:block"></div>
        <div class="bon-dot-field pointer-events-none absolute bottom-10 right-6 hidden h-40 w-36 opacity-25 lg:block" style="background-image: radial-gradient(rgba(236,72,153,.36) 1.4px, transparent 1.4px);"></div>

        <div class="relative z-10 px-3 pb-12 sm:px-6 sm:pb-16 lg:px-8">
            <header class="mx-auto mt-4 w-full max-w-[1440px]">
                <div class="bon-home-header-card flex min-h-[68px] items-center justify-between rounded-[1.5rem] border border-white/70 bg-white/75 px-3 py-3 shadow-[0_24px_80px_rgba(30,41,100,.09)] backdrop-blur-2xl sm:min-h-[76px] sm:rounded-[1.75rem] sm:px-6">
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
                        <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'header' })" class="hidden rounded-2xl border border-slate-200/80 bg-white/70 px-5 py-3 text-sm font-bold text-[#070B1F] shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:inline-flex">
                            Вход
                        </a>
                        <a href="{{ route('register') }}" data-track="cta_business_signup" onclick="window.trackBonEvent('sign_up_start', { source: 'header' })" class="hidden rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-3.5 py-2.5 text-sm font-bold text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35 sm:inline-flex sm:px-6 sm:py-3">
                            Регистрация
                        </a>
                        <details data-mobile-menu class="group relative lg:hidden">
                            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-2xl border border-white/70 bg-white/80 text-slate-700 shadow-lg shadow-blue-900/5 backdrop-blur-xl transition hover:text-blue-600" aria-label="Меню">
                                <svg class="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                                </svg>
                                <svg class="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
                                </svg>
                            </summary>

                            <div class="absolute right-0 top-12 max-h-[calc(100dvh-5rem)] w-[min(21rem,calc(100vw-2rem))] overflow-y-auto rounded-[1.75rem] border border-white/70 bg-white/92 p-3 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:top-14">
                                <div class="grid gap-1">
                                    @foreach ($navItems as $item)
                                        <a href="{{ $item['href'] }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                    <div class="my-1 h-px bg-slate-200/70"></div>
                                    <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'mobile_header' })" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Вход</a>
                                    <a href="{{ route('register') }}" data-track="cta_business_signup" onclick="window.trackBonEvent('sign_up_start', { source: 'mobile_header' })" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-center text-sm font-black text-white shadow-lg shadow-violet-500/20">Регистрация</a>
                                </div>
                            </div>
                        </details>
                    </div>
                </div>
            </header>

            <section class="mx-auto w-full max-w-[1440px] pt-6 sm:pt-12 lg:pt-16">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,0.92fr)_minmax(420px,0.78fr)] lg:items-center xl:gap-10">
                <div class="mx-auto w-full max-w-5xl text-center lg:mx-0 lg:max-w-3xl lg:text-left">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-200/70 bg-white/80 px-3.5 py-2 text-xs font-semibold text-blue-700 shadow-sm shadow-blue-900/5 backdrop-blur-xl sm:px-4 sm:text-sm">
                        <span class="text-violet-600">✦</span>
                        BON Business OS
                    </div>

                    <h1 class="bon-home-title mx-auto mt-4 max-w-full text-[28px] font-black leading-[1.1] tracking-tight text-[#070B1F] sm:mt-6 sm:max-w-5xl sm:text-[60px] sm:tracking-[-0.055em] lg:mx-0 lg:text-[64px] xl:text-[74px]">
                        BON помага на бизнеси да намират <span class="bg-gradient-to-r from-blue-600 to-violet-600 bg-clip-text text-transparent">решения</span>, хора и инструменти за <span class="bg-gradient-to-r from-fuchsia-500 to-pink-500 bg-clip-text text-transparent">растеж</span>.
                    </h1>

                    <p class="bon-home-subtitle mx-auto mt-4 max-w-full text-[15px] leading-7 text-slate-600 sm:mt-6 sm:max-w-3xl sm:text-xl sm:leading-8 lg:mx-0">
                        Платформа за бизнеси, фрийлансъри и потребители — с инструменти, консултации, задачи, профили,
                        резервации и възможности за развитие.
                    </p>

                    <div class="bon-home-actions mt-6 flex flex-col justify-center gap-3 sm:mt-8 sm:flex-row lg:justify-start">
                        <a href="{{ route('business.landing') }}" data-track="cta_business_signup" onclick="window.trackBonEvent('business_registration_start', { source: 'home_cta' })" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 sm:w-auto">
                            Започни като бизнес
                        </a>
                        <a href="{{ route('register', ['role' => 'freelancer']) }}" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'home_cta' })" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:w-auto">
                            Стани фрийлансър
                        </a>
                        <a href="{{ route('search') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:w-auto">
                            Намери услуга
                        </a>
                    </div>

                    <a href="#bon-tools" class="mt-4 inline-flex text-sm font-black text-blue-700 transition hover:text-violet-700 sm:mt-5">
                        Виж инструментите в BON →
                    </a>

                    <div class="mt-5 grid gap-2 sm:grid-cols-3 lg:max-w-2xl">
                        @foreach ([
                            ['label' => 'Бизнес диагностика', 'accent' => 'from-cyan-400 to-blue-500'],
                            ['label' => 'Trust профили', 'accent' => 'from-violet-500 to-fuchsia-500'],
                            ['label' => 'Задачи и растеж', 'accent' => 'from-emerald-400 to-cyan-400'],
                        ] as $proof)
                            <div class="flex min-h-11 items-center gap-2 rounded-2xl border border-white/10 bg-white/[0.055] px-3 py-2 text-left shadow-lg shadow-black/10 backdrop-blur-xl">
                                <span class="bon-icon-pulse h-2.5 w-2.5 shrink-0 rounded-full bg-gradient-to-br {{ $proof['accent'] }}"></span>
                                <span class="text-xs font-black text-slate-300">{{ $proof['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 grid gap-3 rounded-[1.5rem] border border-white/10 bg-slate-950/55 p-3 text-left shadow-2xl shadow-black/15 backdrop-blur-2xl sm:grid-cols-4 lg:max-w-3xl">
                        @foreach ([
                            ['value' => $formatBonCount($socialProofStats['businesses']), 'label' => 'бизнес профила', 'accent' => 'from-blue-400 to-cyan-300'],
                            ['value' => $formatBonCount($socialProofStats['freelancers']), 'label' => 'фрийлансъри', 'accent' => 'from-violet-400 to-fuchsia-300'],
                            ['value' => $formatBonCount($socialProofStats['open_jobs']), 'label' => 'отворени задачи', 'accent' => 'from-emerald-300 to-cyan-300'],
                            ['value' => $formatBonCount($socialProofStats['reviews']), 'label' => 'одобрени отзиви', 'accent' => 'from-amber-300 to-orange-300'],
                        ] as $stat)
                            <div class="rounded-2xl border border-white/10 bg-white/[0.055] px-4 py-3">
                                <p class="bg-gradient-to-r {{ $stat['accent'] }} bg-clip-text text-xl font-black text-transparent">{{ $stat['value'] }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-300">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <p class="mt-3 max-w-2xl text-center text-xs font-semibold leading-5 text-slate-500 lg:text-left">
                        BON показва trust signals като Verified, Premium, отзиви, активност и profile completeness, за да се вземат по-спокойни решения.
                    </p>

                </div>

                <div class="bon-home-orb-wrap relative mx-auto mt-4 w-full max-w-full overflow-hidden sm:mt-12 sm:max-w-5xl lg:mx-0 lg:mt-0 lg:max-w-[640px] lg:justify-self-end">
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
                        <span class="bon-orbit-icon bg-gradient-to-br from-blue-600 to-cyan-400">B</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Businesses</span>
                            <span class="block text-xs font-semibold text-slate-500">profiles & growth</span>
                        </span>
                        <span class="bon-orbit-status bg-emerald-400"></span>
                    </div>

                    <div class="bon-orbit-card left-10 top-[38%] hidden lg:flex" style="animation-delay: -1.4s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-sky-500 to-blue-600">F</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Freelancers</span>
                            <span class="block text-xs font-semibold text-slate-500">talent network</span>
                        </span>
                        <span class="bon-orbit-status bg-blue-400"></span>
                    </div>

                    <div class="bon-orbit-card bottom-12 left-4 hidden lg:flex" style="animation-delay: -2.1s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-violet-500 to-blue-500">U</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Users</span>
                            <span class="block text-xs font-semibold text-slate-500">requests & reviews</span>
                        </span>
                        <span class="bon-orbit-status bg-violet-400"></span>
                    </div>

                    <div class="bon-globe-stage bon-float">
                        <div class="bon-globe-glow bon-pulse"></div>
                        <div class="bon-globe-halo"></div>
                        <div class="bon-globe-halo-ring"></div>
                        <div class="bon-globe-shell"></div>
                        <div class="bon-globe-ring bon-globe-ring-outer"></div>
                        <div class="bon-globe-ring bon-globe-ring-inner"></div>
                        <div class="bon-globe-core">
                            <span class="bon-globe-network" aria-hidden="true">
                                <span class="bon-globe-link" style="left: 18%; top: 31%; width: 50%; transform: rotate(18deg);"></span>
                                <span class="bon-globe-link" style="left: 25%; top: 62%; width: 52%; transform: rotate(-22deg);"></span>
                                <span class="bon-globe-link" style="left: 32%; top: 18%; width: 34%; transform: rotate(74deg);"></span>
                                <span class="bon-globe-link" style="left: 50%; top: 28%; width: 32%; transform: rotate(116deg);"></span>
                                <span class="bon-globe-link" style="left: 18%; top: 50%; width: 58%; transform: rotate(0deg);"></span>
                                <span class="bon-globe-node" style="left: 16%; top: 28%; animation-delay: -.2s;"></span>
                                <span class="bon-globe-node" style="left: 48%; top: 16%; animation-delay: -1.1s;"></span>
                                <span class="bon-globe-node" style="left: 72%; top: 36%; animation-delay: -2s;"></span>
                                <span class="bon-globe-node" style="left: 26%; top: 64%; animation-delay: -2.7s;"></span>
                                <span class="bon-globe-node" style="left: 62%; top: 72%; animation-delay: -3.4s;"></span>
                            </span>
                            <span class="bon-globe-letter">B</span>
                        </div>
                    </div>

                    <div class="bon-orbit-card right-0 top-6 hidden lg:flex" style="animation-delay: -0.8s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-fuchsia-500 to-pink-500">T</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Tools</span>
                            <span class="block text-xs font-semibold text-slate-500">insights & reports</span>
                        </span>
                        <span class="bon-orbit-status bg-pink-400"></span>
                    </div>

                    <div class="bon-orbit-card right-10 top-[39%] hidden lg:flex" style="animation-delay: -1.8s;">
                        <span class="bon-orbit-icon bg-gradient-to-br from-violet-600 to-fuchsia-500">O</span>
                        <span>
                            <span class="block text-sm font-black text-[#070B1F]">Opportunities</span>
                            <span class="block text-xs font-semibold text-slate-500">tasks & growth</span>
                        </span>
                        <span class="bon-orbit-status bg-violet-400"></span>
                    </div>
                </div>

                <div class="bon-home-mobile-chips mx-auto mt-4 grid max-w-md grid-cols-2 gap-2 lg:hidden">
                    @foreach ([
                        ['label' => 'Businesses', 'detail' => 'Profiles & growth', 'accent' => 'from-blue-600 to-cyan-400'],
                        ['label' => 'Freelancers', 'detail' => 'Talent network', 'accent' => 'from-sky-500 to-blue-600'],
                        ['label' => 'Users', 'detail' => 'Requests & reviews', 'accent' => 'from-violet-500 to-blue-500'],
                        ['label' => 'Tools', 'detail' => 'Insights & reports', 'accent' => 'from-fuchsia-500 to-pink-500'],
                        ['label' => 'Opportunities', 'detail' => 'Tasks & growth', 'accent' => 'from-violet-600 to-fuchsia-500'],
                    ] as $chip)
                        <div class="flex min-h-14 items-center gap-2 rounded-2xl border border-white/70 bg-white/76 px-3 py-2 text-left shadow-xl shadow-blue-900/5 backdrop-blur-xl">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-gradient-to-br {{ $chip['accent'] }}"></span>
                            <span>
                                <span class="block text-xs font-black leading-4 text-slate-700">{{ $chip['label'] }}</span>
                                <span class="block text-[11px] font-semibold leading-4 text-slate-500">{{ $chip['detail'] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
                </div>
            </section>

            <section class="mx-auto mt-7 w-full max-w-[1180px] sm:mt-10">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['label' => 'Видимост', 'value' => 'Profiles', 'text' => 'Профили, снимки, отзиви и сигнали за доверие.', 'icon' => 'V', 'accent' => 'from-cyan-400 to-blue-500'],
                        ['label' => 'Растеж', 'value' => 'Growth', 'text' => 'Инструменти, препоръки и следващи действия.', 'icon' => 'G', 'accent' => 'from-emerald-400 to-cyan-400'],
                        ['label' => 'Доверие', 'value' => 'Trust', 'text' => 'Badges, рейтинг и по-добро представяне.', 'icon' => 'T', 'accent' => 'from-violet-500 to-fuchsia-500'],
                        ['label' => 'Действие', 'value' => 'Ops', 'text' => 'Задачи, консултации, booking и Premium опции.', 'icon' => 'O', 'accent' => 'from-amber-400 to-rose-400'],
                    ] as $stat)
                        <article class="bon-home-stat-card rounded-[1.35rem] border border-white/10 bg-white/[0.055] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl transition hover:-translate-y-1 sm:rounded-[1.75rem] sm:p-5">
                            <div class="flex items-center gap-3">
                                <span class="bon-icon-pulse grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br {{ $stat['accent'] }} text-sm font-black text-white shadow-lg shadow-blue-500/20">{{ $stat['icon'] }}</span>
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500">{{ $stat['label'] }}</p>
                                    <p class="mt-1 text-lg font-black leading-none text-white">{{ $stat['value'] }}</p>
                                </div>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-slate-400">{{ $stat['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="bon-search" class="mx-auto mt-8 w-full max-w-[1180px] sm:mt-12">
                <div class="rounded-[1.65rem] border border-white/10 bg-white/[0.06] p-4 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:rounded-[2rem] sm:p-6 lg:p-7">
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.22em] text-blue-300">Consumer discovery</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-white sm:text-3xl">Намери правилния профил в BON.</h2>
                        </div>
                        <p class="max-w-md text-sm leading-6 text-slate-400">
                            Търсачката е вход към платформата: помага на потребителите да откриват бизнеси, услуги и специалисти, докато BON остава система за инструменти, доверие и растеж.
                        </p>
                    </div>

                    <form action="{{ route('search') }}" method="GET" class="bon-home-search grid gap-3 text-left sm:grid-cols-[1fr_0.75fr_0.85fr_auto] sm:items-end">
                        <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                            Услуга, име или специалист
                            <input name="q" value="{{ request('q') }}" placeholder="фризьор, уеб дизайн, салон..." class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal text-[#070B1F] outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                        </label>
                        <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                            Град
                            <input name="city" value="{{ request('city') }}" placeholder="София" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal text-[#070B1F] outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                        </label>
                        <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                            Категория
                            <select name="category" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal text-[#070B1F] outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                                <option value="">Всички категории</option>
                                @foreach($smartCategories as $category)
                                    <option value="{{ $category }}" @selected((string) $smartCategory === (string) $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5">
                            Намери в BON
                        </button>
                    </form>

                    <div class="mt-5 rounded-[1.35rem] border border-white/10 bg-[#061126]/70 p-3 shadow-inner shadow-black/20 sm:mt-6 sm:p-4">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-300">Категории в BON</p>
                                <p class="mt-1 text-sm leading-6 text-slate-400">Бързи входове към consumer discovery слоя, без BON да се превръща в обикновен каталог.</p>
                            </div>
                        </div>

                        <div class="bon-category-chip-grid mt-3">
                            @foreach(collect($smartCategories) as $category)
                                <a href="{{ route('search', ['category' => $category]) }}" class="bon-category-chip">
                                    {{ $category }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-7 grid w-full max-w-[1440px] gap-3 sm:mt-10 md:grid-cols-3">
                @foreach ([
                    ['label' => 'За бизнеси', 'icon' => 'B', 'title' => 'Операционна система за видимост и растеж.', 'text' => 'Инструменти, диагностика, статистика, задачи, резервации, отзиви и платени опции за по-силен профил.', 'href' => route('business.landing'), 'cta' => 'Виж бизнес платформата', 'accent' => 'from-blue-500 to-cyan-400'],
                    ['label' => 'За фрийлансъри', 'icon' => 'F', 'title' => 'Професионален профил, портфолио и нови възможности.', 'text' => 'Умения, проекти, оферти, кандидатури и доверие, подредени в работещ фриланс профил.', 'href' => route('bon.freelancers'), 'cta' => 'Виж фрийланс мрежата', 'accent' => 'from-violet-500 to-fuchsia-500'],
                    ['label' => 'За потребители', 'icon' => 'U', 'title' => 'По-лесен път до правилната услуга.', 'text' => 'Търсене, любими, запитвания, резервации и отзиви без усещане за хаотичен каталог.', 'href' => route('search'), 'cta' => 'Намери подходящ профил', 'accent' => 'from-amber-400 to-cyan-400'],
                ] as $audience)
                    <article class="group rounded-[1.45rem] border border-white/10 bg-white/[0.06] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl transition hover:-translate-y-1 hover:border-blue-400/30 sm:rounded-[2rem] sm:p-6">
                        <div class="flex items-center gap-3">
                            <span class="bon-icon-pulse grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br {{ $audience['accent'] }} text-sm font-black text-white shadow-lg shadow-violet-500/30">{{ $audience['icon'] }}</span>
                            <span class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">{{ $audience['label'] }}</span>
                        </div>
                        <h2 class="mt-4 text-xl font-black leading-tight tracking-tight text-white sm:text-2xl">{{ $audience['title'] }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-400 sm:text-[15px] sm:leading-7">{{ $audience['text'] }}</p>
                        <a href="{{ $audience['href'] }}" class="mt-5 inline-flex items-center gap-2 text-sm font-black text-blue-300 transition group-hover:text-violet-200">
                            {{ $audience['cta'] }}
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </article>
                @endforeach
            </section>

            <section id="bon-how" class="mx-auto mt-9 w-full max-w-[1440px] sm:mt-16">
                <div class="relative overflow-hidden rounded-[1.65rem] border border-white/10 bg-white/[0.055] p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-emerald-400/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-amber-400/10 blur-3xl"></div>

                    <div class="relative flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-300">How BON works</p>
                            <h2 class="mt-3 text-[26px] font-black leading-tight tracking-tight text-white sm:text-5xl">
                                От профил и доверие до реално действие.
                            </h2>
                            <p class="mt-3 text-[15px] leading-7 text-slate-400 sm:text-base sm:leading-8">
                                BON комбинира публично присъствие, фрийланс мрежа, бизнес инструменти и consumer discovery в един подреден flow.
                            </p>
                        </div>
                        <a href="{{ route('bon.tools') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-white/12 bg-white/[0.07] px-6 text-sm font-black text-slate-100 shadow-lg shadow-black/10 transition hover:border-cyan-300/40 sm:w-auto">
                            Виж инструментите
                        </a>
                    </div>

                    <div class="relative mt-7 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ([
                            ['step' => '01', 'title' => 'Създай силен профил', 'text' => 'Бизнесът или фрийлансърът подрежда услуги, снимки, портфолио, контакти и доверителни сигнали.', 'accent' => 'from-cyan-400 to-blue-500'],
                            ['step' => '02', 'title' => 'Добави инструменти', 'text' => 'Активирай анализи, задачи, консултации, статистика, booking и paid options според нуждите.', 'accent' => 'from-violet-500 to-fuchsia-500'],
                            ['step' => '03', 'title' => 'Покажи доверие', 'text' => 'BON изкарва напред badges, reviews, Trust Score, Premium сигнали и актуална активност.', 'accent' => 'from-emerald-400 to-cyan-400'],
                            ['step' => '04', 'title' => 'Превърни интереса в действие', 'text' => 'Потребителите изпращат запитвания, бизнесите публикуват задачи, а фрийлансърите кандидатстват по проекти.', 'accent' => 'from-amber-400 to-rose-400'],
                        ] as $flow)
                            <article class="bon-step-card rounded-[1.45rem] border border-white/10 bg-[#071326]/80 p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl transition hover:-translate-y-1 sm:rounded-[1.75rem]">
                                <div class="bon-icon-pulse mb-5 inline-flex h-11 min-w-11 items-center justify-center rounded-2xl bg-gradient-to-br {{ $flow['accent'] }} px-3 text-sm font-black text-white shadow-lg shadow-blue-500/20">
                                    {{ $flow['step'] }}
                                </div>
                                <h3 class="text-lg font-black tracking-tight text-white">{{ $flow['title'] }}</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-400">{{ $flow['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="bon-tools" class="mx-auto mt-9 w-full max-w-[1440px] sm:mt-16">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Business tools</p>
                    <h2 class="mt-3 text-[26px] font-black leading-tight tracking-tight sm:text-5xl">BON е работна система за решения, не каталог.</h2>
                    <p class="mt-3 max-w-3xl text-[15px] leading-7 text-slate-600 sm:mt-4 sm:text-base sm:leading-8">
                        Бизнесът получава инструменти за анализ, стратегия, задачи, фрийлансъри, резервации, статистика и платени опции за растеж.
                    </p>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ([
                        ['title' => 'Бизнес диагностика', 'text' => 'Подрежда проблема, причините и следващите действия, преди да харчиш време и бюджет.', 'accent' => 'from-blue-500 to-cyan-400'],
                        ['title' => 'Стратегия за развитие', 'text' => 'Growth насоки за профил, оферта, комуникация, снимки, услуги и видимост.', 'accent' => 'from-violet-500 to-fuchsia-500'],
                        ['title' => 'Консултации', 'text' => 'Платени разговори и насоки от BON за по-добро позициониране и повече възможности.', 'accent' => 'from-amber-400 to-fuchsia-500'],
                        ['title' => 'Анализ приходи/разходи', 'text' => 'Финансов поглед върху маржове, разходи, персонал, break-even и бизнес здраве.', 'accent' => 'from-emerald-400 to-cyan-400'],
                        ['title' => 'Публикуване на задачи', 'text' => 'Бизнесите могат да публикуват задачи към специалисти и да получават кандидатури.', 'accent' => 'from-amber-400 to-orange-500'],
                        ['title' => 'Намиране на фрийлансъри', 'text' => 'Достъп до профили с умения, портфолио, доверие, отзиви и кандидатури.', 'accent' => 'from-violet-500 to-emerald-400'],
                        ['title' => 'Статистика', 'text' => 'Преглеждания, кликове, запитвания, репутация и сигнали за подобрение.', 'accent' => 'from-emerald-400 to-cyan-400'],
                        ['title' => 'Резервации / booking', 'text' => 'Заключена premium/add-on функция за бизнеси, които работят с часове и резервации.', 'accent' => 'from-amber-400 to-rose-500'],
                        ['title' => 'Абонамент / план', 'text' => 'Standard и Premium отключват различни нива на видимост, инструменти и подкрепа.', 'accent' => 'from-cyan-400 to-violet-500'],
                    ] as $tool)
                        <article class="rounded-[1.45rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl transition hover:-translate-y-1 hover:border-blue-400/30 sm:rounded-[1.75rem]">
                            <span class="bon-icon-pulse mb-4 block h-1.5 w-14 rounded-full bg-gradient-to-r {{ $tool['accent'] }}"></span>
                            <h3 class="text-lg font-black tracking-tight text-white">{{ $tool['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-400">{{ $tool['text'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-300">Interactive tools</p>
                    <h3 class="mt-2 text-2xl font-black tracking-tight text-white sm:text-3xl">Бързи инструменти, които показват как работи BON.</h3>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($businessTools as $tool)
                        @php
                            $modalId = 'bon-tool-modal-' . $loop->index;
                        @endphp
                        <button type="button" data-tool-open="{{ $modalId }}" class="group w-full rounded-[1.45rem] border border-white/70 bg-white/75 p-4 text-left shadow-2xl shadow-blue-900/5 backdrop-blur-2xl transition hover:-translate-y-1 hover:border-blue-200/80 hover:shadow-blue-900/10 sm:rounded-[2rem] sm:p-6">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br {{ $tool['color'] }} text-lg font-black text-white shadow-lg shadow-violet-500/20 sm:mb-5 sm:h-12 sm:w-12 sm:text-xl">
                                {{ $tool['icon'] }}
                            </div>
                            <h3 class="text-lg font-black tracking-tight sm:text-xl">{{ $tool['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 sm:mt-3 sm:leading-7">{{ $tool['text'] }}</p>
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
                    <div id="{{ $modalId }}" data-tool-modal class="fixed inset-0 z-[100] hidden px-2.5 py-2.5 sm:px-6 sm:py-5" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="{{ $modalId }}-title">
                        <button type="button" data-tool-overlay class="absolute inset-0 bg-slate-950/45 opacity-0 backdrop-blur-md transition-opacity duration-300" aria-label="Затвори"></button>

                        <div class="relative z-10 mx-auto flex min-h-full max-w-3xl items-center justify-center">
                            <div data-tool-panel class="max-h-[min(calc(100dvh-1rem),48rem)] w-full translate-y-4 scale-95 overflow-y-auto rounded-[1.35rem] border border-white/70 bg-white/[0.94] p-4 opacity-0 shadow-2xl shadow-blue-950/20 backdrop-blur-2xl transition duration-300 sm:rounded-[2rem] sm:p-7">
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

            <section class="mx-auto mt-12 w-full max-w-[1440px] sm:mt-16">
                <div class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-300">Freelancer Network</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight text-white sm:text-5xl">Мрежа от специалисти, които могат да изпълнят правилната задача.</h2>
                        <p class="mt-4 text-base leading-8 text-slate-400">
                            Фрийлансърите в BON имат професионален профил, портфолио, умения, кандидатури, отзиви и достъп до задачи от бизнеси.
                        </p>
                        <a href="{{ route('bon.freelancers') }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-white/12 bg-white/[0.06] px-6 text-sm font-black text-slate-100 transition hover:-translate-y-0.5 hover:border-violet-300/40 sm:w-auto">
                            Виж фрийланс мрежата
                        </a>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ([
                            ['title' => 'Проекти / Портфолио', 'text' => 'Завършени работи, снимки, линкове, резултати и примери от реална работа.'],
                            ['title' => 'Кандидатури', 'text' => 'Оферти към задачи с цена, срок, съобщение и статус.'],
                            ['title' => 'Умения', 'text' => 'Категории, услуги, технологии, стил, опит и начин на работа.'],
                            ['title' => 'Отзиви', 'text' => 'Рейтинг, доверие, завършени проекти и сигнали за качество.'],
                            ['title' => 'Статистика', 'text' => 'Преглеждания, покани, кликове, кандидатури и приети проекти.'],
                            ['title' => 'Намери задачи', 'text' => 'Достъп до публикувани задачи от бизнеси, които търсят изпълнение.'],
                        ] as $item)
                            <article class="rounded-[1.45rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
                                <h3 class="text-lg font-black text-white">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">{{ $item['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-12 w-full max-w-[1440px] rounded-[1.65rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:mt-16 sm:rounded-[2rem] sm:p-8">
                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-300">Consumer experience</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight text-white sm:text-5xl">Потребителите стигат по-лесно до правилния бизнес.</h2>
                        <p class="mt-4 text-base leading-8 text-slate-400">
                            Търсенето е само вход. След него BON помага с профили, доверие, любими, запитвания, резервации и отзиви.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ([
                            'Търсене на бизнеси',
                            'Любими профили',
                            'Резервации и запитвания',
                            'Отзиви и рейтинг',
                            'Подходяща услуга',
                            'Проверени профили',
                        ] as $item)
                            <div class="flex min-h-20 items-center gap-3 rounded-3xl border border-white/10 bg-[#071326]/80 p-4">
                                <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-br from-blue-400 to-fuchsia-400 shadow-lg shadow-blue-500/30"></span>
                                <span class="text-sm font-black text-slate-100">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-12 w-full max-w-[1440px] sm:mt-16">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-300">Consultations & paid options</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight text-white sm:text-5xl">Допълнителна помощ, когато бизнесът иска по-бърз напредък.</h2>
                        <p class="mt-4 text-base leading-8 text-slate-400">
                            Платените опции са отделни от основната логика: визуално показваме възможностите, без да създаваме нов checkout flow.
                        </p>
                    </div>
                    <a href="{{ route('plans') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 sm:w-auto">
                        Виж планове и опции
                    </a>
                </div>

                <div class="mt-7 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ([
                        ['title' => 'Бизнес консултация', 'text' => 'Разговор за профил, оферта, видимост и следващи действия.'],
                        ['title' => 'Growth пакет', 'text' => 'Структуриран план за подобрение на представянето и повече възможности.'],
                        ['title' => 'Help / Setup пакет', 'text' => 'BON помага с подреждане на профил, снимки, услуги и описание.'],
                        ['title' => 'Premium visibility', 'text' => 'По-силно позициониране, badge, статистика и повече инструменти.'],
                        ['title' => 'Advanced tools', 'text' => 'Разширени доклади, анализ, препоръки и business health сигнали.'],
                        ['title' => 'Booking tools', 'text' => 'Резервации и часове като add-on за бизнеси, за които има смисъл.'],
                        ['title' => 'Subscription plans', 'text' => 'Standard и Premium като основа за бизнес присъствие и инструменти.'],
                        ['title' => 'Early partner support', 'text' => 'Приоритетна помощ за бизнеси в beta периода на BON.'],
                    ] as $option)
                        <article class="rounded-[1.45rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
                            <h3 class="text-lg font-black text-white">{{ $option['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-400">{{ $option['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="recommended-specialists" class="mx-auto mt-12 w-full max-w-[1440px] sm:mt-16">
                <div class="rounded-[2rem] border border-white/70 bg-white/75 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7 lg:p-8">
                    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-end">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Network intelligence</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-4xl">BON подрежда профили и задачи според контекста.</h2>
                            <p class="mt-4 max-w-3xl text-base leading-8 text-slate-600">
                                Категорията е само сигнал. BON комбинира профили, задачи, Trust Score, рейтинг, активност и Premium видимост,
                                за да покаже по-подходящи следващи възможности.
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
                            <div data-empty-state class="rounded-[1.75rem] border border-dashed border-slate-200 bg-white/70 p-6 text-center text-sm leading-6 text-slate-500 md:col-span-2 xl:col-span-4">
                                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-lg font-black text-white shadow-lg shadow-violet-500/25">B</div>
                                <p class="mt-4 text-xl font-black text-[#070B1F]">Бъди сред първите доверени профили в тази категория.</p>
                                <p class="mx-auto mt-2 max-w-2xl">Когато има повече бизнеси и фрийлансъри, BON ще подрежда профилите по Premium статус, Trust Score, рейтинг и активност.</p>
                                <div class="mt-5 flex flex-col justify-center gap-3 sm:flex-row">
                                    <a href="{{ route('business.landing') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white">Добави бизнес</a>
                                    <a href="{{ route('register', ['role' => 'freelancer']) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700">Стани фрийлансър</a>
                                </div>
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
                                    <div data-empty-state class="rounded-3xl border border-dashed border-slate-200 bg-white/70 p-5 text-sm leading-6 text-slate-500">
                                        <p class="font-black text-[#070B1F]">Няма активни обяви за избраната категория.</p>
                                        <p class="mt-2">Публикувайте първата нужда и BON ще я покаже на подходящи фрийлансъри и специалисти.</p>
                                        <a href="{{ route('request.service', array_filter(['category' => $smartCategory])) }}" onclick="window.trackBonEvent('service_request_start', { source: 'smart_jobs_empty' })" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-violet-500/20">
                                            Публикувай заявка
                                        </a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-white/80 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-6 text-white shadow-2xl shadow-violet-500/20">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-white/65">Публикувай нужда</p>
                            <h3 class="mt-3 text-2xl font-black">Искаш BON да подаде нуждата към правилните профили?</h3>
                            <p class="mt-3 text-sm leading-7 text-white/75">
                                Опиши проекта или нуждата си и BON ще помогне профилите в правилната категория да видят контекста.
                            </p>
                            <a href="{{ route('request.service', array_filter(['category' => $smartCategory])) }}" onclick="window.trackBonEvent('service_request_start', { source: 'home_cta' })" class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-white px-5 text-sm font-black text-violet-700 shadow-xl shadow-violet-950/10 sm:w-auto">
                                Публикувай заявка
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-10 grid w-full max-w-[1440px] gap-6 sm:mt-14 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
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
                        <a href="{{ route('register') }}" data-track="cta_business_signup" onclick="window.trackBonEvent('sign_up_start', { source: 'financial_analysis_cta' })" class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/25 sm:w-auto">
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

            <section class="mx-auto mt-10 grid w-full max-w-[1440px] gap-6 sm:mt-14 lg:grid-cols-2">
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

            <section class="mx-auto mt-10 w-full max-w-[1440px] rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/8 backdrop-blur-2xl sm:mt-14 sm:rounded-[2rem] sm:p-8">
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

            <section class="mx-auto mt-10 w-full max-w-[1440px] sm:mt-14">
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

            <section class="mx-auto mt-10 w-full max-w-[1440px] rounded-[1.65rem] border border-white/70 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-2xl shadow-violet-500/20 sm:mt-14 sm:rounded-[2rem] sm:p-8">
                <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-white/70">Започни с BON</p>
                        <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Избери своя вход към business operating network.</h2>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[38rem] xl:min-w-[44rem]">
                        <a href="{{ route('business.landing') }}" data-track="cta_business_signup" onclick="window.trackBonEvent('business_registration_start', { source: 'final_cta' })" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-white px-5 text-sm font-black text-[#070B1F] shadow-xl">
                            Създай бизнес профил
                        </a>
                        <a href="{{ route('register', ['role' => 'freelancer']) }}" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'final_cta' })" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-5 text-sm font-black text-white backdrop-blur-xl">
                            Регистрирай се като фрийлансър
                        </a>
                        <a href="{{ route('search') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-5 text-sm font-black text-white backdrop-blur-xl">
                            Намери бизнес
                        </a>
                    </div>
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
                        document.documentElement.classList.remove('bon-modal-open');
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
                document.documentElement.classList.add('bon-modal-open');
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

            window.addEventListener('pagehide', () => {
                if (activeModal) {
                    activeModal.classList.add('hidden');
                    activeModal.setAttribute('aria-hidden', 'true');
                    activeModal = null;
                }

                document.documentElement.classList.remove('bon-modal-open');
                document.body.classList.remove('bon-modal-open');
            });

            window.addEventListener('pageshow', () => {
                const visibleModal = document.querySelector('[data-tool-modal]:not(.hidden)');

                if (!visibleModal) {
                    activeModal = null;
                    document.documentElement.classList.remove('bon-modal-open');
                    document.body.classList.remove('bon-modal-open');
                }
            });
        });
    </script>
</body>
</html>

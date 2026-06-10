@php
    $paths = [
        [
            'id' => 'business-diagnosis',
            'eyebrow' => 'Business Diagnosis',
            'title' => 'Имам бизнес проблем',
            'description' => 'Открий какво спира бизнеса ти и какви действия трябва да предприемеш след това.',
            'button' => 'Започни диагностика',
            'href' => route('bon.business-problem'),
            'accent' => 'from-blue-600 via-violet-600 to-indigo-600',
            'glow' => 'shadow-blue-600/20',
            'icon' => '↯',
            'chips' => ['Диагностика', 'Стратегия', 'Действия'],
        ],
        [
            'id' => 'consumer-need',
            'eyebrow' => 'BON Guide',
            'title' => 'Търся решение',
            'description' => 'Опиши какво ти трябва и BON ще те насочи към подходящ бизнес, услуга, оферта или свободно време.',
            'button' => 'Опиши нужда',
            'href' => route('bon.consumers'),
            'accent' => 'from-fuchsia-500 via-pink-500 to-rose-500',
            'glow' => 'shadow-pink-500/20',
            'icon' => '⌕',
            'chips' => ['Нужда', 'Оферта', 'Свободен час'],
        ],
        [
            'id' => 'talent-application',
            'eyebrow' => 'BON Talent Network',
            'title' => 'Искам да съм специалист',
            'description' => 'Кандидатствай за BON Talent Network и получавай възможности от бизнеси с реални проблеми за решаване.',
            'button' => 'Кандидатствай като специалист',
            'href' => auth()->user()?->isFreelancer() ? route('freelancer.credits.index') : route('bon.freelancers'),
            'accent' => 'from-violet-600 via-blue-600 to-fuchsia-500',
            'glow' => 'shadow-violet-600/20',
            'icon' => '✦',
            'chips' => ['Експертиза', 'Проекти', 'Растеж'],
        ],
    ];

    $steps = [
        ['title' => 'Избираш посока', 'text' => 'Бизнес, потребител или специалист.'],
        ['title' => 'Описваш проблема или нуждата', 'text' => 'BON събира контекста, за да разбере ситуацията.'],
        ['title' => 'Получаваш следваща стъпка', 'text' => 'Насока, решение или връзка с правилния човек.'],
        ['title' => 'Действаш', 'text' => 'Целта е да стигнеш от проблем до реално решение.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Добре дошъл в BON</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes bon-onboarding-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .bon-grid {
            background-image:
                linear-gradient(to right, rgba(37, 99, 235, .08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, .08) 1px, transparent 1px);
            background-size: 72px 72px;
        }

        .bon-float {
            animation: bon-onboarding-float 7s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased">
    <main class="relative min-h-screen overflow-hidden bg-[#F8FAFF] text-[#070B1F]">
        <div class="pointer-events-none absolute -left-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-10 h-[32rem] w-[32rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-1/3 h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/15 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-18rem] left-1/4 h-[30rem] w-[30rem] rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 bon-grid opacity-[0.24]"></div>

        <div class="relative z-10 px-4 py-5 sm:px-6 lg:px-8">
            <header class="mx-auto flex max-w-7xl items-center justify-between rounded-[2rem] border border-white/70 bg-white/75 px-5 py-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-4">
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white shadow-xl shadow-violet-500/25">
                        <span class="relative z-10">B</span>
                        <div class="absolute inset-0 rounded-2xl bg-white/20"></div>
                    </div>

                    <div class="leading-tight">
                        <div class="text-2xl font-black tracking-tight">BON</div>
                        <div class="hidden text-sm font-medium text-slate-500 sm:block">Business Operating Network</div>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="hidden rounded-2xl border border-slate-200/80 bg-white/70 px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-600 sm:inline-flex">
                        Начало
                    </a>
                    <a href="{{ route('dashboard') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-bold text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5">
                        Dashboard
                    </a>
                </div>
            </header>

            <section class="mx-auto max-w-7xl pb-14 pt-14 sm:pt-16 lg:pt-20">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-200/70 bg-white/80 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm shadow-blue-900/5 backdrop-blur-xl">
                        <span class="text-violet-600">✦</span>
                        Добре дошъл в BON
                    </div>

                    <h1 class="mt-6 text-4xl font-black tracking-tight text-[#070B1F] sm:text-5xl lg:text-6xl">
                        Как искаш да използваш BON?
                    </h1>

                    <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl">
                        Избери посоката си, за да те насочим към правилната следваща стъпка.
                    </p>
                </div>

                <div class="mt-12 grid gap-5 lg:grid-cols-3 lg:gap-6">
                    @foreach ($paths as $path)
                        <article id="{{ $path['id'] }}" class="group relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl transition duration-300 hover:-translate-y-1 sm:p-7">
                            <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent"></div>
                            <div class="absolute -right-12 -top-12 h-36 w-36 rounded-full bg-gradient-to-br {{ $path['accent'] }} opacity-10 blur-2xl"></div>

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $path['accent'] }} text-2xl font-black text-white shadow-xl {{ $path['glow'] }}">
                                {{ $path['icon'] }}
                            </div>

                            <div class="mt-6 text-xs font-black uppercase tracking-[0.22em] text-slate-500">
                                {{ $path['eyebrow'] }}
                            </div>

                            <h2 class="mt-3 text-2xl font-black tracking-tight text-[#070B1F] sm:text-3xl">
                                {{ $path['title'] }}
                            </h2>

                            <p class="mt-4 min-h-[6.5rem] text-base leading-7 text-slate-600">
                                {{ $path['description'] }}
                            </p>

                            <a href="{{ $path['href'] }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r {{ $path['accent'] }} px-5 py-3 text-sm font-black text-white shadow-xl {{ $path['glow'] }} transition hover:-translate-y-0.5">
                                {{ $path['button'] }}
                                <span>→</span>
                            </a>

                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($path['chips'] as $chip)
                                    <span class="rounded-full border border-white/70 bg-white/75 px-3 py-1 text-xs font-bold text-slate-600 shadow-sm">
                                        {{ $chip }}
                                    </span>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>

                <section class="relative mt-12 overflow-hidden rounded-[2.25rem] border border-white/70 bg-white/75 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7 lg:p-8">
                    <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gradient-to-br from-blue-500/15 via-violet-500/15 to-fuchsia-500/15 blur-3xl"></div>

                    <div class="relative grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-violet-200/70 bg-white/80 px-4 py-2 text-sm font-bold text-violet-700">
                                <span>◎</span>
                                Как работи BON?
                            </div>
                            <h2 class="mt-5 text-3xl font-black tracking-tight text-[#070B1F] sm:text-4xl">
                                От избор към ясно действие.
                            </h2>
                            <p class="mt-4 text-base leading-7 text-slate-600">
                                Първата стъпка е проста: казваш коя роля имаш в BON, а системата те насочва към правилния flow без стар dashboard шум.
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($steps as $index => $step)
                                <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-xl shadow-blue-900/5">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-sm font-black text-white">
                                        {{ $index + 1 }}
                                    </div>
                                    <h3 class="mt-4 text-lg font-black text-[#070B1F]">
                                        {{ $step['title'] }}
                                    </h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        {{ $step['text'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </main>
</body>
</html>

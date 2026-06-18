@php
    $categories = $categories ?? \App\Support\CategoryCatalog::names()->all();

    $freelancers = $freelancers ?? collect();

    $steps = [
        ['title' => 'Клиент публикува проект', 'text' => 'Задачата има категория, бюджет, срок, начин на работа и кратко описание.'],
        ['title' => 'Фрийлансъри кандидатстват', 'text' => 'Всеки специалист изпраща оферта с цена, срок, съобщение и линк към портфолио.'],
        ['title' => 'Клиентът сравнява оферти', 'text' => 'Виждат се цена, срок, рейтинг, Trust Score, опит и профил на кандидата.'],
        ['title' => 'Избира се подходящ човек', 'text' => 'BON помага изборът да е по доверие, качество и яснота, не само по най-ниска цена.'],
    ];

    $benefits = [
        'Профил с услуги, умения, портфолио и Trust Score',
        'Кандидатстване по реални проекти с кредитна система',
        'По-добро представяне пред клиенти и бизнеси',
        'Възможност ранните профили да получат повече видимост',
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Фрийлансъри | BON</title>
    <meta name="description" content="BON помага на фрийлансъри и независими специалисти да изградят професионален профил, да бъдат откривани по-лесно и да кандидатстват по реални проекти.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="bon-dark-page antialiased">
    <main class="relative min-h-screen overflow-x-hidden text-white">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(37,99,235,.18)_0%,rgba(2,6,23,.82)_42%,rgba(2,6,23,1)_100%)]"></div>
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-20 h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[28rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/16 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.24]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10">
            @include('partials.public-header')

            <section class="mx-auto max-w-[1440px] px-4 pb-5 pt-5 sm:px-6 sm:pb-8 sm:pt-8 lg:px-8">
                <nav class="flex gap-2 overflow-x-auto rounded-[1.5rem] border border-white/10 bg-slate-950/55 p-2 text-sm font-black shadow-xl shadow-black/20 backdrop-blur-2xl sm:flex-wrap sm:justify-center sm:rounded-[2rem]" aria-label="Freelance навигация">
                    <a href="#freelancer-directory" class="min-w-max rounded-2xl px-4 py-3 text-slate-200 hover:bg-white/10 hover:text-white">Намери фрилансър</a>
                    <a href="{{ route('freelancer.projects.create') }}" class="min-w-max rounded-2xl px-4 py-3 text-slate-200 hover:bg-white/10 hover:text-white">Публикувай проект</a>
                    <a href="{{ route('register', ['role' => 'freelancer']) }}" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'freelancers_nav' })" class="min-w-max rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-white shadow-lg shadow-violet-500/20">Стани фрилансър</a>
                    <a href="#how-freelance-works" class="min-w-max rounded-2xl px-4 py-3 text-slate-200 hover:bg-white/10 hover:text-white">Как работи</a>
                </nav>
            </section>

            <section class="mx-auto grid max-w-[1440px] gap-7 px-4 py-6 sm:px-6 sm:py-10 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:px-8 lg:py-14">
                <div>
                    <div class="inline-flex rounded-full border border-violet-300/20 bg-white/10 px-4 py-2 text-sm font-bold text-violet-100 shadow-sm shadow-black/20 backdrop-blur-xl">
                        BON Freelance Network
                    </div>
                    <h1 class="mt-5 max-w-4xl text-[32px] font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Покажи услугите си пред <span class="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">правилните клиенти</span>.
                    </h1>
                    <p class="mt-5 max-w-3xl text-[15px] leading-7 text-slate-300 sm:text-lg sm:leading-8">
                        BON помага на фрийлансъри и независими специалисти да изградят професионален профил,
                        да бъдат откривани по-лесно и да получават повече запитвания от клиенти с реални задачи.
                    </p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register', ['role' => 'freelancer']) }}" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'freelancers_hero' })" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 sm:w-auto">
                            Стани фрилансър
                        </a>
                        <a href="{{ route('freelancer.projects.index') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-6 text-sm font-black text-white shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300/30 hover:bg-white/15 sm:w-auto">
                            Виж проекти
                        </a>
                        <a href="#freelancer-directory" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-violet-300/20 bg-violet-500/10 px-6 text-sm font-black text-violet-100 shadow-sm transition hover:-translate-y-0.5 hover:bg-violet-500/15 sm:w-auto">
                            Намери фрилансър
                        </a>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-slate-400">
                        Ранните профили в BON ще имат предимство при първите кампании и препоръчани позиции.
                    </p>
                </div>

                <div class="relative">
                    <div class="absolute -inset-8 rounded-full bg-gradient-to-br from-blue-400/18 via-violet-400/18 to-fuchsia-400/18 blur-3xl"></div>
                    <div class="relative rounded-[1.5rem] border border-white/10 bg-slate-950/55 p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-200/80">Профил</p>
                                <h2 class="mt-2 text-2xl font-black tracking-tight text-white">Independent Specialist</h2>
                            </div>
                            <span class="rounded-full bg-gradient-to-r from-blue-600 to-fuchsia-500 px-4 py-2 text-xs font-black text-white shadow-lg shadow-violet-500/20">Trust Score 86</span>
                        </div>

                        <div class="mt-7 grid gap-3">
                            @foreach (['Услуги', 'Портфолио', 'Оферти', 'Отзиви', 'Контакт'] as $item)
                                <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.06] px-4 py-3 shadow-sm">
                                    <span class="text-sm font-black text-white">{{ $item }}</span>
                                    <span class="h-2 w-20 rounded-full bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500"></span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-7 rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-xl shadow-violet-500/20">
                            <p class="text-sm font-bold text-white/75">Какво отключва BON</p>
                            <p class="mt-2 text-xl font-black sm:text-2xl">По-добро представяне, доверие и по-качествени запитвания.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="how-freelance-works" class="mx-auto max-w-[1440px] px-4 py-5 sm:px-6 sm:py-8 lg:px-8">
                <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/55 p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200/80">Как работи</p>
                    <h2 class="mt-3 text-[26px] font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Логика като големите freelance платформи, но подредена за BON.
                    </h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($steps as $step)
                            <article class="rounded-3xl border border-white/10 bg-white/[0.06] p-5 shadow-lg shadow-black/20 backdrop-blur-xl">
                                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-sm font-black text-white">{{ $loop->iteration }}</div>
                                <h3 class="text-lg font-black text-white">{{ $step['title'] }}</h3>
                                <p class="mt-3 text-sm leading-7 text-slate-300">{{ $step['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-[1440px] gap-5 px-4 py-5 sm:gap-6 sm:px-6 sm:py-8 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/50 p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-200/80">Защо си струва</p>
                    <h2 class="mt-3 text-[24px] font-black leading-tight tracking-tight text-white sm:text-3xl">За специалисти, които искат да изглеждат сериозно онлайн.</h2>
                    <div class="mt-7 grid gap-3">
                        @foreach ($benefits as $benefit)
                            <div class="flex gap-3 rounded-2xl border border-white/10 bg-white/[0.06] p-4 text-sm font-bold text-slate-200 shadow-sm">
                                <span class="font-black text-blue-300">✓</span>
                                {{ $benefit }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/50 p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-200/80">Категории специалисти</p>
                    <h2 class="mt-3 text-[24px] font-black leading-tight tracking-tight text-white sm:text-3xl">Покажи експертизата си ясно.</h2>
                    <div class="mt-6 flex flex-wrap gap-2.5 sm:gap-3">
                        @foreach ($categories as $category)
                            <span class="rounded-full border border-white/10 bg-white/[0.06] px-4 py-2 text-sm font-black text-slate-200 shadow-sm">
                                {{ $category }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="freelancer-directory" class="mx-auto max-w-[1440px] px-4 py-5 sm:px-6 sm:py-8 lg:px-8">
                <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/55 p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200/80">Намери фрилансър</p>
                            <h2 class="mt-3 text-[26px] font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">Специалисти в BON</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                                Прегледай профили, умения, Trust Score, портфолио и начин на работа. Първите профили са demo/ранни участници за тест на секцията.
                            </p>
                        </div>
                        <a href="{{ route('freelancer.projects.create') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 sm:w-auto">
                            Публикувай проект
                        </a>
                    </div>

                    <form method="GET" action="{{ route('bon.freelancers') }}" class="mt-7 grid gap-3 lg:grid-cols-[1fr_180px_160px_160px_150px_150px_auto]">
                        <input name="q" value="{{ request('q') }}" placeholder="Име, умение, услуга..." class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/70 px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-300/60 focus:ring-4 focus:ring-blue-500/10">
                        <input name="category" list="freelancer-category-options" value="{{ request('category') }}" placeholder="Категория" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/70 px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-300/60 focus:ring-4 focus:ring-blue-500/10">
                        <datalist id="freelancer-category-options">
                            @foreach($categories as $category)
                                <option value="{{ $category }}"></option>
                            @endforeach
                        </datalist>
                        <input name="city" value="{{ request('city') }}" placeholder="Град" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/70 px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-300/60 focus:ring-4 focus:ring-blue-500/10">
                        <select name="work_mode" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/70 px-4 text-sm text-white outline-none focus:border-blue-300/60 focus:ring-4 focus:ring-blue-500/10">
                            <option value="">Работа</option>
                            <option value="online" @selected(request('work_mode') === 'online')>Онлайн</option>
                        </select>
                        <select name="rating" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/70 px-4 text-sm text-white outline-none focus:border-blue-300/60 focus:ring-4 focus:ring-blue-500/10">
                            <option value="">Рейтинг</option>
                            <option value="4.5" @selected(request('rating') === '4.5')>4.5+</option>
                            <option value="4" @selected(request('rating') === '4')>4.0+</option>
                        </select>
                        <select name="availability" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/70 px-4 text-sm text-white outline-none focus:border-blue-300/60 focus:ring-4 focus:ring-blue-500/10">
                            <option value="">Наличност</option>
                            <option value="available" @selected(request('availability') === 'available')>Активни скоро</option>
                        </select>
                        <button class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/20">Филтрирай</button>
                    </form>

                    <div class="mt-7 grid gap-4 lg:grid-cols-3">
                        @forelse($freelancers as $freelancer)
                            @php
                                $trust = data_get($freelancer, 'trust_summary') ?: $freelancer->trustSummary();
                                $skills = collect($freelancer->serviceCategories())
                                    ->map(fn ($profileCategory) => \App\Support\CategoryCatalog::displayName($profileCategory))
                                    ->filter()
                                    ->unique()
                                    ->take(4);
                                $cities = collect($freelancer->serviceCities())->take(3);
                                $headline = $freelancer->business_category ?: $skills->first() ?: 'Независим специалист';
                                $description = $freelancer->short_description ?: $freelancer->description ?: 'Фрийлансър в BON с услуги, портфолио и възможност за работа по проекти.';
                            @endphp
                            <article class="rounded-[1.5rem] border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-black/20 backdrop-blur-2xl transition hover:-translate-y-1 hover:border-blue-300/30 hover:bg-white/[0.09] sm:rounded-[2rem]">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white shadow-xl shadow-violet-500/20">
                                        {{ strtoupper(mb_substr($freelancer->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(array_slice($trust['badges'] ?? [], 0, 2) as $badge)
                                                <span class="rounded-full border border-blue-300/20 bg-blue-500/10 px-2.5 py-1 text-[11px] font-black text-blue-100">{{ $badge }}</span>
                                            @endforeach
                                        </div>
                                        <h3 class="mt-2 text-xl font-black leading-tight text-white">{{ $freelancer->name }}</h3>
                                        <p class="mt-1 text-sm font-bold text-violet-200">{{ $headline }}</p>
                                    </div>
                                </div>

                                <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-300">{{ $description }}</p>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @forelse($skills as $skill)
                                        <span class="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 text-xs font-black text-slate-200">{{ $skill }}</span>
                                    @empty
                                        <span class="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 text-xs font-black text-slate-200">Услуги по заявка</span>
                                    @endforelse
                                </div>

                                <div class="mt-5 grid gap-2 text-sm text-slate-300">
                                    <div class="flex justify-between gap-3 rounded-2xl border border-white/10 bg-slate-950/45 px-3 py-2">
                                        <span>Trust Score</span>
                                        <strong>{{ $trust['trust_score'] ?? 0 }}/100</strong>
                                    </div>
                                    <div class="flex justify-between gap-3 rounded-2xl border border-white/10 bg-slate-950/45 px-3 py-2">
                                        <span>Рейтинг</span>
                                        <strong>{{ ($trust['average_rating'] ?? null) ? number_format($trust['average_rating'], 1, '.', '') . '/5' : 'Няма още' }}</strong>
                                    </div>
                                    <div class="flex justify-between gap-3 rounded-2xl border border-white/10 bg-slate-950/45 px-3 py-2">
                                        <span>Работа</span>
                                        <strong>{{ $cities->isNotEmpty() ? $cities->implode(', ') : 'Онлайн' }}</strong>
                                    </div>
                                    <div class="flex justify-between gap-3 rounded-2xl border border-white/10 bg-slate-950/45 px-3 py-2">
                                        <span>Цена</span>
                                        <strong>По договаряне</strong>
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                    <a href="{{ route('freelancers.show', $freelancer) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-4 text-sm font-black text-white shadow-lg shadow-blue-600/20">
                                        Виж профил
                                    </a>
                                    <a href="{{ $freelancer->email ? 'mailto:' . $freelancer->email . '?subject=' . rawurlencode('Покана за проект през BON') : route('login') }}" onclick="window.trackBonEvent('contact_click', { source: 'freelancers_listing_invite', profile_id: '{{ $freelancer->id }}', profile_type: 'freelancer' })" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.08] px-4 text-sm font-black text-white shadow-sm">
                                        Покани
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-blue-300/25 bg-blue-500/10 p-8 text-center lg:col-span-3">
                                <p class="text-2xl font-black text-white">Бъди сред първите фрийлансъри в BON.</p>
                                <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-300">
                                    Скоро ще добавим още специалисти. Ако предлагаш услуги, създай профил и заеми ранна позиция в мрежата.
                                </p>
                                <a href="{{ route('register', ['role' => 'freelancer']) }}" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'freelancers_empty_state' })" class="mt-5 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white">
                                    Създай freelancer профил
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($freelancers, 'links'))
                        <div class="mt-7">{{ $freelancers->links() }}</div>
                    @endif
                </div>
            </section>

            <section class="mx-auto max-w-[1440px] px-4 pb-10 pt-5 sm:px-6 sm:pb-16 sm:pt-8 lg:px-8">
                <div class="rounded-[1.5rem] border border-white/70 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-2xl shadow-violet-500/20 sm:rounded-[2rem] sm:p-8">
                    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-white/70">Започни с BON</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Създай профил или публикувай първия проект.</h2>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <a href="{{ route('register', ['role' => 'freelancer']) }}" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'freelancers_final_cta' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl">
                                Стани фрилансър
                            </a>
                            <a href="{{ route('freelancer.projects.create') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/30 bg-white/10 px-6 text-sm font-black text-white shadow-xl">
                                Публикувай проект
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            @include('partials.public-footer')
        </div>
    </main>
</body>
</html>

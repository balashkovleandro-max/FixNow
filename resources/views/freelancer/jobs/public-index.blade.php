<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проекти за фрийлансъри | BON</title>
    <meta name="description" content="Разгледай активни BON проекти и задачи, по които фрийлансъри могат да кандидатстват с оферта.">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-clip bg-[#F8FAFF] text-[#070B1F]">
    <main class="relative min-h-screen overflow-x-clip">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10">
            @include('partials.public-header')

            <section class="mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-10 lg:px-8">
                <div class="rounded-[1.5rem] border border-white/70 bg-white/78 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">BON Projects</p>
                            <h1 class="mt-3 text-[32px] font-black leading-tight tracking-tight sm:text-5xl">Активни проекти и задачи</h1>
                            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                                Клиенти и бизнеси публикуват конкретна задача. Фрийлансърите изпращат оферта с цена, срок и съобщение.
                            </p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('freelancer.projects.create') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                                Публикувай проект
                            </a>
                            <a href="{{ route('register', ['role' => 'freelancer']) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-6 text-sm font-black text-slate-700 shadow-sm">
                                Стани фрилансър
                            </a>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('freelancer.projects.index') }}" class="mt-7 grid gap-3 md:grid-cols-[1fr_200px_180px_180px_auto]">
                        <input name="q" value="{{ request('q') }}" placeholder="Търси по задача, умение или бизнес" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 text-sm outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        <input name="category" list="public-project-category-options" value="{{ request('category') }}" placeholder="Категория" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 text-sm outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        <datalist id="public-project-category-options">
                            @foreach($categories as $category)
                                <option value="{{ $category }}"></option>
                            @endforeach
                        </datalist>
                        <select name="work_mode" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 text-sm outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                            <option value="">Начин на работа</option>
                            <option value="online" @selected(request('work_mode') === 'online')>Онлайн</option>
                            <option value="onsite" @selected(request('work_mode') === 'onsite')>На място</option>
                            <option value="hybrid" @selected(request('work_mode') === 'hybrid')>Хибридно</option>
                        </select>
                        <input name="location" value="{{ request('location') }}" placeholder="Град" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 text-sm outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        <button class="min-h-12 rounded-2xl bg-slate-950 px-6 text-sm font-black text-white">Филтрирай</button>
                    </form>
                </div>

                <section class="mt-7 grid gap-5 lg:grid-cols-2">
                    @forelse($jobs as $job)
                        <article class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl transition hover:-translate-y-1 hover:shadow-blue-900/10 sm:rounded-[2rem] sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-violet-600">{{ $job->category ?: 'Проект' }}</p>
                                    <h2 class="mt-2 text-2xl font-black leading-tight">{{ $job->title }}</h2>
                                    <p class="mt-2 text-sm text-slate-500">
                                        {{ $job->business?->business_name ?: $job->business?->name ?: 'Клиент в BON' }}
                                        @if($job->location) · {{ $job->location }} @endif
                                    </p>
                                </div>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                                    {{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита
                                </span>
                            </div>

                            <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600">{{ $job->description }}</p>

                            <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold text-slate-500">
                                @if($job->budget)
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Бюджет: {{ number_format((float) $job->budget, 2, ',', ' ') }} €</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Бюджет: по договаряне</span>
                                @endif
                                @if($job->deadline)<span class="rounded-full bg-slate-100 px-3 py-1">Срок: {{ $job->deadline->format('d.m.Y') }}</span>@endif
                                @if($job->work_mode)<span class="rounded-full bg-slate-100 px-3 py-1">{{ ['online' => 'Онлайн', 'onsite' => 'На място', 'hybrid' => 'Хибридно'][$job->work_mode] ?? $job->work_mode }}</span>@endif
                                <span class="rounded-full bg-slate-100 px-3 py-1">{{ $job->applications_count }} кандидатури</span>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('freelancer.projects.show', $job) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/20">
                                    Виж детайли
                                </a>
                                <a href="{{ route('bon.freelancers') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 text-sm font-black text-slate-700 shadow-sm">
                                    Виж фрийлансъри
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-blue-200 bg-blue-50/70 p-8 text-center lg:col-span-2">
                            <p class="text-2xl font-black">Все още няма публикувани проекти.</p>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
                                Публикувай първия проект и BON ще го покаже на подходящи фрийлансъри.
                            </p>
                            <a href="{{ route('freelancer.projects.create') }}" class="mt-5 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white">
                                Публикувай първия проект
                            </a>
                        </div>
                    @endforelse
                </section>

                <div class="mt-8">{{ $jobs->links() }}</div>
            </section>

            @include('partials.public-footer')
        </div>
    </main>
</body>
</html>

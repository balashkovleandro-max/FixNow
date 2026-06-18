<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обяви за фрийлансъри | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bon-dark-page min-h-screen overflow-x-hidden text-white antialiased">
    <main class="relative min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">BON Jobs</p>
                        <p class="text-sm text-slate-500">Баланс: {{ $creditStats['available'] }} кредита</p>
                    </div>
                </a>
                <nav class="flex flex-wrap gap-2 text-sm font-bold">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Обзор</a>
                    <a href="{{ route('freelancer.credits.index') }}" class="rounded-2xl bg-blue-50 px-4 py-2 text-blue-700">Моите кредити</a>
                </nav>
            </header>

            <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Обяви от бизнеси</p>
                <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-4xl font-black tracking-tight sm:text-5xl">Кандидатствай с кредити.</h1>
                        <p class="mt-4 max-w-3xl text-lg leading-8 text-slate-600">Всяко кандидатстване струва {{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита. Виж задачата, подготви кратко съобщение и изпрати кандидатура.</p>
                    </div>
                    <a href="{{ route('freelancer.credits.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">Купи кредити</a>
                </div>

                <form method="GET" action="{{ route('freelancer.jobs.index') }}" class="mt-8 grid gap-3 md:grid-cols-[1fr_200px_180px_180px_auto]">
                    <input name="q" value="{{ request('q') }}" placeholder="Търси по задача, умение или бизнес" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                    <input name="category" list="freelancer-job-category-options" value="{{ request('category') }}" placeholder="Всички категории" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                    <datalist id="freelancer-job-category-options">
                        @foreach($categories as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                    <select name="work_mode" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        <option value="">Тип работа</option>
                        <option value="online" @selected(request('work_mode') === 'online')>Онлайн</option>
                        <option value="onsite" @selected(request('work_mode') === 'onsite')>На място</option>
                        <option value="hybrid" @selected(request('work_mode') === 'hybrid')>Хибридно</option>
                    </select>
                    <input name="location" value="{{ request('location') }}" placeholder="Локация" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                    <button class="min-h-12 rounded-2xl bg-slate-950 px-6 text-sm font-black text-white">Филтрирай</button>
                </form>
            </section>

            <section class="mt-8 grid gap-5 lg:grid-cols-2">
                @forelse($jobs as $job)
                    <article class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl transition hover:-translate-y-1 hover:shadow-blue-900/10">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.2em] text-violet-600">{{ $job->category ?: 'Проект' }}</p>
                                <h2 class="mt-2 text-2xl font-black">{{ $job->title }}</h2>
                                <p class="mt-2 text-sm text-slate-500">{{ $job->business?->business_name ?: $job->business?->name }} @if($job->location) · {{ $job->location }} @endif</p>
                            </div>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита</span>
                        </div>
                        <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600">{{ $job->description }}</p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold text-slate-500">
                            @if($job->budget)<span class="rounded-full bg-slate-100 px-3 py-1">Бюджет: {{ number_format((float) $job->budget, 2, ',', ' ') }} €</span>@endif
                            @if($job->deadline)<span class="rounded-full bg-slate-100 px-3 py-1">Срок: {{ $job->deadline->format('d.m.Y') }}</span>@endif
                            @if($job->work_mode)<span class="rounded-full bg-slate-100 px-3 py-1">{{ ['online' => 'Онлайн', 'onsite' => 'На място', 'hybrid' => 'Хибридно'][$job->work_mode] ?? $job->work_mode }}</span>@endif
                            <span class="rounded-full bg-slate-100 px-3 py-1">{{ $job->applications_count }} кандидатури</span>
                        </div>
                        <a href="{{ route('freelancer.jobs.show', $job) }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white shadow-xl shadow-blue-600/20 sm:w-auto">Виж и кандидатствай</a>
                    </article>
                @empty
                    <div class="lg:col-span-2 rounded-[2rem] border border-white/70 bg-white/80 p-10 text-center shadow-xl shadow-blue-900/5">
                        <p class="text-2xl font-black">Няма обяви по тези филтри</p>
                        <p class="mt-3 text-slate-500">Опитай с друга категория или премахни част от филтрите.</p>
                    </div>
                @endforelse
            </section>

            <div class="mt-8">{{ $jobs->links() }}</div>
        </div>
    </main>
</body>
</html>

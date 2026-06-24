<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проекти към фрийлансъри | BON</title>
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
                        <p class="text-xl font-black">BON Projects</p>
                        <p class="text-sm text-slate-500">{{ $business->business_name ?: $business->name }}</p>
                    </div>
                </a>
                <div class="flex flex-wrap gap-2 text-sm font-bold">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Табло</a>
                    <a href="{{ route('business.jobs.create') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 py-2 text-white shadow-xl shadow-blue-600/20">Нова обява</a>
                </div>
            </header>

            @if(session('success'))
                <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Проекти и задачи</p>
                <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-4xl font-black tracking-tight sm:text-5xl">Публикувай проект към BON фрийлансъри.</h1>
                        <p class="mt-4 max-w-3xl text-lg leading-8 text-slate-600">Опиши задачата, бюджета и срока. Фрийлансърите кандидатстват с оферти, а ти избираш най-подходящия специалист.</p>
                    </div>
                    <a href="{{ route('business.jobs.create') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">Създай проект</a>
                </div>
            </section>

            <section class="mt-8 grid gap-5">
                @forelse($jobs as $job)
                    <article class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.2em] text-violet-600">{{ $job->category ?: 'Обява' }}</p>
                                <h2 class="mt-2 text-2xl font-black">{{ $job->title }}</h2>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $job->status === 'open' ? 'Отворена' : 'Затворена' }}
                                    @if($job->deadline) · срок {{ $job->deadline->format('d.m.Y') }} @endif
                                    @if($job->location) · {{ $job->location }} @endif
                                </p>
                            </div>
                            <div class="grid gap-2 text-right">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $job->applications_count }} кандидатури</span>
                                @if($job->budget)<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ number_format((float) $job->budget, 2, ',', ' ') }} €</span>@endif
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ $job->description }}</p>

                        <div class="mt-5 grid gap-3">
                            @forelse($job->applications()->with('freelancer')->latest()->take(5)->get() as $application)
                                @php
                                    $candidateTrust = $application->freelancer?->trustSummary();
                                    $applicationStatusLabels = [
                                        'submitted' => 'Нова кандидатура',
                                        'accepted' => 'Избран кандидат',
                                        'not_selected' => 'Не е избран',
                                        'completed' => 'Завършен проект',
                                        'done' => 'Завършен проект',
                                    ];
                                @endphp
                                <div class="rounded-3xl border border-slate-100 bg-white p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            @if($application->freelancer)
                                                <a href="{{ route('freelancers.show', $application->freelancer) }}" class="font-black text-blue-700 hover:text-violet-700">{{ $application->freelancer->name }}</a>
                                            @else
                                                <span class="font-black text-slate-700">Фрийлансър профилът не е наличен</span>
                                            @endif
                                            <p class="mt-1 text-sm text-slate-500">{{ $application->freelancer?->email }} · {{ $application->created_at?->format('d.m.Y H:i') }} · -{{ $application->credits_spent }} кредита</p>
                                        </div>
                                        <span class="rounded-full {{ $application->status === 'accepted' ? 'bg-emerald-50 text-emerald-700' : ($application->status === 'not_selected' ? 'bg-slate-100 text-slate-500' : 'bg-blue-50 text-blue-700') }} px-3 py-1 text-xs font-black">
                                            {{ $applicationStatusLabels[$application->status] ?? $application->status }}
                                        </span>
                                    </div>
                                    <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                                        <p class="rounded-2xl bg-slate-50 px-3 py-2">Цена: <strong class="text-slate-900">{{ $application->proposed_price ?: 'Не е посочена' }}</strong></p>
                                        <p class="rounded-2xl bg-slate-50 px-3 py-2">Срок: <strong class="text-slate-900">{{ $application->proposed_timeframe ?: 'Не е посочен' }}</strong></p>
                                        <p class="rounded-2xl bg-slate-50 px-3 py-2">Телефон: <strong class="text-slate-900">{{ $application->contact_phone ?: $application->freelancer?->phone ?: 'Не е посочен' }}</strong></p>
                                        <p class="rounded-2xl bg-slate-50 px-3 py-2">Имейл: <strong class="text-slate-900">{{ $application->contact_email ?: $application->freelancer?->email ?: 'Не е посочен' }}</strong></p>
                                    </div>
                                    @if($application->portfolio_url)
                                        <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener" class="mt-3 inline-flex min-h-10 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black text-blue-700">
                                            Виж портфолио
                                        </a>
                                    @endif
                                    @if($candidateTrust)
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">Trust {{ $candidateTrust['trust_score'] }}/100</span>
                                            <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-black text-violet-700">{{ $candidateTrust['completed_projects_count'] }} проекта</span>
                                            @foreach(array_slice($candidateTrust['badges'], 0, 2) as $badge)
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ $badge }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($application->cover_message)
                                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $application->cover_message }}</p>
                                    @endif
                                    @if($application->status === 'submitted' && $job->status === 'open')
                                        <form action="{{ route('business.jobs.applications.select', $application) }}" method="POST" class="mt-4">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="min-h-11 w-full rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 text-sm font-black text-white shadow-lg shadow-blue-600/20 sm:w-auto">
                                                Избери кандидат
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="rounded-3xl border border-slate-100 bg-white p-4 text-sm text-slate-500">Все още няма кандидатури.</p>
                            @endforelse
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-10 text-center shadow-xl shadow-blue-900/5">
                        <p class="text-2xl font-black">Все още няма публикувани обяви</p>
                        <p class="mt-3 text-slate-500">Създай първата задача, по която фрийлансъри могат да кандидатстват.</p>
                        <a href="{{ route('business.jobs.create') }}" class="mt-6 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-6 text-sm font-black text-white">Нова обява</a>
                    </div>
                @endforelse
            </section>

            <div class="mt-8">{{ $jobs->links() }}</div>
        </div>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $freelancerJob->title }} | BON Projects</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
    @include('partials.public-header')

    <main class="relative overflow-x-clip">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_340px] lg:items-start">
                    <div>
                        <a href="{{ route('freelancer.projects.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-4 text-xs font-black text-slate-600 hover:text-blue-700">
                            ← Всички проекти
                        </a>
                        <p class="mt-6 text-sm font-black uppercase tracking-[0.22em] text-violet-600">{{ $freelancerJob->category ?: 'Проект' }}</p>
                        <h1 class="mt-3 text-[32px] font-black leading-tight tracking-tight sm:text-5xl">{{ $freelancerJob->title }}</h1>
                        <p class="mt-3 text-slate-500">
                            {{ $freelancerJob->business?->business_name ?: $freelancerJob->business?->name ?: 'Клиент в BON' }}
                            @if($freelancerJob->location) · {{ $freelancerJob->location }} @endif
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-3xl bg-blue-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-700">Бюджет</p>
                                <p class="mt-2 font-black">{{ $freelancerJob->budget ? number_format((float) $freelancerJob->budget, 2, ',', ' ') . ' €' : 'По договаряне' }}</p>
                            </div>
                            <div class="rounded-3xl bg-violet-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-700">Срок</p>
                                <p class="mt-2 font-black">{{ $freelancerJob->deadline ? $freelancerJob->deadline->format('d.m.Y') : 'Гъвкав' }}</p>
                            </div>
                            <div class="rounded-3xl bg-fuchsia-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-fuchsia-700">Работа</p>
                                <p class="mt-2 font-black">{{ ['online' => 'Онлайн', 'onsite' => 'На място', 'hybrid' => 'Хибридно'][$freelancerJob->work_mode] ?? 'Не е посочено' }}</p>
                            </div>
                            <div class="rounded-3xl bg-cyan-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">Оферти</p>
                                <p class="mt-2 font-black">{{ $freelancerJob->applications_count }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h2 class="text-xl font-black">Описание на задачата</h2>
                            <p class="mt-4 whitespace-pre-line text-base leading-8 text-slate-700">{{ $freelancerJob->description }}</p>
                        </div>

                        @if($freelancerJob->attachment_path)
                            <a href="{{ asset('storage/' . $freelancerJob->attachment_path) }}" target="_blank" rel="noopener" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 px-5 text-sm font-black text-blue-700">
                                Виж прикачения файл
                            </a>
                        @endif
                    </div>

                    <aside class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-xl shadow-fuchsia-900/5">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Следваща стъпка</p>
                        <h2 class="mt-3 text-2xl font-black">Искаш да кандидатстваш?</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Фрийлансърите изпращат оферта с кратко съобщение, цена, срок и портфолио. Кандидатстването струва {{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита.
                        </p>

                        <div class="mt-6 grid gap-3">
                            @auth
                                @if(auth()->user()->isFreelancer())
                                    <a href="{{ route('freelancer.jobs.show', $freelancerJob) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                                        Кандидатствай по проекта
                                    </a>
                                @else
                                    <a href="{{ route('freelancer.projects.create') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                                        Публикувай подобен проект
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register', ['role' => 'freelancer']) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                                    Стани фрийлансър
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-black text-slate-700">
                                    Вход
                                </a>
                            @endauth
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    </main>

    @include('partials.public-footer')
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} | BON Freelancer</title>
    <meta name="description" content="Публичен BON профил на фрийлансър с Trust Score, значки, завършени проекти и репутация.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
    @include('partials.public-header')

    <main class="relative overflow-hidden">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.20]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-start">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($trustSummary['badges'] as $badge)
                                <span class="rounded-full border border-white/70 bg-white/80 px-3 py-1 text-xs font-black text-blue-700 shadow-sm">{{ $badge }}</span>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            @include('partials.favorite-button', ['profile' => $user, 'variant' => 'light'])
                        </div>

                        <div class="mt-8 flex flex-col gap-5 sm:flex-row sm:items-center">
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-[2rem] bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-4xl font-black text-white shadow-xl shadow-violet-500/25">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">BON Freelancer</p>
                                <h1 class="mt-2 text-4xl font-black tracking-tight sm:text-5xl">{{ $user->name }}</h1>
                                <p class="mt-3 text-lg leading-8 text-slate-600">Профил на независим специалист в BON Talent Network.</p>
                            </div>
                        </div>

                        <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-3xl bg-blue-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-700">Trust Score</p>
                                <p class="mt-2 text-3xl font-black">{{ $trustSummary['trust_score'] }}/100</p>
                            </div>
                            <div class="rounded-3xl bg-violet-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-700">Завършени</p>
                                <p class="mt-2 text-3xl font-black">{{ $trustSummary['completed_projects_count'] }}</p>
                            </div>
                            <div class="rounded-3xl bg-fuchsia-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-fuchsia-700">Успех</p>
                                <p class="mt-2 text-3xl font-black">{{ $trustSummary['success_rate'] }}%</p>
                            </div>
                            <div class="rounded-3xl bg-cyan-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">В BON от</p>
                                <p class="mt-2 text-3xl font-black">{{ $trustSummary['registered_year'] ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <aside class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-xl shadow-blue-900/5">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Доверие</p>
                        <div class="mt-5 grid gap-3">
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Имейл</span>
                                <span class="font-black {{ $trustSummary['email_verified'] ? 'text-emerald-600' : 'text-slate-400' }}">{{ $trustSummary['email_verified'] ? 'Потвърден' : 'Непотвърден' }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Телефон</span>
                                <span class="font-black {{ $trustSummary['phone_verified'] ? 'text-emerald-600' : 'text-slate-400' }}">{{ $trustSummary['phone_verified'] ? 'Потвърден' : 'Непотвърден' }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Профил</span>
                                <span class="font-black text-blue-600">{{ $trustSummary['profile_completeness'] }}%</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Отговор</span>
                                <span class="font-black text-slate-700">{{ $trustSummary['response_label'] ? 'Средно ' . $trustSummary['response_label'] : 'Няма данни' }}</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:p-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Портфолио</p>
                        <h2 class="mt-3 text-3xl font-black">Избрани проекти</h2>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $portfolioItems->count() }} проекта</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($portfolioItems as $item)
                        <article class="overflow-hidden rounded-3xl border border-slate-100 bg-white/85 shadow-lg shadow-blue-900/5">
                            @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" loading="lazy" class="h-44 w-full object-cover">
                            @else
                                <div class="grid h-44 place-items-center bg-gradient-to-br from-blue-50 via-violet-50 to-pink-50">
                                    <span class="flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white">B</span>
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="text-lg font-black">{{ $item->title }}</h3>
                                @if($item->description)
                                    <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $item->description }}</p>
                                @endif
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if($item->project_url)
                                        <a href="{{ $item->project_url }}" target="_blank" rel="noopener" class="rounded-2xl bg-blue-50 px-4 py-2 text-xs font-black text-blue-700">Линк</a>
                                    @endif
                                    @if($item->pdf_path)
                                        <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" rel="noopener" class="rounded-2xl bg-violet-50 px-4 py-2 text-xs font-black text-violet-700">PDF</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-200 bg-white/70 p-8 text-center text-sm text-slate-500 md:col-span-2 lg:col-span-3">
                            Този фрийлансър все още не е добавил публично портфолио.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Репутация</p>
                    <h2 class="mt-3 text-3xl font-black">Защо клиентите избират този специалист</h2>
                    <div class="mt-6 grid gap-3">
                        @foreach($trustSummary['reasons'] as $reason)
                            <div class="rounded-3xl border border-slate-100 bg-white/80 p-4 text-sm leading-6 text-slate-600">
                                {{ $reason }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Активност</p>
                    <h2 class="mt-3 text-3xl font-black">Публична история</h2>
                    <div class="mt-6 grid gap-3">
                        @forelse($applications as $application)
                            <div class="rounded-3xl border border-slate-100 bg-white/80 p-4">
                                <p class="font-black">{{ $application->job?->title ?: 'Обява' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $application->created_at?->format('d.m.Y') }} · статус {{ $application->status }}</p>
                            </div>
                        @empty
                            <p class="rounded-3xl border border-slate-100 bg-white/80 p-5 text-sm text-slate-500">Този профил все още няма публична история от завършени или потвърдени проекти.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('partials.public-footer')
</body>
</html>

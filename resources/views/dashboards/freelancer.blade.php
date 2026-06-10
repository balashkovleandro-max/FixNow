<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фрийлансър табло | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
    <main class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.25]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">BON</p>
                        <p class="text-sm text-slate-500">Freelancer Network</p>
                    </div>
                </a>
                <nav class="flex flex-wrap gap-2 text-sm font-bold">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl bg-blue-50 px-4 py-2 text-blue-700">Обзор</a>
                    <a href="{{ route('freelancer.credits.index') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Моите кредити</a>
                    <a href="{{ route('freelancer.jobs.index') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Обяви</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Изход</button>
                    </form>
                </nav>
            </header>

            @if(session('success'))
                <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            @php
                $profile = $profile ?? \App\Support\ProfileCompletion::summary(auth()->user());
                $portfolioItems = auth()->user()->relationLoaded('freelancerPortfolioItems') ? auth()->user()->freelancerPortfolioItems : collect();
            @endphp

            <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Профилна завършеност</p>
                        <h2 class="mt-3 text-3xl font-black">Профилът ви е завършен на {{ $profile['percent'] }}%</h2>
                        <div class="mt-5 h-3 rounded-full bg-slate-200">
                            <div class="h-3 rounded-full bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500" style="width: {{ $profile['percent'] }}%"></div>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-slate-600">
                            Остава да добавите:
                            <strong>{{ empty($profile['missing']) ? 'нищо — профилът изглежда готов.' : implode(', ', $profile['missing']) }}</strong>
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($profile['items'] as $item)
                            <div class="rounded-3xl border {{ $item['complete'] ? 'border-emerald-100 bg-emerald-50' : 'border-slate-100 bg-white' }} p-4">
                                <div class="flex items-start gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl {{ $item['complete'] ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $item['complete'] ? '✓' : '•' }}</span>
                                    <div>
                                        <p class="font-black">{{ $item['label'] }}</p>
                                        <p class="mt-1 text-xs font-bold text-slate-500">{{ $item['weight'] }}% от профила</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-stretch">
                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Моите кредити</p>
                    <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-5xl">
                        {{ $creditStats['available'] }} кредита
                    </h1>
                    <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-600">
                        Всеки месец получаваш 30 кредита. Кандидатстването по обява струва 3 кредита и се записва в историята ти.
                    </p>
                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('freelancer.jobs.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5">Виж обяви</a>
                        <a href="{{ route('freelancer.credits.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-700">Купи кредити</a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                        <p class="text-sm font-bold text-slate-500">Използвани кредити</p>
                        <p class="mt-2 text-3xl font-black">{{ $creditStats['used'] }}</p>
                    </div>
                    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                        <p class="text-sm font-bold text-slate-500">Закупени кредити</p>
                        <p class="mt-2 text-3xl font-black">{{ $creditStats['purchased'] }}</p>
                    </div>
                    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                        <p class="text-sm font-bold text-slate-500">Цена на кандидатстване</p>
                        <p class="mt-2 text-3xl font-black">3</p>
                    </div>
                </div>
            </section>

            <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Портфолио</p>
                        <h2 class="mt-3 text-3xl font-black">Добави проекти към публичния си профил</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Портфолиото помага на клиентите да оценят стил, опит и качество преди да се свържат с теб.</p>

                        <form action="{{ route('freelancer.portfolio.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
                            @csrf
                            <label class="grid gap-2 text-sm font-bold text-slate-700">Заглавие на проект
                                <input name="title" required maxlength="160" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                            </label>
                            <label class="grid gap-2 text-sm font-bold text-slate-700">Описание
                                <textarea name="description" rows="3" maxlength="2000" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></textarea>
                            </label>
                            <label class="grid gap-2 text-sm font-bold text-slate-700">Линк към проект
                                <input name="project_url" type="url" placeholder="https://..." class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                            </label>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="grid gap-2 text-sm font-bold text-slate-700">Снимка
                                    <input name="image" type="file" accept="image/*" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm">
                                </label>
                                <label class="grid gap-2 text-sm font-bold text-slate-700">PDF
                                    <input name="pdf" type="file" accept="application/pdf" class="rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm">
                                </label>
                            </div>
                            <button class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">Добави проект</button>
                        </form>
                    </div>

                    <div class="grid gap-3">
                        @forelse($portfolioItems as $item)
                            <article class="rounded-3xl border border-slate-100 bg-white/85 p-4 shadow-sm">
                                <div class="flex gap-4">
                                    @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="h-20 w-24 rounded-2xl object-cover">
                                    @else
                                        <div class="grid h-20 w-24 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-blue-50 to-fuchsia-50 text-xl font-black text-blue-700">B</div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="font-black">{{ $item->title }}</p>
                                        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $item->description ?: 'Без описание' }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @if($item->project_url)
                                                <a href="{{ $item->project_url }}" target="_blank" rel="noopener" class="rounded-2xl bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">Линк</a>
                                            @endif
                                            @if($item->pdf_path)
                                                <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" rel="noopener" class="rounded-2xl bg-violet-50 px-3 py-1 text-xs font-black text-violet-700">PDF</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('freelancer.portfolio.destroy', $item) }}" method="POST" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs font-black text-rose-500 hover:text-rose-700">Премахни</button>
                                </form>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-200 bg-white/70 p-6 text-center text-sm text-slate-500">
                                Все още няма добавени проекти.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Нови възможности</p>
                            <h2 class="mt-2 text-2xl font-black">Обяви за кандидатстване</h2>
                        </div>
                        <a href="{{ route('freelancer.jobs.index') }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-center text-sm font-black text-white">Всички обяви</a>
                    </div>

                    <div class="mt-6 grid gap-4">
                        @forelse($openJobs as $job)
                            <article class="rounded-3xl border border-slate-100 bg-white/80 p-5 shadow-sm">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h3 class="text-lg font-black">{{ $job->title }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $job->business?->business_name ?: $job->business?->name }} · {{ $job->category ?: 'Без категория' }}</p>
                                    </div>
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">3 кредита</span>
                                </div>
                                <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $job->description }}</p>
                                <a href="{{ route('freelancer.jobs.show', $job) }}" class="mt-4 inline-flex rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 py-3 text-sm font-black text-white">Детайли</a>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-slate-100 bg-white/80 p-8 text-center">
                                <p class="font-black">Все още няма активни обяви</p>
                                <p class="mt-2 text-sm text-slate-500">Когато бизнес публикува проект, той ще се появи тук.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-fuchsia-900/10 backdrop-blur-2xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">История</p>
                    <h2 class="mt-2 text-2xl font-black">Последни кандидатури</h2>
                    <div class="mt-6 grid gap-3">
                        @forelse($recentApplications as $application)
                            <div class="rounded-3xl border border-slate-100 bg-white/80 p-4">
                                <p class="font-black">{{ $application->job?->title ?: 'Обява' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $application->created_at?->format('d.m.Y H:i') }} · -{{ $application->credits_spent }} кредита</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">
                                        {{ $application->status === 'accepted' ? 'Избран' : ($application->status === 'not_selected' ? 'Неизбран' : 'Изпратена') }}
                                    </span>
                                    @if($application->proposed_price)
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">Цена: {{ $application->proposed_price }}</span>
                                    @endif
                                    @if($application->proposed_timeframe)
                                        <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-black text-violet-700">Срок: {{ $application->proposed_timeframe }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="rounded-3xl border border-slate-100 bg-white/80 p-5 text-sm text-slate-500">Все още няма кандидатствания.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>

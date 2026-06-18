<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $freelancerJob->title }} | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bon-dark-page min-h-screen overflow-x-hidden text-white antialiased">
    <main class="relative min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-6xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[1.5rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:rounded-[2rem] sm:p-5">
                <a href="{{ route('freelancer.jobs.index') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">Детайл на проект</p>
                        <p class="text-sm text-slate-500">Баланс: {{ $creditStats['available'] }} кредита</p>
                    </div>
                </a>
                <a href="{{ route('freelancer.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-5 text-sm font-black text-slate-700">Назад</a>
            </header>

            @if(session('success'))
                <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-7 grid gap-6 lg:grid-cols-[1fr_390px]">
                <article class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">{{ $freelancerJob->category ?: 'Проект' }}</p>
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
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">Кандидатстване</p>
                            <p class="mt-2 font-black">{{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита</p>
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
                </article>

                <aside class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-fuchsia-900/10 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Оферта</p>
                    <h2 class="mt-2 text-2xl font-black">Баланс: {{ $creditStats['available'] }} кредита</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        При изпращане ще бъдат приспаднати {{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита.
                    </p>

                    @if($hasApplied)
                        <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-700">
                            <p class="font-black">Вече си кандидатствал</p>
                            <p class="mt-2 text-sm">Кандидатурата е записана в историята ти.</p>
                        </div>
                    @else
                        <form action="{{ route('freelancer.jobs.apply', $freelancerJob) }}" method="POST" class="mt-6 grid gap-4">
                            @csrf
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="grid gap-2 text-sm font-black text-slate-700">
                                    Предложена цена
                                    <input name="proposed_price" value="{{ old('proposed_price') }}" maxlength="120" placeholder="450 € или по договаряне" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                </label>
                                <label class="grid gap-2 text-sm font-black text-slate-700">
                                    Срок
                                    <input name="proposed_timeframe" value="{{ old('proposed_timeframe') }}" maxlength="120" placeholder="7 дни" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                </label>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="grid gap-2 text-sm font-black text-slate-700">
                                    Телефон
                                    <input name="contact_phone" value="{{ old('contact_phone', auth()->user()?->phone) }}" maxlength="80" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                </label>
                                <label class="grid gap-2 text-sm font-black text-slate-700">
                                    Имейл
                                    <input name="contact_email" type="email" value="{{ old('contact_email', auth()->user()?->email) }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                </label>
                            </div>
                            <label class="grid gap-2 text-sm font-black text-slate-700">
                                Линк към портфолио
                                <input name="portfolio_url" type="url" value="{{ old('portfolio_url', auth()->user()?->website) }}" placeholder="https://..." class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                            </label>
                            <textarea name="cover_message" rows="6" placeholder="Кратко представяне, релевантен опит и как би помогнал по задачата..." class="rounded-3xl border border-slate-200 bg-white/80 px-4 py-4 text-sm leading-6 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">{{ old('cover_message') }}</textarea>
                            <button type="submit" class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                                Кандидатствай · {{ \App\Support\FreelancerCredits::APPLICATION_COST }} кредита
                            </button>
                            <button type="button" data-open-credit-modal class="min-h-12 rounded-2xl border border-slate-200 bg-white/80 px-6 text-sm font-black text-slate-700">
                                Купи кредити
                            </button>
                        </form>
                    @endif
                </aside>
            </section>
        </div>
    </main>

    <div id="credit-modal" class="{{ session('show_credit_modal') ? '' : 'hidden' }} fixed inset-0 z-50 px-4 py-6 sm:px-6" role="dialog" aria-modal="true">
        <button type="button" data-close-credit-modal class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm"></button>
        <div class="relative mx-auto flex min-h-full max-w-3xl items-center">
            <div class="max-h-[88vh] w-full overflow-y-auto rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-2xl shadow-blue-950/20 backdrop-blur-2xl sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-3xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                        <h2 class="mt-5 text-3xl font-black">Недостатъчно кредити</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">За кандидатстване са нужни 3 кредита. Избери пакет и продължи с кандидатурата.</p>
                    </div>
                    <button type="button" data-close-credit-modal class="rounded-2xl border border-slate-200 bg-white px-4 py-2 font-black text-slate-500">×</button>
                </div>

                <div class="mt-7 grid gap-4 md:grid-cols-3">
                    @foreach($packages as $key => $package)
                        <form action="{{ route('freelancer.credits.purchase') }}" method="POST" class="rounded-3xl border border-slate-100 bg-white p-5 shadow-xl shadow-blue-900/5">
                            @csrf
                            <input type="hidden" name="package" value="{{ $key }}">
                            <p class="text-2xl font-black">{{ $package['label'] }}</p>
                            <p class="mt-2 text-lg font-black text-blue-600">{{ number_format($package['price'], 2, '.', '') }} €</p>
                            <button type="submit" class="mt-5 min-h-12 w-full rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 text-sm font-black text-white">Купи</button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        const creditModal = document.getElementById('credit-modal');
        document.querySelectorAll('[data-open-credit-modal]').forEach((button) => {
            button.addEventListener('click', () => creditModal?.classList.remove('hidden'));
        });
        document.querySelectorAll('[data-close-credit-modal]').forEach((button) => {
            button.addEventListener('click', () => creditModal?.classList.add('hidden'));
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моите кредити | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bon-dark-page min-h-screen overflow-x-hidden text-white antialiased">
    <main class="relative min-h-screen overflow-x-clip">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">Моите кредити</p>
                        <p class="text-sm text-slate-500">BON Freelancer Network</p>
                    </div>
                </a>
                <nav class="flex flex-wrap gap-2 text-sm font-bold">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Обзор</a>
                    <a href="{{ route('freelancer.jobs.index') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Обяви</a>
                </nav>
            </header>

            @if(session('success'))
                <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            <section class="mt-8 grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Баланс</p>
                    <h1 class="mt-3 text-5xl font-black">{{ $creditStats['available'] }}</h1>
                    <p class="mt-2 text-slate-500">налични кредита</p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-3xl bg-blue-50 p-4">
                            <p class="text-sm font-bold text-blue-700">Използвани</p>
                            <p class="mt-1 text-2xl font-black">{{ $creditStats['used'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-fuchsia-50 p-4">
                            <p class="text-sm font-bold text-fuchsia-700">Закупени</p>
                            <p class="mt-1 text-2xl font-black">{{ $creditStats['purchased'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-fuchsia-900/10 backdrop-blur-2xl sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">Купи кредити</p>
                    <h2 class="mt-2 text-3xl font-black">Пакети за кандидатстване</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Всеки пакет добавя кредити към баланса ти. Едно кандидатстване струва 3 кредита.</p>
                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        @foreach($packages as $key => $package)
                            <form action="{{ route('freelancer.credits.purchase') }}" method="POST" class="rounded-3xl border border-slate-100 bg-white/80 p-5 shadow-xl shadow-blue-900/5">
                                @csrf
                                <input type="hidden" name="package" value="{{ $key }}">
                                <p class="text-2xl font-black">{{ $package['label'] }}</p>
                                <p class="mt-2 text-lg font-black text-blue-600">{{ number_format($package['price'], 2, '.', '') }} €</p>
                                <p class="mt-3 text-sm text-slate-500">Около {{ floor($package['credits'] / \App\Support\FreelancerCredits::APPLICATION_COST) }} кандидатствания.</p>
                                <button type="submit" class="mt-5 min-h-12 w-full rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-xl shadow-violet-500/25">Купи кредити</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                    <h2 class="text-2xl font-black">История на кредитите</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full min-w-[720px] border-separate border-spacing-y-2 text-left text-sm">
                            <thead class="text-xs uppercase tracking-[0.18em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-2">Дата</th>
                                    <th class="px-4 py-2">Тип</th>
                                    <th class="px-4 py-2">Кредити</th>
                                    <th class="px-4 py-2">Баланс</th>
                                    <th class="px-4 py-2">Описание</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="rounded-l-2xl bg-white px-4 py-3">{{ $transaction->created_at?->format('d.m.Y H:i') }}</td>
                                        <td class="bg-white px-4 py-3">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                        <td class="bg-white px-4 py-3 font-black {{ $transaction->amount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }}</td>
                                        <td class="bg-white px-4 py-3 font-black">{{ $transaction->balance_after }}</td>
                                        <td class="rounded-r-2xl bg-white px-4 py-3 text-slate-500">{{ $transaction->description }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="rounded-2xl bg-white px-4 py-8 text-center text-slate-500">Няма кредитна история.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $transactions->links() }}</div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                    <h2 class="text-2xl font-black">История на кандидатстванията</h2>
                    <div class="mt-5 grid gap-3">
                        @forelse($applications as $application)
                            <div class="rounded-3xl border border-slate-100 bg-white p-4">
                                <p class="font-black">{{ $application->job?->title ?: 'Обява' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $application->job?->business?->business_name ?: $application->job?->business?->name }} · {{ $application->created_at?->format('d.m.Y H:i') }}</p>
                                <p class="mt-2 text-sm font-bold text-rose-600">-{{ $application->credits_spent }} кредита</p>
                            </div>
                        @empty
                            <p class="rounded-3xl border border-slate-100 bg-white p-5 text-sm text-slate-500">Все още няма кандидатствания.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer кредити | BON Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-12 text-white">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(37,99,235,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(217,70,239,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <main class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-8">
        <header class="rounded-[28px] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 font-black">B</div>
                    <div>
                        <p class="text-xl font-black">BON Admin</p>
                        <p class="text-xs text-white/50">Freelancer credits</p>
                    </div>
                </a>
                <nav class="flex flex-wrap gap-2 text-sm font-bold text-white/70">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Admin</a>
                    <a href="{{ route('admin.service-requests.index') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Заявки</a>
                </nav>
            </div>
        </header>

        @if(session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-rose-200">{{ $errors->first() }}</div>
        @endif

        <section class="mt-6 rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-blue-200/80">Credit control</p>
            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black sm:text-5xl">Управление на freelancer кредити</h1>
                    <p class="mt-3 max-w-3xl text-white/60">Добавяне/отнемане на кредити, история на транзакции, закупени пакети и кандидатури по обяви.</p>
                </div>
                <div class="rounded-3xl border border-blue-300/20 bg-blue-300/10 px-5 py-4">
                    <p class="text-sm text-blue-100">Фрийлансъри</p>
                    <p class="mt-1 text-3xl font-black">{{ $freelancers->count() }}</p>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <h2 class="text-2xl font-black">Фрийлансъри</h2>
                <div class="mt-5 grid gap-4">
                    @forelse($freelancers as $freelancer)
                        <article class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-lg font-black">{{ $freelancer->name }}</p>
                                    <p class="mt-1 text-sm text-white/50">{{ $freelancer->email }}</p>
                                    <p class="mt-2 text-sm text-blue-100">{{ $freelancer->freelancerCreditsBalance() }} кредита · {{ $freelancer->freelancer_job_applications_count }} кандидатури</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.freelancer-credits.adjust', $freelancer) }}" method="POST" class="mt-4 grid gap-3 sm:grid-cols-[130px_1fr_auto]">
                                @csrf
                                <input name="amount" type="number" required placeholder="+/-" class="min-h-11 rounded-2xl border border-white/10 bg-white/10 px-4 text-white outline-none placeholder:text-white/35">
                                <input name="description" placeholder="Описание" class="min-h-11 rounded-2xl border border-white/10 bg-white/10 px-4 text-white outline-none placeholder:text-white/35">
                                <button class="rounded-2xl bg-gradient-to-r from-blue-500 to-violet-500 px-5 py-3 text-sm font-black text-white">Запази</button>
                            </form>
                        </article>
                    @empty
                        <p class="rounded-3xl border border-white/10 bg-slate-950/45 p-6 text-white/60">Все още няма freelancer профили.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <h2 class="text-2xl font-black">Последни транзакции</h2>
                <div class="mt-5 overflow-x-auto">
                    <table class="w-full min-w-[780px] border-separate border-spacing-y-2 text-left text-sm">
                        <thead class="text-xs uppercase tracking-[0.18em] text-white/40">
                            <tr>
                                <th class="px-4 py-2">Дата</th>
                                <th class="px-4 py-2">Фрийлансър</th>
                                <th class="px-4 py-2">Тип</th>
                                <th class="px-4 py-2">Кредити</th>
                                <th class="px-4 py-2">Баланс</th>
                                <th class="px-4 py-2">Описание</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="rounded-l-2xl bg-slate-950/55 px-4 py-3">{{ $transaction->created_at?->format('d.m.Y H:i') }}</td>
                                    <td class="bg-slate-950/55 px-4 py-3">{{ $transaction->user?->name }}</td>
                                    <td class="bg-slate-950/55 px-4 py-3">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                    <td class="bg-slate-950/55 px-4 py-3 font-black {{ $transaction->amount >= 0 ? 'text-emerald-200' : 'text-rose-200' }}">{{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }}</td>
                                    <td class="bg-slate-950/55 px-4 py-3 font-black">{{ $transaction->balance_after }}</td>
                                    <td class="rounded-r-2xl bg-slate-950/55 px-4 py-3 text-white/55">{{ $transaction->description }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="rounded-2xl bg-slate-950/55 px-4 py-8 text-center text-white/55">Няма транзакции.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-2">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <h2 class="text-2xl font-black">Закупени пакети</h2>
                <div class="mt-5 grid gap-3">
                    @forelse($purchasedPackages as $transaction)
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="font-black">{{ $transaction->user?->name }} · {{ $transaction->credit_package }}</p>
                            <p class="mt-1 text-sm text-white/55">+{{ $transaction->amount }} кредита · {{ $transaction->price_amount }} {{ $transaction->currency }} · {{ $transaction->created_at?->format('d.m.Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 text-white/55">Няма закупени пакети.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <h2 class="text-2xl font-black">Кандидатствания по обяви</h2>
                <div class="mt-5 grid gap-3">
                    @forelse($applications as $application)
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="font-black">{{ $application->job?->title }}</p>
                            <p class="mt-1 text-sm text-white/55">{{ $application->freelancer?->name }} · {{ $application->job?->business?->business_name ?: $application->job?->business?->name }} · -{{ $application->credits_spent }} кредита</p>
                            <p class="mt-1 text-xs text-white/35">{{ $application->created_at?->format('d.m.Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 text-white/55">Няма кандидатствания.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</body>
</html>

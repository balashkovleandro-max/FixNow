@extends('layouts.admin')

@section('title', 'Кредити')
@section('page-title', 'Управление на freelancer кредити')
@section('eyebrow', 'Credits')

@section('content')
    <section class="grid gap-4 xl:grid-cols-[1fr_.9fr]">
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <h2 class="text-xl font-black">Фрийлансъри и баланс</h2>
            <div class="mt-4 grid gap-3">
                @forelse($freelancers as $freelancer)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div class="min-w-0">
                                <p class="truncate font-black">{{ $freelancer->name }}</p>
                                <p class="mt-1 truncate text-sm text-white/50">{{ $freelancer->email }} · {{ $freelancer->phone ?: 'няма телефон' }}</p>
                                <p class="mt-1 text-xs text-white/40">{{ $freelancer->freelancer_job_applications_count ?? 0 }} кандидатури</p>
                            </div>
                            <form action="{{ route('admin.freelancer-credits.adjust', $freelancer) }}" method="POST" class="grid gap-2 sm:grid-cols-[110px_1fr_auto]">
                                @csrf
                                <input name="amount" type="number" placeholder="+/-" class="min-h-10 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-sm font-bold text-white">
                                <input name="description" placeholder="Бележка" class="min-h-10 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-sm font-bold text-white">
                                <button class="min-h-10 rounded-xl bg-white px-4 text-xs font-black text-[#070B1F]">Обнови</button>
                            </form>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-black text-violet-100">{{ $freelancer->freelancerCreditsBalance() }} налични кредита</span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-white/55">Няма фрийлансъри.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <h2 class="text-xl font-black">Последни кредитни транзакции</h2>
            <div class="mt-4 grid gap-3">
                @forelse($transactions as $transaction)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="font-black">{{ $transaction->user?->name ?: 'Фрийлансър' }} · {{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }}</p>
                        <p class="mt-1 text-sm text-white/50">{{ $transaction->type }} · {{ $transaction->created_at?->format('d.m.Y H:i') }}</p>
                        <p class="mt-2 text-sm text-white/60">{{ $transaction->description }}</p>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-white/55">Няма транзакции.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-4 rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <h2 class="text-xl font-black">Кандидатствания по обяви</h2>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @forelse($applications as $application)
                <article class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="font-black">{{ $application->freelancer?->name ?: 'Фрийлансър' }}</p>
                    <p class="mt-1 text-sm text-white/50">{{ $application->job?->title ?: 'Обява' }}</p>
                    <p class="mt-2 text-sm text-white/60">{{ $application->credits_spent }} използвани кредита · {{ $application->status }}</p>
                </article>
            @empty
                <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-white/55">Няма кандидатствания.</p>
            @endforelse
        </div>
    </section>
@endsection

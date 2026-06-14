@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Платформен обзор')
@section('eyebrow', 'Admin dashboard')

@section('content')
    @php
        $cards = [
            ['label' => 'Общо бизнеси', 'value' => $stats['total_businesses'] ?? 0, 'tone' => 'from-blue-500 to-cyan-400'],
            ['label' => 'Активни бизнеси', 'value' => $stats['active_businesses'] ?? 0, 'tone' => 'from-emerald-400 to-cyan-400'],
            ['label' => 'Спрени бизнеси', 'value' => $stats['suspended_businesses'] ?? 0, 'tone' => 'from-rose-500 to-pink-500'],
            ['label' => 'Платени бизнеси', 'value' => $stats['paid_businesses'] ?? 0, 'tone' => 'from-violet-500 to-blue-500'],
            ['label' => 'Неплатени бизнеси', 'value' => $stats['unpaid_businesses'] ?? 0, 'tone' => 'from-amber-400 to-orange-500'],
            ['label' => 'Standard', 'value' => $stats['standard_subscriptions'] ?? 0, 'tone' => 'from-slate-400 to-blue-400'],
            ['label' => 'Premium', 'value' => $stats['premium_subscriptions'] ?? 0, 'tone' => 'from-fuchsia-500 to-pink-500'],
            ['label' => 'Trial', 'value' => $stats['trial_businesses'] ?? 0, 'tone' => 'from-sky-400 to-violet-500'],
            ['label' => 'Изтекли профили', 'value' => $stats['expired_profiles'] ?? 0, 'tone' => 'from-orange-400 to-rose-500'],
            ['label' => 'Нови регистрации', 'value' => $stats['new_registrations'] ?? 0, 'tone' => 'from-blue-500 to-violet-500'],
            ['label' => 'Нови заявки', 'value' => $stats['new_requests'] ?? 0, 'tone' => 'from-cyan-400 to-emerald-400'],
            ['label' => 'Клиенти', 'value' => $stats['clients'] ?? 0, 'tone' => 'from-purple-500 to-fuchsia-500'],
            ['label' => 'Изпратени оферти', 'value' => $stats['sent_offers'] ?? 0, 'tone' => 'from-blue-400 to-indigo-500'],
            ['label' => 'Приети оферти', 'value' => $stats['accepted_offers'] ?? 0, 'tone' => 'from-emerald-400 to-green-500'],
            ['label' => 'Фрийлансъри', 'value' => $stats['freelancers'] ?? 0, 'tone' => 'from-violet-400 to-fuchsia-500'],
        ];
    @endphp

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach($cards as $card)
            <article class="rounded-[1.35rem] border border-white/10 bg-white/[.08] p-4 shadow-xl shadow-black/15 backdrop-blur-xl">
                <div class="mb-4 h-1.5 w-16 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></div>
                <p class="text-sm font-bold text-white/52">{{ $card['label'] }}</p>
                <p class="mt-2 text-3xl font-black tracking-tight">{{ $card['value'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-4 grid gap-4 xl:grid-cols-[1.1fr_.9fr]">
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black">Последни бизнеси</h2>
                    <p class="mt-1 text-sm text-white/50">Профили, планове, телефони и видимост.</p>
                </div>
                <a href="{{ route('admin.businesses.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-white px-4 text-sm font-black text-[#070B1F]">Всички бизнеси</a>
            </div>

            <div class="mt-4 grid gap-3">
                @forelse($recentBusinesses as $business)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <p class="truncate font-black">{{ $business->business_name ?: $business->name }}</p>
                                <p class="mt-1 truncate text-sm text-white/50">{{ $business->email }} · {{ $business->phone ?: 'няма телефон' }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $business->planLabel() }}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $business->effectiveSubscriptionStatus() }}</span>
                                @if($business->is_verified)
                                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/55">Няма бизнеси.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-black">Последни действия</h2>
                    <p class="mt-1 text-sm text-white/50">Audit лог за админ промени.</p>
                </div>
                <a href="{{ route('admin.activity.index') }}" class="text-sm font-black text-blue-200 hover:text-white">Виж лог</a>
            </div>
            <div class="mt-4 grid gap-3">
                @forelse($activityLogs as $log)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-sm font-black">{{ $log->action }}</p>
                        <p class="mt-1 text-xs text-white/45">{{ $log->admin?->name ?: 'Admin' }} · {{ $log->created_at?->format('d.m.Y H:i') }}</p>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/55">Все още няма admin действия.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-4 rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black">Нови заявки и консултации</h2>
                <p class="mt-1 text-sm text-white/50">Последни входящи заявки с контакти, статус и оферти.</p>
            </div>
            <a href="{{ route('admin.requests.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-4 text-sm font-black text-white hover:bg-white/15">Всички заявки</a>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @forelse($recentRequests as $request)
                <article class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="font-black">{{ $request->name ?: 'Клиент' }}</p>
                    <p class="mt-1 text-sm text-white/50">{{ $request->phone ?: 'няма телефон' }} · {{ $request->city ?: 'няма град' }}</p>
                    <p class="mt-3 line-clamp-2 text-sm text-white/65">{{ $request->description }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $request->status }}</span>
                        <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-black text-violet-100">{{ $request->offers_count ?? $request->offers->count() }} оферти</span>
                    </div>
                </article>
            @empty
                <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/55">Няма заявки.</p>
            @endforelse
        </div>
    </section>
@endsection

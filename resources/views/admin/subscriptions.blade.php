@extends('layouts.admin')

@section('title', $mode === 'payments' ? 'Плащания' : 'Абонаменти')
@section('page-title', $mode === 'payments' ? 'Плащания и Stripe данни' : 'Абонаменти')
@section('eyebrow', 'Billing control')

@section('content')
    <section class="grid gap-3">
        @foreach($businesses as $business)
            <article class="rounded-[1.5rem] border border-white/10 bg-white/[.08] p-4 shadow-xl shadow-black/15 backdrop-blur-xl">
                <div class="grid gap-4 lg:grid-cols-[1fr_1fr_auto] lg:items-center">
                    <div class="min-w-0">
                        <p class="truncate text-lg font-black">{{ $business->business_name ?: $business->name }}</p>
                        <p class="mt-1 text-sm text-white/50">{{ $business->email }} · {{ $business->phone ?: 'няма телефон' }}</p>
                        <p class="mt-2 text-xs text-white/40">Stripe customer: {{ $business->stripe_customer_id ?: '—' }}</p>
                        <p class="mt-1 text-xs text-white/40">Stripe subscription: {{ $business->stripe_subscription_id ?: '—' }}</p>
                    </div>
                    <div class="grid gap-2 text-sm text-white/60 sm:grid-cols-2">
                        <p class="rounded-2xl bg-slate-950/35 p-3">План: <strong class="text-white">{{ $business->subscription_plan ?: 'Free' }}</strong></p>
                        <p class="rounded-2xl bg-slate-950/35 p-3">Статус: <strong class="text-white">{{ $business->effectiveSubscriptionStatus() }}</strong></p>
                        <p class="rounded-2xl bg-slate-950/35 p-3">Последно плащане: <strong class="text-white">{{ $business->subscription_started_at?->format('d.m.Y') ?: '—' }}</strong></p>
                        <p class="rounded-2xl bg-slate-950/35 p-3">Следващо/край: <strong class="text-white">{{ $business->subscription_ends_at?->format('d.m.Y') ?: '—' }}</strong></p>
                    </div>
                    <form action="{{ route('admin.businesses.update', $business) }}" method="POST" class="grid gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="action" class="min-h-10 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-xs font-bold text-white">
                            <option value="activate">Активирай</option>
                            <option value="trial">Дай trial</option>
                            <option value="premium">Направи Premium</option>
                            <option value="standard">Направи Standard</option>
                            <option value="expired">Маркирай expired</option>
                            <option value="cancelled">Маркирай cancelled</option>
                            <option value="suspend">Спри профила</option>
                        </select>
                        <button class="min-h-10 rounded-xl bg-white px-3 text-xs font-black text-[#070B1F]">Приложи</button>
                    </form>
                </div>
            </article>
        @endforeach
    </section>

    <div class="mt-4">{{ $businesses->links() }}</div>
@endsection

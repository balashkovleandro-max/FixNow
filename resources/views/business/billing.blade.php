<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing и план | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_16%_12%,rgba(251,146,60,0.16),transparent_30%),radial-gradient(circle_at_84%_12%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#020812,#061426_48%,#020812)]"></div>

    @php
        $statusLabels = [
            'trial' => 'Trial',
            'trialing' => 'Stripe trial',
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'canceled' => 'Canceled',
            'past_due' => 'Payment past due',
            'unpaid' => 'Unpaid',
            'incomplete' => 'Incomplete',
            'incomplete_expired' => 'Incomplete expired',
            'payment_failed' => 'Payment failed',
        ];
        $statusClass = match ($subscriptionStatus) {
            'active', 'trialing' => 'border-emerald-300/30 bg-emerald-400/10 text-emerald-100',
            'trial' => 'border-orange-300/30 bg-orange-400/10 text-orange-100',
            'cancelled', 'canceled' => 'border-amber-300/30 bg-amber-400/10 text-amber-100',
            'past_due', 'unpaid', 'incomplete', 'incomplete_expired', 'payment_failed' => 'border-rose-300/30 bg-rose-400/10 text-rose-100',
            default => 'border-rose-300/30 bg-rose-400/10 text-rose-100',
        };
        $hasPaymentIssue = $business->hasPaymentIssue();
        $hasActiveStripeSubscription = $hasActiveStripeSubscription ?? false;
        $mustManageExistingStripeSubscription = $mustManageExistingStripeSubscription ?? false;
        $canStartCheckout = $canStartCheckout ?? true;
        $canOpenBillingPortal = $canOpenBillingPortal ?? (filled($business->stripe_customer_id) || $business->hasActiveSubscription());
        $currentPlan = $plans[$business->planKey()];
        $limitCards = [
            ['label' => 'Градове', 'used' => $usage['cities'], 'limit' => $usage['city_limit'], 'note' => 'Включени според плана: '.$usage['included_city_limit']],
            ['label' => 'Категории/услуги', 'used' => $usage['categories'], 'limit' => $usage['category_limit'], 'note' => 'Основни услуги и категории'],
            ['label' => 'Снимки', 'used' => $usage['photos'], 'limit' => $usage['photo_limit'], 'note' => 'Отделна бизнес галерия към профила'],
        ];
    @endphp

    <header class="border-b border-white/10 bg-slate-950/55 backdrop-blur-xl">
        <nav class="mx-auto flex max-w-[1440px] items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 text-xl font-black">B</span>
                <span class="text-xl font-black">BON</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('plans') }}" class="hidden min-h-11 items-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/80 hover:bg-white/10 sm:inline-flex">Виж планове</a>
                <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-4 py-2 text-sm font-black text-white shadow-lg shadow-orange-600/20">Към таблото</a>
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-[1440px] px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 rounded-3xl border border-orange-300/20 bg-orange-300/10 p-5 text-orange-50" data-testid="billing-flash">
                {{ session('success') }}
            </div>
        @endif

        @if(request('stripe') === 'success')
            <div class="mb-6 rounded-3xl border border-emerald-300/25 bg-emerald-400/10 p-5 text-emerald-50" data-testid="stripe-return-success">
                <p class="font-black">Stripe Checkout беше завършен.</p>
                <p class="mt-2 text-sm leading-6 text-emerald-50/80">Планът ще бъде активиран само след валиден Stripe webhook. Ако статусът не се обнови веднага, изчакайте няколко секунди и презаредете страницата.</p>
            </div>
        @elseif(request('stripe') === 'cancelled')
            <div class="mb-6 rounded-3xl border border-amber-300/25 bg-amber-400/10 p-5 text-amber-50" data-testid="stripe-return-cancelled">
                <p class="font-black">Плащането не беше завършено. Абонаментът не е активиран.</p>
                <p class="mt-2 text-sm leading-6 text-amber-50/80">Можете да стартирате Stripe Checkout отново, когато сте готови. Текущият план и статус остават непроменени.</p>
            </div>
        @endif

        @if($errors->has('stripe'))
            <div class="mb-6 rounded-3xl border border-rose-300/25 bg-rose-400/10 p-5 text-rose-50" data-testid="billing-stripe-error">
                {{ $errors->first('stripe') }}
            </div>
        @endif

        @if($hasPaymentIssue)
            <div class="mb-6 rounded-3xl border border-amber-300/25 bg-amber-400/10 p-5 text-amber-50" data-testid="billing-payment-warning">
                <p class="font-black">Има проблем с плащането по абонамента.</p>
                <p class="mt-2 text-sm leading-6 text-amber-50/80">Premium предимствата са спрени, докато плащането не бъде възстановено през Stripe Customer Portal.</p>
            </div>
        @endif

        @if($hasActiveStripeSubscription)
            <div class="mb-6 rounded-3xl border border-orange-300/25 bg-orange-300/10 p-5 text-orange-50" data-testid="active-stripe-subscription-notice">
                <p class="font-black">Вече имате активен Stripe абонамент.</p>
                <p class="mt-2 text-sm leading-6 text-orange-50/80">Промени на плана, upgrade или downgrade се управляват през Stripe Customer Portal, за да избегнем дублирани абонаменти.</p>
            </div>
        @elseif($mustManageExistingStripeSubscription)
            <div class="mb-6 rounded-3xl border border-amber-300/25 bg-amber-400/10 p-5 text-amber-50" data-testid="manage-existing-stripe-subscription-notice">
                <p class="font-black">Имате съществуващ Stripe абонамент, който трябва да се управлява през Customer Portal.</p>
                <p class="mt-2 text-sm leading-6 text-amber-50/80">Отворете Customer Portal, за да коригирате плащането или да промените плана без риск от втори абонамент.</p>
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-stretch">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Billing</p>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <span class="rounded-full border border-orange-300/25 bg-orange-400/10 px-4 py-2 text-sm font-black text-orange-100">{{ $business->planLabel() }}</span>
                    <span class="rounded-full border px-4 py-2 text-sm font-black {{ $statusClass }}">{{ $statusLabels[$subscriptionStatus] ?? $subscriptionStatus }}</span>
                    @if($business->is_verified)
                        <span class="rounded-full border border-emerald-300/25 bg-emerald-400/10 px-4 py-2 text-sm font-black text-emerald-100">Потвърден</span>
                    @else
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-black text-white/60">Непотвърден</span>
                    @endif
                </div>
                <h1 class="mt-6 text-3xl font-black leading-tight sm:text-6xl">
                    Управление на плана за {{ $business->business_name ?: $business->name }}
                </h1>
                    <p class="mt-5 max-w-3xl text-base leading-7 text-white/70 sm:text-lg sm:leading-8">
                    Тук виждате текущия план, статус, активирани инструменти и действия за управление на абонамента. Планът се активира само след успешно потвърдено плащане през Stripe или админ действие.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <p class="text-sm text-white/60">Месечна цена</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($business->planMonthlyAmount(), 2, ',', ' ') }} €</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <p class="text-sm text-white/60">Месечна активност</p>
                        <p class="mt-2 text-3xl font-black">{{ $usage['offer_points'] ?? 0 }}</p>
                        <p class="mt-1 text-xs text-white/50">Още {{ $usage['remaining_offers'] ?? 0 }} оферти</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <p class="text-sm text-white/60">Общо прогнозно</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($business->estimatedMonthlyAmount(), 2, ',', ' ') }} €</p>
                    </div>
                </div>

                <div class="mt-6 rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-white/50">Крайна дата</p>
                    <p class="mt-2 text-xl font-black">{{ $endDate ? $endDate->format('d.m.Y') : 'Без крайна дата' }}</p>
                    <p class="mt-2 text-sm text-white/60">Trial и активните абонаменти продължават да използват текущата subscription logic.</p>
                </div>
            </div>

            <aside class="rounded-[32px] border {{ $business->isPremium() ? 'border-orange-300/30 bg-orange-400/10' : 'border-orange-300/20 bg-white/10' }} p-6 shadow-2xl shadow-black/25 backdrop-blur-xl sm:p-8">
                @if($business->isPremium())
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200">Premium active</p>
                    <h2 class="mt-4 text-3xl font-black">Вашият бизнес има Premium предимство</h2>
                    <ul class="mt-6 grid gap-3 text-sm leading-6 text-white/70">
                        @foreach($plans['premium']['features'] as $feature)
                            <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-orange-300"></span><span>{{ $feature }}</span></li>
                        @endforeach
                        <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-orange-300"></span><span>Premium препоръки за растеж</span></li>
                    </ul>
                    @if($canStartCheckout)
                        <form action="{{ route('business.billing.checkout') }}" method="POST" onsubmit="window.trackBonEvent('subscription_checkout_start', { plan: 'standard', source: 'business_billing' })" class="mt-6">
                            @csrf
                            <input type="hidden" name="plan" value="standard">
                             <button type="submit" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-5 py-4 font-black text-white hover:bg-white/20" data-testid="checkout-standard-button">
                                Премини към Standard - 18,99 €/месец
                            </button>
                        </form>
                    @else
                        <div class="mt-6 rounded-3xl border border-orange-300/20 bg-orange-300/10 p-5 text-sm leading-6 text-orange-50" data-testid="checkout-blocked-existing-subscription">
                            Управлявайте промяната на плана през Stripe Customer Portal. Така няма да се създаде втори активен абонамент.
                        </div>
                    @endif
                @else
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200">Upgrade</p>
                    <h2 class="mt-4 text-3xl font-black">Ъпгрейд към Premium</h2>
                    <p class="mt-3 text-sm leading-6 text-white/70">Premium отключва разширен финансов анализ, по-подробен Business Health Score, калкулатори за клиенти и ценообразуване, месечен бизнес доклад, Premium препоръки за растеж и приоритетна поддръжка.</p>
                    @if($canStartCheckout)
                        <form action="{{ route('business.billing.checkout') }}" method="POST" onsubmit="window.trackBonEvent('subscription_checkout_start', { plan: 'premium', source: 'business_billing' })" class="mt-6">
                            @csrf
                            <input type="hidden" name="plan" value="premium">
                             <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-4 font-black text-white shadow-xl shadow-orange-600/25" data-testid="upgrade-premium-button">
                                Вземи Premium - 24,99 €/месец
                            </button>
                        </form>
                        <form action="{{ route('business.billing.checkout') }}" method="POST" onsubmit="window.trackBonEvent('subscription_checkout_start', { plan: 'standard', source: 'business_billing' })" class="mt-3">
                            @csrf
                            <input type="hidden" name="plan" value="standard">
                             <button type="submit" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-5 py-4 font-black text-white hover:bg-white/20" data-testid="checkout-standard-button">
                                Активирай Standard - 18,99 €/месец
                            </button>
                        </form>
                        <p class="mt-3 text-xs leading-5 text-white/50">Планът се активира само след успешно потвърдено плащане през Stripe. Натискането на бутона само стартира процеса по плащане.</p>
                    @else
                        <div class="mt-6 rounded-3xl border border-orange-300/20 bg-orange-300/10 p-5 text-sm leading-6 text-orange-50" data-testid="checkout-blocked-existing-subscription">
                            Вече има Stripe абонамент към този профил. Upgrade/downgrade се прави през Customer Portal, за да не се създава дублиран абонамент.
                        </div>
                    @endif
                @endif

                @if($canOpenBillingPortal)
                    <form action="{{ route('business.billing.portal') }}" method="POST" class="mt-5">
                        @csrf
                        <button type="submit" class="min-h-12 w-full rounded-2xl border border-orange-300/20 bg-orange-300/10 px-5 py-4 font-black text-orange-50 hover:bg-orange-300/15" data-testid="billing-portal-button">
                            Управлявай абонамента
                        </button>
                    </form>
                @endif
            </aside>
        </section>

        <section class="mt-6 grid gap-4 lg:grid-cols-3">
            @foreach($limitCards as $card)
                @php
                    $overLimit = $card['used'] > $card['limit'];
                    $percent = $card['limit'] > 0 ? min(100, (int) round(($card['used'] / $card['limit']) * 100)) : 0;
                @endphp
                <article class="rounded-[28px] border {{ $overLimit ? 'border-rose-300/30 bg-rose-400/10' : 'border-white/10 bg-white/10' }} p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <div class="flex items-center justify-between">
                        <p class="font-black">{{ $card['label'] }}</p>
                        <span class="rounded-full bg-slate-950/55 px-3 py-1 text-xs font-black">{{ $card['used'] }} / {{ $card['limit'] }}</span>
                    </div>
                    <div class="mt-4 h-2 rounded-full bg-white/10">
                        <div class="h-2 rounded-full {{ $overLimit ? 'bg-rose-400' : 'bg-gradient-to-r from-orange-400 to-orange-600' }}" style="width: {{ $percent }}%"></div>
                    </div>
                    <p class="mt-3 text-sm text-white/60">{{ $card['note'] }}</p>
                    @if($overLimit)
                        <p class="mt-3 text-sm font-bold text-rose-100">Над лимита за текущия план.</p>
                    @endif
                </article>
            @endforeach
        </section>

        <section class="mt-6 grid gap-5 lg:grid-cols-2">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 backdrop-blur-xl sm:p-8">
                <h2 class="text-3xl font-black">Текущ план: {{ $currentPlan['label'] }}</h2>
                <ul class="mt-5 grid gap-3 text-sm leading-6 text-white/70">
                    @foreach($currentPlan['features'] as $feature)
                        <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-orange-300"></span><span>{{ $feature }}</span></li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 backdrop-blur-xl sm:p-8">
                <h2 class="text-3xl font-black">Бързи действия</h2>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('plans') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Виж планове</a>
                    <a href="{{ route('business.profile.edit') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Редактирай профил</a>
                    <a href="{{ route('businesses.show', $business) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Виж публичен профил</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-4 text-center font-black text-white">Към dashboard</a>
                </div>
            </div>
        </section>
    </main>
    @include('partials.mobile-bottom-nav')
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панел на бизнес | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    @php
        $business = auth()->user();
        $subscriptionStatus = $business->effectiveSubscriptionStatus();
        $trialDaysRemaining = $business->trialDaysRemaining();
        $profile = \App\Support\ProfileCompletion::summary($business);
        $serviceCount = $business->services->count();
        $photoCount = $business->photoCount();
        $cityCount = $business->serviceCityCount();
        $categoryCount = $business->serviceCategoryCount();
        $statusLabels = [
            'trial' => 'Trial',
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ];
        $statusClasses = [
            'trial' => 'border-orange-300/30 bg-orange-400/10 text-orange-100',
            'active' => 'border-emerald-300/30 bg-emerald-400/10 text-emerald-100',
            'expired' => 'border-rose-300/30 bg-rose-400/10 text-rose-100',
            'cancelled' => 'border-amber-300/30 bg-amber-400/10 text-amber-100',
        ];
        $statusText = $statusLabels[$subscriptionStatus] ?? 'Inactive';
        $statusClass = $statusClasses[$subscriptionStatus] ?? 'border-white/10 bg-white/10 text-white';
        $endDate = $subscriptionStatus === 'trial' ? $business->trial_ends_at : $business->subscription_ends_at;
        $analyticsStats = $analyticsStats ?? [
            'profile_views' => 0,
            'phone_clicks' => 0,
            'website_clicks' => 0,
            'social_clicks' => 0,
            'inquiry_clicks' => 0,
            'chat_clicks' => 0,
            'website_social_clicks' => 0,
            'total_clicks' => 0,
        ];
        $analyticsTotals = array_merge($analyticsStats, $analyticsTotals ?? []);
        $hasAnalyticsData = array_sum($analyticsStats) > 0;
        $businessReviews = $businessReviews ?? collect();
        $assignedServiceRequests = $assignedServiceRequests ?? collect();
        $serviceRequestStats = array_merge([
            'total' => 0,
            'new' => 0,
            'contacted' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ], $serviceRequestStats ?? []);
        $offerStats = array_merge([
            'points_balance' => method_exists($business, 'offerPointsBalance') ? $business->offerPointsBalance() : 0,
            'remaining_offers' => method_exists($business, 'remainingOfferCount') ? $business->remainingOfferCount() : 0,
            'sent_offers' => 0,
            'has_request_based_categories' => method_exists($business, 'hasRequestBasedCategories') ? $business->hasRequestBasedCategories() : false,
        ], $offerStats ?? []);
        $reviewStats = array_merge([
            'approved' => 0,
            'pending' => 0,
            'average' => null,
        ], $reviewStats ?? []);
        $averageReviewRating = $reviewStats['average'] !== null
            ? round((float) $reviewStats['average'], 1)
            : null;
        $limitCards = [
            ['label' => 'Градове', 'value' => $cityCount, 'limit' => $business->cityLimit(), 'note' => 'Обслужвани градове според плана'],
            ['label' => 'Категории', 'value' => $categoryCount, 'limit' => $business->categoryLimit(), 'note' => 'Избрани категории/услуги'],
            ['label' => 'Услуги', 'value' => $serviceCount, 'limit' => $business->categoryLimit(), 'note' => 'Публикувани услуги в профила'],
            ['label' => 'Снимки', 'value' => $photoCount, 'limit' => $business->photoLimit(), 'note' => 'Качени снимки към услуги'],
        ];
        $onboardingItems = collect($profile['items'] ?? []);
        $isProfileComplete = ($profile['percent'] ?? 0) >= 100;
        $approvalLabel = $business->is_verified ? 'Одобрен' : 'Чака одобрение';
        $approvalClass = $business->is_verified
            ? 'border-emerald-300/30 bg-emerald-400/10 text-emerald-100'
            : 'border-amber-300/30 bg-amber-400/10 text-amber-100';
    @endphp

    <div class="mx-auto grid max-w-[1500px] gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[280px_1fr] lg:px-8">
        <header class="rounded-[28px] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/20 backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 font-black">B</div>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-black">Панел на бизнес</p>
                        <p class="truncate text-xs text-white/50">{{ $business->business_name ?: $business->name }}</p>
                    </div>
                </a>
                <a href="{{ route('business.billing') }}" class="inline-flex min-h-11 items-center rounded-2xl bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100">План</a>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <a href="{{ route('business.profile.edit') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-center text-sm font-black text-white">Редакция</a>
                <a href="{{ route('business.insights.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-center text-sm font-black text-white">Финанси</a>
                <a href="{{ route('business.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-center text-sm font-black text-white">Обяви</a>
                <a href="{{ route('businesses.show', $business) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-center text-sm font-black text-white">Профил</a>
            </div>
        </header>

        <aside class="hidden overflow-y-auto rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/20 backdrop-blur-xl lg:sticky lg:top-6 lg:block lg:h-[calc(100vh-48px)]">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 font-black">B</div>
                <div>
                    <p class="text-xl font-black">BON</p>
                    <p class="text-xs text-white/50">Панел на бизнес</p>
                </div>
            </a>

            <nav class="mt-8 grid gap-2 text-sm font-bold">
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-orange-300/10 px-4 py-3 text-orange-100">Обзор</a>
                <a href="{{ route('business.insights.index') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Финансов анализ</a>
                <a href="{{ route('business.jobs.index') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Обяви към фрийлансъри</a>
                <a href="{{ route('business.profile.edit') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Редакция на профил</a>
                <a href="{{ route('services.create') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Добави услуга</a>
                <a href="{{ route('business.service-requests.index') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Заявки</a>
                <a href="{{ route('businesses.show', $business) }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Виж публичния профил</a>
                <a href="{{ route('businesses.index') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Публични бизнеси</a>
                <a href="{{ route('services.index') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Услуги</a>
                <a href="{{ url('/categories') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Категории</a>
                <a href="{{ url('/') }}" class="rounded-2xl px-4 py-3 text-white/70 hover:bg-white/10 hover:text-white">Начало</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="mt-8">
                @csrf
                <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-left text-sm font-bold text-white/70 hover:bg-white/10">Изход</button>
            </form>
        </aside>

        <main class="grid min-w-0 gap-6">
            <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Business control center</p>
                        <h1 class="mt-3 text-3xl font-black sm:text-5xl">
                            Управлявайте <span class="bg-gradient-to-r from-orange-300 via-orange-400 to-orange-500 bg-clip-text text-transparent">{{ $business->business_name ?: $business->name }}</span>
                        </h1>
                        <p class="mt-3 max-w-3xl text-white/60">Попълвайте профила си, следете активността и управлявайте видимостта си във BON без нужда от админ намеса.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('business.profile.edit') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-6 py-4 text-center font-black text-white shadow-lg shadow-orange-600/25">Редактирай профила</a>
                        <a href="{{ route('businesses.show', $business) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-4 text-center font-black text-white hover:bg-white/10">Виж публичния профил</a>
                    </div>
                </div>
            </section>

            <section data-testid="dashboard-billing-card" class="rounded-[32px] border border-orange-300/20 bg-gradient-to-br from-orange-400/10 via-orange-500/10 to-orange-600/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Billing и план</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">
                            {{ $business->planLabel() }} · {{ number_format($business->planMonthlyAmount(), 2, ',', ' ') }} €/месец
                        </h2>
                        <p class="mt-2 text-sm text-white/60">
                            Статус: {{ $statusText }} · Градове {{ $cityCount }}/{{ $business->cityLimit() }} · Категории {{ $categoryCount }}/{{ $business->categoryLimit() }} · Снимки {{ $photoCount }}/{{ $business->photoLimit() }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('plans') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-center text-sm font-black text-white hover:bg-white/10">Виж планове</a>
                        <a href="{{ route('business.billing') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-center text-sm font-black text-white shadow-lg shadow-orange-600/20">Управление на плана</a>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" data-testid="business-offer-points-summary">
                <div class="rounded-[28px] border border-orange-300/20 bg-orange-400/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm font-bold text-white/55">Оставащи точки</p>
                    <p class="mt-2 text-4xl font-black">{{ $offerStats['points_balance'] }}</p>
                    <p class="mt-2 text-sm text-white/60">Още около {{ $offerStats['remaining_offers'] }} оферти към клиентски заявки.</p>
                </div>
                <div class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm font-bold text-white/55">Нови заявки</p>
                    <p class="mt-2 text-4xl font-black">{{ $serviceRequestStats['new'] }}</p>
                    <p class="mt-2 text-sm text-white/60">Директни или назначени заявки към вашия профил.</p>
                </div>
                <div class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm font-bold text-white/55">Изпратени оферти</p>
                    <p class="mt-2 text-4xl font-black">{{ $offerStats['sent_offers'] }}</p>
                    <p class="mt-2 text-sm text-white/60">Оферти, изпратени от вашия профил на бизнес.</p>
                </div>
                <div class="rounded-[28px] border {{ $offerStats['has_request_based_categories'] ? 'border-orange-300/20 bg-orange-400/10' : 'border-white/10 bg-white/10' }} p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm font-bold text-white/55">Режим на профила</p>
                    <p class="mt-2 text-2xl font-black">{{ $offerStats['has_request_based_categories'] ? 'Профил + заявки' : 'Профил' }}</p>
                    <a href="{{ route('business.service-requests.index') }}" class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-white/10 px-4 py-3 text-sm font-black text-white hover:bg-white/15">Управлявай заявки</a>
                </div>
            </section>

            @php
                $freelancerJobStats = array_merge([
                    'published' => 0,
                    'open' => 0,
                    'applications' => 0,
                    'selected' => 0,
                ], $freelancerJobStats ?? []);
                $latestFreelancerJobs = $latestFreelancerJobs ?? collect();
                $recentFreelancerApplications = $recentFreelancerApplications ?? collect();
            @endphp

            <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8" data-testid="business-freelancer-jobs-overview">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">BON Talent Network</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Обяви, кандидатури и избрани специалисти</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-white/60">Публикувайте проект към фрийлансъри, сравнявайте кандидатури и избирайте подходящ специалист от business dashboard-а.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('business.jobs.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-600/20">Публикувай заявка</a>
                        <a href="{{ route('business.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-black text-white hover:bg-white/10">Виж кандидатури</a>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        ['label' => 'Публикувани обяви', 'value' => $freelancerJobStats['published']],
                        ['label' => 'Активни обяви', 'value' => $freelancerJobStats['open']],
                        ['label' => 'Получени кандидатури', 'value' => $freelancerJobStats['applications']],
                        ['label' => 'Избрани специалисти', 'value' => $freelancerJobStats['selected']],
                    ] as $stat)
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm text-white/55">{{ $stat['label'] }}</p>
                            <p class="mt-2 text-3xl font-black">{{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-4 xl:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <h3 class="text-lg font-black">Последни обяви</h3>
                        <div class="mt-4 grid gap-3">
                            @forelse($latestFreelancerJobs as $job)
                                <div class="rounded-2xl bg-white/5 px-4 py-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-black">{{ $job->title }}</p>
                                            <p class="mt-1 text-sm text-white/50">{{ $job->category ?: 'Проект' }} · {{ $job->applications_count }} кандидатури</p>
                                        </div>
                                        <span class="rounded-full {{ $job->status === 'open' ? 'bg-emerald-400/10 text-emerald-100' : 'bg-white/10 text-white/55' }} px-3 py-1 text-xs font-black">{{ $job->status === 'open' ? 'Отворена' : 'Затворена' }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="rounded-2xl bg-white/5 px-4 py-4 text-sm text-white/55">Все още няма публикувани freelancer обяви.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <h3 class="text-lg font-black">Последни кандидатури</h3>
                        <div class="mt-4 grid gap-3">
                            @forelse($recentFreelancerApplications as $application)
                                <div class="rounded-2xl bg-white/5 px-4 py-3">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="font-black">{{ $application->freelancer?->name ?: 'Фрийлансър' }}</p>
                                            <p class="mt-1 text-sm text-white/50">{{ $application->job?->title ?: 'Обява' }}</p>
                                        </div>
                                        <span class="rounded-full {{ $application->status === 'accepted' ? 'bg-emerald-400/10 text-emerald-100' : ($application->status === 'not_selected' ? 'bg-white/10 text-white/55' : 'bg-orange-400/10 text-orange-100') }} px-3 py-1 text-xs font-black">{{ $application->status }}</span>
                                    </div>
                                    <div class="mt-3 grid gap-2 text-sm text-white/60 sm:grid-cols-2">
                                        <p>Цена: <strong class="text-white">{{ $application->proposed_price ?: 'Не е посочена' }}</strong></p>
                                        <p>Срок: <strong class="text-white">{{ $application->proposed_timeframe ?: 'Не е посочен' }}</strong></p>
                                    </div>
                                </div>
                            @empty
                                <p class="rounded-2xl bg-white/5 px-4 py-4 text-sm text-white/55">Все още няма кандидатури от freelancer профили.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            @php
                $recentBusinessDiagnostics = $recentBusinessDiagnostics ?? collect();
            @endphp

            <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8" data-testid="business-diagnostics-overview">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">BON диагностика</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Бизнес анализи и следващи действия</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-white/60">Запазените диагностики помагат да следите повтарящите се проблеми, препоръките и подходящите специалисти.</p>
                    </div>
                    <a href="{{ route('bon.business-problem') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-violet-600/20">Нова диагностика</a>
                </div>

                <div class="mt-6 grid gap-3">
                    @forelse($recentBusinessDiagnostics as $diagnostic)
                        <a href="{{ route('bon.business-problem.result', $diagnostic) }}" class="block rounded-3xl border border-white/10 bg-slate-950/45 p-5 transition hover:-translate-y-0.5 hover:bg-slate-950/60">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black">{{ $diagnostic->problem_type }}</p>
                                    <p class="mt-1 text-sm text-white/50">{{ $diagnostic->business_name ?: $business->business_name ?: $business->name }} · {{ $diagnostic->created_at?->format('d.m.Y H:i') }}</p>
                                </div>
                                <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">Виж резултат</span>
                            </div>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-white/60">{{ $diagnostic->likely_reason }}</p>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-dashed border-white/15 bg-slate-950/35 p-6 text-sm leading-6 text-white/55">
                            Все още няма запазени BON диагностики. Пуснете първи анализ, за да получите структурирани насоки за следваща стъпка.
                        </div>
                    @endforelse
                </div>
            </section>

            <section data-testid="business-onboarding-checklist" class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Onboarding</p>
                            <span class="rounded-full border px-3 py-1 text-xs font-black {{ $approvalClass }}" data-testid="business-approval-status">{{ $approvalLabel }}</span>
                            @if($isProfileComplete)
                                <span class="rounded-full border border-emerald-300/25 bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100" data-testid="profile-complete-status">Профилът е завършен</span>
                            @else
                                <span class="rounded-full border border-amber-300/25 bg-amber-400/10 px-3 py-1 text-xs font-black text-amber-100" data-testid="profile-incomplete-status">Има липсващи данни</span>
                            @endif
                        </div>
                        <h2 class="mt-4 text-2xl font-black sm:text-4xl">Подгответе профила за първите реални клиенти</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-white/65">
                            Колкото по-завършен е профилът ти, толкова по-голям шанс имаш клиентите да се свържат с теб. Снимките, ясната категория, градът и телефонът правят профила по-доверен.
                        </p>
                    </div>

                    <div class="w-full rounded-3xl border border-white/10 bg-slate-950/45 p-5 xl:max-w-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm text-white/55">Завършеност</p>
                                <p class="mt-1 text-3xl font-black" data-testid="profile-completeness-percent">{{ $profile['percent'] }}%</p>
                            </div>
                            <a href="{{ route('business.profile.edit') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-4 py-2 text-sm font-black text-white" data-testid="onboarding-fill-profile-cta">Попълни профила</a>
                        </div>
                        <div class="mt-4 h-3 rounded-full bg-white/10">
                            <div class="h-3 rounded-full bg-gradient-to-r from-orange-400 to-orange-600" style="width: {{ $profile['percent'] }}%"></div>
                        </div>
                    </div>
                </div>

                <div data-testid="business-onboarding-conversion-steps" class="mt-6 grid gap-3 md:grid-cols-5">
                    @foreach([
                        ['label' => 'Попълни профила', 'text' => 'Име, град, категория и телефон.'],
                        ['label' => 'Добави снимки', 'text' => 'Покажи обекти, работа или екип.'],
                        ['label' => 'Избери план', 'text' => 'Standard или Premium според нуждите.'],
                        ['label' => 'Получавай заявки', 'text' => 'Клиентите стигат до теб от профила.'],
                        ['label' => 'Следи резултати', 'text' => 'Преглеждания, кликове и заявки.'],
                    ] as $index => $step)
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-orange-300/10 text-sm font-black text-orange-100">{{ $index + 1 }}</span>
                            <p class="mt-3 font-black">{{ $step['label'] }}</p>
                            <p class="mt-1 text-xs leading-5 text-white/55">{{ $step['text'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($onboardingItems as $item)
                        <div data-testid="onboarding-item-{{ $item['key'] }}" class="rounded-3xl border {{ $item['complete'] ? 'border-emerald-300/20 bg-emerald-400/10' : 'border-white/10 bg-slate-950/45' }} p-5">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl {{ $item['complete'] ? 'bg-emerald-400/20 text-emerald-100' : 'bg-white/10 text-white/55' }}">
                                    {{ $item['complete'] ? '✓' : '•' }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-black">{{ $item['label'] }}</p>
                                    <p class="mt-1 text-sm text-white/55">{{ $item['weight'] }}% от завършеността</p>
                                    <p class="mt-2 text-xs font-black {{ $item['complete'] ? 'text-emerald-100' : 'text-amber-100' }}">{{ $item['complete'] ? 'Готово' : 'Липсва' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @unless($business->is_verified)
                    <div class="mt-5 rounded-3xl border border-amber-300/20 bg-amber-400/10 p-5" data-testid="business-verification-pending">
                        <p class="font-black text-amber-100">Профилът чака проверка от админ</p>
                        <p class="mt-2 text-sm leading-6 text-white/65">След като попълните основните данни, админ може да потвърди профила ви и да получите badge “Потвърден”.</p>
                    </div>
                @endunless

                <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <a href="{{ route('business.profile.edit') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-4 text-center font-black text-white">Попълни профила</a>
                    <a href="{{ route('services.create') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Добави услуга</a>
                    <a href="{{ route('business.service-requests.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-orange-300/20 bg-orange-300/10 px-5 py-4 text-center font-black text-orange-100 hover:bg-orange-300/15">Виж заявки</a>
                    <a href="{{ route('businesses.show', $business) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Виж публичния профил</a>
                    <a href="{{ route('business.billing') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Управление на плана</a>
                </div>
            </section>

            <section class="grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-[32px] border {{ $subscriptionStatus === 'expired' || $subscriptionStatus === 'cancelled' ? 'border-rose-300/30 bg-rose-500/10' : 'border-orange-300/20 bg-white/10' }} p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.25em] text-white/50">Статус и план</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusClass }}">{{ $statusText }}</span>
                                <span class="rounded-full border border-orange-300/20 bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $business->planLabel() }}</span>
                                @if($business->is_verified)
                                    <span class="rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">Потвърден</span>
                                @else
                                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-black text-white/60">Непотвърден</span>
                                @endif
                            </div>

                            @if($subscriptionStatus === 'trial')
                                <h2 class="mt-5 text-2xl font-black sm:text-4xl">Остават {{ $trialDaysRemaining }} {{ $trialDaysRemaining === 1 ? 'ден' : 'дни' }} от trial периода</h2>
                                <p class="mt-3 max-w-3xl text-white/70">След края на trial периода профилът ще бъде скрит от публичните резултати, ако не активирате абонамент.</p>
                            @elseif($subscriptionStatus === 'active')
                                <h2 class="mt-5 text-2xl font-black sm:text-4xl">Профилът ви е активен</h2>
                                <p class="mt-3 max-w-3xl text-white/70">Клиентите могат да ви откриват в търсене, категории, препоръчани секции и публични списъци.</p>
                            @elseif($subscriptionStatus === 'cancelled')
                                <h2 class="mt-5 text-2xl font-black sm:text-4xl">Абонаментът е отменен</h2>
                                <p class="mt-3 max-w-3xl text-white/70">Профилът е скрит за публичните потребители, но вие можете да го редактирате и preview-нете.</p>
                            @else
                                <h2 class="mt-5 text-2xl font-black sm:text-4xl">Профилът е скрит</h2>
                                <p class="mt-3 max-w-3xl text-white/70">Trial периодът е изтекъл или няма активен абонамент. Собственикът все още може да вижда и подготвя профила.</p>
                            @endif

                            <div class="mt-5 grid gap-3 text-sm text-white/65 sm:grid-cols-2">
                                <p class="rounded-2xl bg-slate-950/45 p-4">План: <strong class="text-white">{{ $business->planLabel() }}</strong> · {{ number_format($business->planMonthlyAmount(), 2, ',', ' ') }} €/месец</p>
                                <p class="rounded-2xl bg-slate-950/45 p-4">Крайна дата: <strong class="text-white">{{ $endDate ? $endDate->format('d.m.Y') : 'без крайна дата' }}</strong></p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 lg:w-72">
                            @if($business->isPremium())
                                <p class="text-sm font-black uppercase text-orange-200">Premium предимство</p>
                                <p class="mt-3 text-sm leading-6 text-white/65">Вашият профил получава по-високо показване, Premium badge и място в препоръчани секции.</p>
                            @else
                                <p class="text-sm font-black uppercase text-orange-200">Upgrade</p>
                                <p class="mt-3 text-sm leading-6 text-white/65">Ъпгрейд към Premium отключва до 5 града, до 5 услуги, 15 снимки и по-високо подреждане.</p>
                                <a href="{{ route('business.billing') }}" class="mt-4 inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white">Ъпгрейд към Premium</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Завършеност</p>
                            <h2 class="mt-3 text-3xl font-black">{{ $profile['percent'] }}%</h2>
                        </div>
                        <a href="{{ route('business.profile.edit') }}" class="inline-flex min-h-11 items-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Попълни</a>
                    </div>
                    <div class="mt-5 h-3 rounded-full bg-white/10">
                        <div class="h-3 rounded-full bg-gradient-to-r from-orange-400 to-orange-600" style="width: {{ $profile['percent'] }}%"></div>
                    </div>
                    <p class="mt-4 text-sm text-white/60">{{ $profile['completed'] }} от {{ $profile['total'] }} ключови елемента са попълнени.</p>
                    <div class="mt-5 grid gap-2">
                        @forelse($profile['missing'] as $missing)
                            <p class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-sm text-white/70">Добавете: {{ $missing }}</p>
                        @empty
                            <p class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm font-bold text-emerald-100">Профилът изглежда готов за клиенти.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($limitCards as $limit)
                    @php
                        $isOverLimit = $limit['value'] > $limit['limit'];
                        $percentage = $limit['limit'] > 0 ? min(100, (int) round(($limit['value'] / $limit['limit']) * 100)) : 0;
                    @endphp
                    <div class="rounded-3xl border {{ $isOverLimit ? 'border-rose-300/30 bg-rose-400/10' : 'border-white/10 bg-white/10' }} p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-white/60">{{ $limit['label'] }}</p>
                            <span class="rounded-full bg-slate-950/55 px-3 py-1 text-xs font-black text-white/70">{{ $limit['value'] }} / {{ $limit['limit'] }}</span>
                        </div>
                        <div class="mt-4 h-2 rounded-full bg-white/10">
                            <div class="h-2 rounded-full {{ $isOverLimit ? 'bg-rose-400' : 'bg-gradient-to-r from-orange-400 to-orange-600' }}" style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-white/55">{{ $limit['note'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Analytics</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Активност за текущия месец</h2>
                        <p class="mt-2 text-sm text-white/60">Показва реални преглеждания и кликове, записани от публичния профил.</p>
                    </div>
                    <span class="rounded-full bg-slate-950/50 px-4 py-2 text-sm font-black text-white/70">Общо кликове: {{ $analyticsStats['total_clicks'] }}</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach([
                    ['testid' => 'analytics-month-profile-views', 'label' => 'Преглеждания', 'value' => $analyticsStats['profile_views'], 'note' => 'публичен профил'],
                    ['testid' => 'analytics-month-phone-clicks', 'label' => 'Телефон', 'value' => $analyticsStats['phone_clicks'], 'note' => 'кликове за обаждане'],
                    ['testid' => 'service-requests-total', 'label' => 'Получени заявки', 'value' => $serviceRequestStats['total'], 'note' => 'общо към профила'],
                    ['testid' => 'service-requests-new', 'label' => 'Нови заявки', 'value' => $serviceRequestStats['new'], 'note' => 'чакат реакция'],
                    ['testid' => 'service-requests-contacted', 'label' => 'Свързани клиенти', 'value' => $serviceRequestStats['contacted'], 'note' => 'маркирани като свързани'],
                    ['testid' => 'service-requests-completed', 'label' => 'Завършени заявки', 'value' => $serviceRequestStats['completed'], 'note' => 'приключени успешно'],
                ] as $stat)
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <p class="text-sm text-white/60">{{ $stat['label'] }}</p>
                        <p data-testid="{{ $stat['testid'] }}" class="mt-3 text-4xl font-black">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-sm text-orange-200">{{ $stat['note'] }}</p>
                    </div>
                @endforeach
                </div>

                <div class="mt-6 rounded-3xl border border-orange-300/20 bg-gradient-to-br from-orange-400/10 via-orange-500/10 to-orange-500/10 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.2em] text-orange-200/80">Стойност от BON</p>
                            <p class="mt-2 text-lg font-black leading-7">
                                Вашият профил е видян <span data-testid="value-profile-views">{{ $analyticsStats['profile_views'] }}</span> пъти и е получил <span data-testid="value-client-actions">{{ $analyticsStats['total_clicks'] + $serviceRequestStats['total'] }}</span> клиентски действия.
                            </p>
                            @if($business->isPremium())
                                <p data-testid="premium-value-message" class="mt-3 text-sm leading-6 text-orange-100">Premium профилите получават по-високо позициониране и повече видимост в резултатите.</p>
                            @else
                                <p data-testid="standard-upgrade-hint" class="mt-3 text-sm leading-6 text-orange-100">Искате повече видимост? Premium профилите се показват по-напред в резултатите.</p>
                            @endif
                        </div>
                        <a href="{{ route('business.service-requests.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-center text-sm font-black text-white hover:bg-white/15">Виж всички заявки</a>
                    </div>
                </div>

                @if($business->isPremium())
                    <div class="mt-6 rounded-3xl border border-orange-300/20 bg-orange-400/10 p-5">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-black uppercase text-orange-200">Premium analytics</p>
                                <h3 class="mt-2 text-xl font-black">Обща активност от началото</h3>
                            </div>
                            <p class="rounded-full bg-slate-950/45 px-4 py-2 text-sm font-black text-white/75">Общо кликове: {{ $analyticsTotals['total_clicks'] }}</p>
                        </div>
                        <div class="mt-5 grid gap-3 md:grid-cols-3 xl:grid-cols-6">
                            @foreach([
                                ['testid' => 'analytics-total-profile-views', 'label' => 'Views', 'value' => $analyticsTotals['profile_views']],
                                ['testid' => 'analytics-total-phone-clicks', 'label' => 'Phone', 'value' => $analyticsTotals['phone_clicks']],
                                ['testid' => 'analytics-total-inquiry-clicks', 'label' => 'Inquiry', 'value' => $analyticsTotals['inquiry_clicks']],
                                ['testid' => 'analytics-total-chat-clicks', 'label' => 'Chat', 'value' => $analyticsTotals['chat_clicks']],
                                ['testid' => 'analytics-total-website-clicks', 'label' => 'Website', 'value' => $analyticsTotals['website_clicks']],
                                ['testid' => 'analytics-total-social-clicks', 'label' => 'Social', 'value' => $analyticsTotals['social_clicks']],
                            ] as $totalStat)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                                    <p class="text-xs text-white/45">{{ $totalStat['label'] }}</p>
                                    <p data-testid="{{ $totalStat['testid'] }}" class="mt-2 text-2xl font-black">{{ $totalStat['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-3xl border border-orange-300/20 bg-orange-300/10 p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-black uppercase text-orange-200">Standard analytics</p>
                                <p class="mt-2 text-sm leading-6 text-white/70">Виждате основните месечни показатели. Premium отключва разбивка по всички event types и total analytics.</p>
                                <p class="mt-2 text-sm text-white/55">Total: {{ $analyticsTotals['profile_views'] }} преглеждания · {{ $analyticsTotals['total_clicks'] }} клика.</p>
                            </div>
                            <a href="{{ route('business.billing') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-center text-sm font-black text-white">Ъпгрейд към Premium</a>
                        </div>
                    </div>
                @endif
            </section>

            <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Assigned leads</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Заявки, изпратени към вашия профил</h2>
                        <p class="mt-2 text-sm text-white/60">Тук виждате само заявки, които системата или admin е изпратила към вашия профил. Чужди заявки не се показват.</p>
                    </div>
                    <a href="{{ route('request.service') }}" class="rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-center text-sm font-black text-white hover:bg-white/15">Виж публичната форма</a>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    @forelse($assignedServiceRequests as $assignment)
                        @php
                            $serviceRequest = $assignment->serviceRequest;
                        @endphp
                        @continue(!$serviceRequest)
                        <article class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black">{{ $serviceRequest->name }}</p>
                                    <p class="mt-1 text-sm text-white/55">{{ $serviceRequest->phone }}</p>
                                    @if($serviceRequest->email)
                                        <p class="mt-1 text-sm text-white/45">{{ $serviceRequest->email }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $assignment->status }}</span>
                                    <span class="rounded-full {{ $serviceRequest->urgency === 'urgent' ? 'bg-rose-400/10 text-rose-100' : 'bg-white/10 text-white/70' }} px-3 py-1 text-xs font-black">{{ $serviceRequest->urgency === 'urgent' ? 'Спешна' : 'Нормална' }}</span>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-3 text-sm text-white/65 sm:grid-cols-2">
                                <p class="rounded-2xl bg-white/5 px-4 py-3">Град: <strong class="text-white">{{ $serviceRequest->city }}</strong></p>
                                <p class="rounded-2xl bg-white/5 px-4 py-3">Категория: <strong class="text-white">{{ $serviceRequest->category ?: 'Не е посочена' }}</strong></p>
                                <p class="rounded-2xl bg-white/5 px-4 py-3">Услуга: <strong class="text-white">{{ $serviceRequest->service ?: 'Не е посочена' }}</strong></p>
                                <p class="rounded-2xl bg-white/5 px-4 py-3">Бюджет: <strong class="text-white">{{ $serviceRequest->budget ?: 'Не е посочен' }}</strong></p>
                            </div>
                            <p class="mt-4 rounded-2xl bg-white/5 px-4 py-3 text-sm leading-6 text-white/70">{{ $serviceRequest->description }}</p>
                            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                @if($assignment->status !== 'contacted')
                                    <form action="{{ route('service-request-assignments.contacted', $assignment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full rounded-2xl bg-emerald-400/10 px-4 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-400/20">Свързах се с клиента</button>
                                    </form>
                                @endif
                                @if($assignment->status !== 'declined')
                                    <form action="{{ route('service-request-assignments.declined', $assignment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full rounded-2xl bg-rose-400/10 px-4 py-3 text-sm font-black text-rose-100 hover:bg-rose-400/20">Не мога да поема</button>
                                    </form>
                                @endif
                                @if($assignment->status !== 'closed')
                                    <form action="{{ route('service-request-assignments.closed', $assignment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full rounded-2xl bg-white/10 px-4 py-3 text-sm font-black text-white hover:bg-white/15">Затвори</button>
                                    </form>
                                @endif
                            </div>
                            <p class="mt-3 text-xs text-white/40">Изпратена: {{ $assignment->sent_at?->format('d.m.Y H:i') ?: $assignment->created_at?->format('d.m.Y H:i') }} · Заявка: {{ $serviceRequest->created_at?->format('d.m.Y H:i') }}</p>
                        </article>
                    @empty
                        <div class="lg:col-span-2 rounded-3xl border border-white/10 bg-slate-950/45 p-8 text-center">
                            <p class="font-black">Все още няма изпратени заявки</p>
                            <p class="mt-2 text-sm text-white/55">Когато системата или admin насочи заявка към вашия профил, тя ще се появи тук с контакт и описание.</p>
                            <a href="{{ route('businesses.show', $business) }}" class="mt-5 inline-flex rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white">Сподели профила си</a>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Отзиви</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Мнения от клиенти</h2>
                        <p class="mt-2 text-sm text-white/60">Виждате всички последни отзиви, но одобрението остава само за админ, за да има доверие в рейтинга.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                            <p class="text-xs text-white/45">Средна</p>
                            <p class="mt-1 font-black">{{ $averageReviewRating ? number_format($averageReviewRating, 1, '.', '') : '—' }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                            <p class="text-xs text-white/45">Approved</p>
                            <p class="mt-1 font-black">{{ $reviewStats['approved'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                            <p class="text-xs text-white/45">Pending</p>
                            <p class="mt-1 font-black">{{ $reviewStats['pending'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    @forelse($businessReviews as $review)
                        <article class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-black">{{ $review->reviewer_name }}</p>
                                    <p class="mt-1 text-sm text-amber-200">{{ str_repeat('★', $review->rating) }} <span class="text-white/45">· {{ $review->rating }}/5</span></p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $review->status === 'approved' ? 'bg-emerald-400/10 text-emerald-100' : ($review->status === 'pending' ? 'bg-amber-400/10 text-amber-100' : 'bg-rose-400/10 text-rose-100') }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-white/70">{{ $review->comment }}</p>
                            <p class="mt-4 text-xs text-white/40">{{ $review->created_at?->format('d.m.Y H:i') }}</p>
                        </article>
                    @empty
                        <div class="lg:col-span-2 rounded-3xl border border-white/10 bg-slate-950/45 p-6 text-center">
                            <p class="font-black">Все още няма отзиви</p>
                            <p class="mt-2 text-sm text-white/55">Споделете публичния профил и поканете доволни клиенти да оставят мнение.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            @php
                $profileUrl = route('businesses.show', $business);
                $shareText = 'Вече можете да ни намерите във BON. Разгледайте профила ни, оставете отзив или се свържете директно с нас.';
                $shareMessage = $shareText . ' ' . $profileUrl;
                $encodedProfileUrl = urlencode($profileUrl);
                $encodedShareMessage = urlencode($shareMessage);
                $badgeCode = '<a href="' . e($profileUrl) . '" style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:14px;background:#061426;color:#fff;text-decoration:none;font-weight:800;">Ние сме във BON</a>';
            @endphp

            <section data-testid="business-share-section" class="rounded-[32px] border border-orange-300/20 bg-gradient-to-br from-orange-400/12 via-orange-500/10 to-orange-600/15 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Share growth</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Сподели профила си</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">{{ $shareText }}</p>
                    </div>
                    <a href="{{ route('businesses.show', $business) }}" class="rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-center font-black text-white hover:bg-white/15">Виж публичния профил</a>
                </div>

                <div class="mt-6 grid gap-4 xl:grid-cols-[1fr_0.9fr]">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <label for="business-profile-link" class="text-sm font-black text-white/70">Публичен линк</label>
                        <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_auto]">
                            <input id="business-profile-link" type="text" readonly value="{{ $profileUrl }}" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white outline-none">
                            <button type="button" onclick="navigator.clipboard?.writeText(document.getElementById('business-profile-link').value)" class="rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white">Copy link</button>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedProfileUrl }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm font-black text-white hover:bg-white/10">Facebook</a>
                            <a target="_blank" rel="noopener" href="https://wa.me/?text={{ $encodedShareMessage }}" class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-center text-sm font-black text-emerald-100">WhatsApp</a>
                            <a href="viber://forward?text={{ $encodedShareMessage }}" class="rounded-2xl border border-orange-300/20 bg-orange-400/10 px-4 py-3 text-center text-sm font-black text-orange-100">Viber</a>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <p class="text-sm font-black text-white/70">Badge cards</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <p class="font-black">Ние сме във BON</p>
                                <p class="mt-1 text-xs text-white/50">Готово за социални мрежи и сайт.</p>
                            </div>
                            @if($business->is_verified)
                                <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-4">
                                    <p class="font-black text-emerald-100">Потвърден бизнес във BON</p>
                                    <p class="mt-1 text-xs text-white/55">Показва доверие към профила.</p>
                                </div>
                            @endif
                            @if($business->isPremium())
                                <div class="rounded-2xl border border-orange-300/20 bg-orange-400/10 p-4">
                                    <p class="font-black text-orange-100">Препоръчан бизнес във BON</p>
                                    <p class="mt-1 text-xs text-white/55">Premium профил с предимство.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                    <label for="business-badge-code" class="text-sm font-black text-white/70">Embeddable badge code</label>
                    <textarea id="business-badge-code" rows="3" readonly class="mt-3 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white outline-none">{{ $badgeCode }}</textarea>
                </div>
            </section>

            @unless($hasAnalyticsData)
                <section class="rounded-[32px] border border-orange-300/20 bg-orange-300/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-2xl font-black">Все още няма активност</h2>
                            <p class="mt-2 text-sm leading-6 text-white/70">Споделете публичния профил, добавете услуги и попълнете описанието, за да започнете да събирате преглеждания, кликове и запитвания.</p>
                        </div>
                        <a href="{{ route('businesses.show', $business) }}" class="rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-center font-black text-white hover:bg-white/15">Сподели профила</a>
                    </div>
                </section>
            @endunless

            <section class="grid gap-6 xl:grid-cols-[1fr_380px]">
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-black">Управление на профила</h2>
                            <p class="mt-2 text-sm text-white/60">Бързи действия за информация, услуги, снимки и публичен preview.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('business.profile.edit') }}" class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 transition hover:border-orange-300/30 hover:bg-white/10">
                            <p class="text-lg font-black">Основна информация</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">Име, описание, контакти, работно време, социални линкове.</p>
                        </a>
                        <a href="{{ route('business.profile.edit') }}#cities" class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 transition hover:border-orange-300/30 hover:bg-white/10">
                            <p class="text-lg font-black">Градове и райони</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">Избрани {{ $cityCount }} от {{ $business->cityLimit() }} позволени града.</p>
                        </a>
                        <a href="{{ route('services.create') }}" class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 transition hover:border-orange-300/30 hover:bg-white/10">
                            <p class="text-lg font-black">Услуги с цени</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">{{ $serviceCount }} от {{ $business->categoryLimit() }} услуги са публикувани.</p>
                        </a>
                        <a href="{{ route('business.profile.edit') }}#gallery" class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 transition hover:border-orange-300/30 hover:bg-white/10">
                            <p class="text-lg font-black">Снимки</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">{{ $photoCount }} от {{ $business->photoLimit() }} снимки са използвани в отделната галерия на профила.</p>
                        </a>
                    </div>
                </div>

                <div class="rounded-[32px] border border-orange-300/20 bg-gradient-to-br from-orange-500/15 via-orange-500/10 to-orange-400/15 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm font-black uppercase text-orange-200">Планови лимити</p>
                    <h2 class="mt-2 text-2xl font-black">Standard или Premium</h2>
                    <div class="mt-5 grid gap-3 text-sm leading-6 text-white/70">
                        <p class="rounded-2xl bg-slate-950/40 p-4">Standard: 18,99 €/месец, до 2 града, 2 категории/услуги и 5 снимки.</p>
                        <p class="rounded-2xl bg-slate-950/40 p-4">Premium: 24,99 €/месец, до 5 града, 5 категории/услуги, 15 снимки и по-високо показване.</p>
                        <p class="rounded-2xl bg-slate-950/40 p-4">Допълнителен Premium град над лимита: +3,99 €/месец.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
    @include('partials.mobile-bottom-nav')
</body>
</html>

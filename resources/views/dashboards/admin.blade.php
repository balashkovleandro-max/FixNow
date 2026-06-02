<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin табло | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    @php
        $adminStats = array_merge([
            'total_users' => 0,
            'total_businesses' => 0,
            'active_businesses' => 0,
            'trial_businesses' => 0,
            'expired_businesses' => 0,
            'cancelled_businesses' => 0,
            'verified_businesses' => 0,
            'unverified_businesses' => 0,
            'new_businesses_last_7_days' => 0,
            'standard_businesses' => 0,
            'premium_businesses' => 0,
            'total_extra_cities_used' => 0,
            'potential_mrr' => 0,
            'trial_pipeline' => 0,
            'estimated_conversion' => 0,
        ], $adminStats ?? []);

        $businesses = $businesses ?? collect();
        $pendingBusinesses = $pendingBusinesses ?? collect();
        $businessFilter = $businessFilter ?? 'all';
        $pendingReviews = $pendingReviews ?? collect();
        $serviceRequests = $serviceRequests ?? collect();
        $leadStats = array_merge([
            'total' => 0,
            'new' => 0,
            'contacted' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'closed' => 0,
            'urgent' => 0,
        ], $leadStats ?? []);
        $platformAnalytics = array_merge([
            'total_profile_views' => 0,
            'total_clicks' => 0,
            'current_month_profile_views' => 0,
            'current_month_phone_clicks' => 0,
            'current_month_clicks' => 0,
            'top_by_views' => collect(),
            'top_by_clicks' => collect(),
        ], $platformAnalytics ?? []);

        $filters = [
            'all' => 'Всички',
            'trial' => 'Trial',
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'verified' => 'Verified',
            'unverified' => 'Unverified',
            'expiring_soon' => 'Изтича скоро',
        ];

        $statusLabels = [
            'trial' => 'Trial',
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'inactive' => 'Inactive',
        ];

        $leadStatusLabels = [
            'new' => 'Нова',
            'contacted' => 'Свързан',
            'closed' => 'Затворена',
        ];

        $leadUrgencyLabels = [
            'normal' => 'Нормална',
            'urgent' => 'Спешна',
        ];
    @endphp

    <main class="mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-6 rounded-[28px] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-300 via-orange-500 to-orange-600 font-black">F</div>
                    <div>
                        <p class="text-xl font-black">FixNow.bg</p>
                        <p class="text-xs text-white/50">Admin control</p>
                    </div>
                </a>

                <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-white/70">
                    <a href="{{ url('/') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Начало</a>
                    <a href="{{ url('/categories') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Категории</a>
                    <a href="{{ route('services.index') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Услуги</a>
                    <a href="{{ route('businesses.index') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Публични изпълнители</a>
                    <a href="{{ route('admin.service-requests.index') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Заявки</a>
                    <a href="{{ route('dashboard') }}" class="rounded-2xl bg-orange-300/10 px-4 py-2 text-orange-100">Admin</a>
                </nav>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-left text-sm font-bold text-white/70 hover:bg-white/10 hover:text-white lg:w-auto">Изход</button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">{{ session('success') }}</div>
        @endif

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Admin Control MVP</p>
            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black sm:text-5xl">Управление на изпълнители и статуси</h1>
                    <p class="mt-3 max-w-3xl text-white/60">Контрол върху trial, active, expired, cancelled и verified статуси. Всички показатели са реални query counts.</p>
                </div>
                <div class="rounded-3xl border border-orange-300/20 bg-orange-300/10 px-5 py-4">
                    <p class="text-sm text-orange-100">Потенциален MRR</p>
                    <p class="mt-1 text-3xl font-black">{{ number_format($adminStats['potential_mrr'], 2, ',', ' ') }} €</p>
                </div>
            </div>
        </section>

        <section data-testid="admin-pending-businesses" class="mt-6 rounded-[32px] border border-amber-300/20 bg-amber-400/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-amber-100/80">Business approval</p>
                    <h2 class="mt-2 text-2xl font-black sm:text-3xl">Изпълнители, чакащи проверка</h2>
                    <p class="mt-2 text-sm leading-6 text-white/65">Проверете профила, основните данни и плана, преди да дадете badge “Потвърден”.</p>
                </div>
                <a href="{{ route('dashboard', ['status' => 'unverified']) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-sm font-black text-white hover:bg-white/15">
                    {{ $pendingBusinesses->count() }} чакащи
                </a>
            </div>

            <div class="mt-6 grid gap-4 xl:grid-cols-2">
                @forelse($pendingBusinesses as $pendingBusiness)
                    @php
                        $pendingProfile = $pendingBusiness->profileCompleteness();
                        $pendingStatus = $pendingBusiness->effectiveSubscriptionStatus();
                    @endphp
                    <article class="rounded-3xl border border-white/10 bg-slate-950/55 p-5" data-testid="pending-business-card">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="min-w-0">
                                <p class="truncate text-lg font-black">{{ $pendingBusiness->business_name ?: $pendingBusiness->name }}</p>
                                <p class="mt-1 truncate text-sm text-white/50">{{ $pendingBusiness->email }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $pendingBusiness->planLabel() }}</span>
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $statusLabels[$pendingStatus] ?? $pendingStatus }}</span>
                                    <span class="rounded-full bg-amber-400/10 px-3 py-1 text-xs font-black text-amber-100">{{ $pendingProfile['percent'] }}% профил</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col gap-2 sm:flex-row md:flex-col">
                                <a href="{{ route('businesses.show', $pendingBusiness) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-black text-white hover:bg-white/10">Преглед</a>
                                <form action="{{ route('admin.businesses.verify', $pendingBusiness) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="min-h-11 w-full rounded-2xl bg-emerald-400/10 px-4 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-400/20">Одобри</button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-2 text-sm text-white/70 sm:grid-cols-2">
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Собственик: <strong class="text-white">{{ $pendingBusiness->name }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Град: <strong class="text-white">{{ $pendingBusiness->city ?: 'Няма' }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Категория: <strong class="text-white">{{ $pendingBusiness->business_category ?: 'Няма' }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Липсва: <strong class="text-white">{{ implode(', ', array_slice($pendingProfile['missing'], 0, 3)) ?: 'Нищо основно' }}</strong></p>
                        </div>
                    </article>
                @empty
                    <div class="xl:col-span-2 rounded-3xl border border-white/10 bg-slate-950/50 p-8 text-center" data-testid="admin-pending-businesses-empty">
                        <p class="font-black">Няма изпълнители, чакащи проверка</p>
                        <p class="mt-2 text-sm text-white/55">Когато нов изпълнител попълни профила си, той ще се появи тук за преглед и одобрение.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mt-6 rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Platform analytics</p>
                    <h2 class="mt-3 text-2xl font-black sm:text-3xl">Обща активност във FixNow</h2>
                    <p class="mt-2 text-sm text-white/60">Преглеждания и кликове от публичните профили на изпълнители.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center md:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                        <p class="text-xs text-white/45">Views total</p>
                        <p data-testid="admin-total-profile-views" class="mt-1 text-2xl font-black">{{ $platformAnalytics['total_profile_views'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                        <p class="text-xs text-white/45">Clicks total</p>
                        <p data-testid="admin-total-clicks" class="mt-1 text-2xl font-black">{{ $platformAnalytics['total_clicks'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                        <p class="text-xs text-white/45">Views 30 дни</p>
                        <p data-testid="admin-month-profile-views" class="mt-1 text-2xl font-black">{{ $platformAnalytics['current_month_profile_views'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                        <p class="text-xs text-white/45">Clicks 30 дни</p>
                        <p data-testid="admin-month-clicks" class="mt-1 text-2xl font-black">{{ $platformAnalytics['current_month_clicks'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3">
                        <p class="text-xs text-white/45">Phone clicks 30 дни</p>
                        <p data-testid="admin-month-phone-clicks" class="mt-1 text-2xl font-black">{{ $platformAnalytics['current_month_phone_clicks'] }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-4 xl:grid-cols-2">
                <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                    <h3 class="text-lg font-black">Top businesses by views</h3>
                    <div class="mt-4 grid gap-3">
                        @forelse($platformAnalytics['top_by_views'] as $row)
                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/5 px-4 py-3">
                                <span class="font-bold">{{ $row->business?->business_name ?: $row->business?->name ?: 'Изтрит изпълнител' }}</span>
                                <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $row->aggregate }}</span>
                            </div>
                        @empty
                            <p class="rounded-2xl bg-white/5 px-4 py-3 text-sm text-white/55">Все още няма profile views.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                    <h3 class="text-lg font-black">Top businesses by clicks</h3>
                    <div class="mt-4 grid gap-3">
                        @forelse($platformAnalytics['top_by_clicks'] as $row)
                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/5 px-4 py-3">
                                <span class="font-bold">{{ $row->business?->business_name ?: $row->business?->name ?: 'Изтрит изпълнител' }}</span>
                                <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $row->aggregate }}</span>
                            </div>
                        @empty
                            <p class="rounded-2xl bg-white/5 px-4 py-3 text-sm text-white/55">Все още няма click events.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['label' => 'Общо потребители', 'value' => $adminStats['total_users'], 'note' => 'всички роли'],
                ['label' => 'Общо изпълнители', 'value' => $adminStats['total_businesses'], 'note' => 'role business'],
                ['label' => 'Active изпълнители', 'value' => $adminStats['active_businesses'], 'note' => 'видими с active план'],
                ['label' => 'Trial изпълнители', 'value' => $adminStats['trial_businesses'], 'note' => 'в пробен период'],
                ['label' => 'Expired изпълнители', 'value' => $adminStats['expired_businesses'], 'note' => 'скрити публично'],
                ['label' => 'Cancelled изпълнители', 'value' => $adminStats['cancelled_businesses'], 'note' => 'отменени'],
                ['label' => 'Verified изпълнители', 'value' => $adminStats['verified_businesses'], 'note' => 'проверени'],
                ['label' => 'Unverified изпълнители', 'value' => $adminStats['unverified_businesses'], 'note' => 'чакат проверка'],
                ['label' => 'Нови изпълнители', 'value' => $adminStats['new_businesses_last_7_days'], 'note' => 'последните 7 дни'],
                ['label' => 'Standard планове', 'value' => $adminStats['standard_businesses'], 'note' => '18,99 € · до 2 града'],
                ['label' => 'Premium планове', 'value' => $adminStats['premium_businesses'], 'note' => '24,99 € · до 5 града'],
                ['label' => 'Точки за оферти', 'value' => '30/90', 'note' => 'Standard/Premium месечни точки'],
                ['label' => 'Trial pipeline', 'value' => number_format($adminStats['trial_pipeline'], 2, ',', ' ') . ' €', 'note' => 'trial планове'],
                ['label' => 'Estimated conversion', 'value' => number_format($adminStats['estimated_conversion'], 2, ',', ' ') . ' €', 'note' => 'trial pipeline x 25%'],
            ] as $stat)
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm text-white/60">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-black">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-orange-200">{{ $stat['note'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="mt-6 rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Lead generation</p>
                    <h2 class="mt-2 text-2xl font-black">Заявки за оферта</h2>
                    <p class="mt-2 text-sm text-white/60">Реални заявки от публичната форма. Admin може да маркира заявките като contacted или closed.</p>
                </div>
                <a href="{{ route('request.service') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-center text-sm font-black text-white hover:bg-white/15">Отвори публичната форма</a>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach([
                    ['testid' => 'admin-service-requests-total', 'label' => 'Общо заявки', 'value' => $leadStats['total'], 'class' => 'text-white bg-white/10'],
                    ['testid' => 'admin-service-requests-new', 'label' => 'Нови', 'value' => $leadStats['new'], 'class' => 'text-orange-100 bg-orange-400/10'],
                    ['testid' => 'admin-service-requests-contacted', 'label' => 'Свързани', 'value' => $leadStats['contacted'], 'class' => 'text-emerald-100 bg-emerald-400/10'],
                    ['testid' => 'admin-service-requests-completed', 'label' => 'Завършени', 'value' => $leadStats['completed'], 'class' => 'text-orange-100 bg-orange-400/10'],
                    ['testid' => 'admin-service-requests-cancelled', 'label' => 'Отказани', 'value' => $leadStats['cancelled'], 'class' => 'text-amber-100 bg-amber-400/10'],
                    ['testid' => 'admin-service-requests-urgent', 'label' => 'Спешни', 'value' => $leadStats['urgent'], 'class' => 'text-rose-100 bg-rose-400/10'],
                ] as $stat)
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <p class="text-sm text-white/55">{{ $stat['label'] }}</p>
                        <p data-testid="{{ $stat['testid'] }}" class="mt-2 text-3xl font-black">{{ $stat['value'] }}</p>
                        <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-black {{ $stat['class'] }}">service requests</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-4 lg:hidden">
                @forelse($serviceRequests as $serviceRequest)
                    @php
                        $assignments = $serviceRequest->relationLoaded('assignments') ? $serviceRequest->assignments : collect();
                    @endphp
                    <article class="rounded-3xl border border-white/10 bg-slate-950/55 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-lg font-black">{{ $serviceRequest->name }}</p>
                                <p class="mt-1 text-sm font-bold text-orange-200">{{ $serviceRequest->phone }}</p>
                                @if($serviceRequest->email)
                                    <p class="mt-1 truncate text-xs text-white/45">{{ $serviceRequest->email }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-black">{{ $leadStatusLabels[$serviceRequest->status] ?? $serviceRequest->status }}</span>
                        </div>

                        <div class="mt-4 grid gap-2 text-sm text-white/70">
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Град: <strong class="text-white">{{ $serviceRequest->city }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Услуга: <strong class="text-white">{{ $serviceRequest->category ?: 'Без категория' }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Спешност: <strong class="{{ $serviceRequest->urgency === 'urgent' ? 'text-rose-200' : 'text-white' }}">{{ $leadUrgencyLabels[$serviceRequest->urgency] ?? $serviceRequest->urgency }}</strong></p>
                        </div>

                        <p class="mt-4 rounded-2xl bg-white/5 px-4 py-3 text-sm leading-6 text-white/65">{{ \Illuminate\Support\Str::limit($serviceRequest->description, 180) }}</p>

                        <div class="mt-4 grid gap-2">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Изпратено към</p>
                            @forelse($assignments as $assignment)
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="font-bold">{{ $assignment->business?->business_name ?: $assignment->business?->name ?: 'Изтрит изпълнител' }}</p>
                                    <p class="mt-1 text-xs text-white/50">{{ $assignment->status }}{{ $assignment->contacted_at ? ' · '.$assignment->contacted_at->format('d.m.Y H:i') : '' }}</p>
                                </div>
                            @empty
                                <p class="rounded-2xl bg-amber-400/10 px-4 py-3 text-sm font-bold text-amber-100">Няма автоматично назначени изпълнители</p>
                            @endforelse
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            @if($serviceRequest->status !== 'contacted')
                                <form action="{{ route('admin.service-requests.contacted', $serviceRequest) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="min-h-11 w-full rounded-2xl bg-emerald-400/10 px-4 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-400/20">Contacted</button>
                                </form>
                            @endif
                            @if($serviceRequest->status !== 'closed')
                                <form action="{{ route('admin.service-requests.closed', $serviceRequest) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="min-h-11 w-full rounded-2xl bg-white/10 px-4 py-3 text-sm font-black text-white hover:bg-white/15">Closed</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-white/10 bg-slate-950/55 px-5 py-8 text-center">
                        <p class="font-black">Все още няма заявки за оферта</p>
                        <p class="mt-2 text-sm text-white/55">Когато клиент попълни формата, заявката ще се появи тук.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 hidden overflow-x-auto lg:block">
                <table class="min-w-[1180px] w-full border-separate border-spacing-y-3 text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.18em] text-white/45">
                        <tr>
                            <th class="px-4 py-2">Клиент</th>
                            <th class="px-4 py-2">Локация</th>
                            <th class="px-4 py-2">Услуга</th>
                            <th class="px-4 py-2">Описание</th>
                            <th class="px-4 py-2">Статус</th>
                            <th class="px-4 py-2">Изпратено към</th>
                            <th class="px-4 py-2">Дата</th>
                            <th class="px-4 py-2 text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRequests as $serviceRequest)
                            @php
                                $assignments = $serviceRequest->relationLoaded('assignments') ? $serviceRequest->assignments : collect();
                            @endphp
                            <tr>
                                <td class="rounded-l-3xl border-y border-l border-white/10 bg-slate-950/55 px-4 py-4 align-top">
                                    <p class="font-black">{{ $serviceRequest->name }}</p>
                                    <p class="mt-1 text-xs text-white/55">{{ $serviceRequest->phone }}</p>
                                    @if($serviceRequest->email)
                                        <p class="mt-1 text-xs text-white/45">{{ $serviceRequest->email }}</p>
                                    @endif
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/55 px-4 py-4 align-top">
                                    <p class="font-bold">{{ $serviceRequest->city }}</p>
                                    <p class="mt-1 text-xs {{ $serviceRequest->urgency === 'urgent' ? 'text-rose-200' : 'text-white/45' }}">{{ $leadUrgencyLabels[$serviceRequest->urgency] ?? $serviceRequest->urgency }}</p>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/55 px-4 py-4 align-top">
                                    <p class="font-bold">{{ $serviceRequest->category ?: 'Без категория' }}</p>
                                    <p class="mt-1 text-xs text-white/50">{{ $serviceRequest->service ?: 'Не е посочена конкретна услуга' }}</p>
                                    @if($serviceRequest->budget)
                                        <p class="mt-2 rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $serviceRequest->budget }}</p>
                                    @endif
                                </td>
                                <td class="max-w-sm border-y border-white/10 bg-slate-950/55 px-4 py-4 align-top text-white/65">
                                    {{ \Illuminate\Support\Str::limit($serviceRequest->description, 130) }}
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/55 px-4 py-4 align-top">
                                    <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-black">{{ $leadStatusLabels[$serviceRequest->status] ?? $serviceRequest->status }}</span>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/55 px-4 py-4 align-top">
                                    <div class="grid gap-2">
                                        @forelse($assignments as $assignment)
                                            <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                                                <p class="font-bold">{{ $assignment->business?->business_name ?: $assignment->business?->name ?: 'Изтрит изпълнител' }}</p>
                                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-white/50">
                                                    <span class="rounded-full bg-white/10 px-2 py-1">{{ $assignment->status }}</span>
                                                    @if($assignment->contacted_at)
                                                        <span>Свързал се: {{ $assignment->contacted_at->format('d.m.Y H:i') }}</span>
                                                    @elseif($assignment->sent_at)
                                                        <span>Изпратено: {{ $assignment->sent_at->format('d.m.Y H:i') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            @if($serviceRequest->assignedBusiness)
                                                <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                                                    <p class="font-bold">{{ $serviceRequest->assignedBusiness->business_name ?: $serviceRequest->assignedBusiness->name }}</p>
                                                    <p class="mt-1 text-xs text-white/50">legacy assigned_business_id</p>
                                                </div>
                                            @else
                                                <span class="rounded-full bg-amber-400/10 px-3 py-1 text-xs font-black text-amber-100">Няма автоматично назначени изпълнители</span>
                                            @endif
                                        @endforelse
                                    </div>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/55 px-4 py-4 align-top text-white/55">
                                    {{ $serviceRequest->created_at?->format('d.m.Y H:i') }}
                                </td>
                                <td class="rounded-r-3xl border-y border-r border-white/10 bg-slate-950/55 px-4 py-4 align-top">
                                    <div class="flex flex-col gap-2">
                                        @if($serviceRequest->status !== 'contacted')
                                            <form action="{{ route('admin.service-requests.contacted', $serviceRequest) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full rounded-2xl bg-emerald-400/10 px-4 py-2 text-xs font-black text-emerald-100 hover:bg-emerald-400/20">Mark contacted</button>
                                            </form>
                                        @endif
                                        @if($serviceRequest->status !== 'closed')
                                            <form action="{{ route('admin.service-requests.closed', $serviceRequest) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full rounded-2xl bg-white/10 px-4 py-2 text-xs font-black text-white hover:bg-white/15">Mark closed</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="rounded-3xl border border-white/10 bg-slate-950/55 px-5 py-8 text-center">
                                    <p class="font-black">Все още няма заявки за оферта</p>
                                    <p class="mt-2 text-sm text-white/55">Когато клиент попълни формата, заявката ще се появи тук.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-6 rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-2xl font-black">Чакащи отзиви</h2>
                    <p class="mt-2 text-sm text-white/60">Одобрявайте само реални и полезни мнения. Pending отзивите не се показват публично.</p>
                </div>
                <span class="rounded-full bg-amber-400/10 px-4 py-2 text-sm font-black text-amber-100">{{ $pendingReviews->count() }} pending</span>
            </div>

            <div class="mt-6 grid gap-4 xl:grid-cols-2">
                @forelse($pendingReviews as $review)
                    <article class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="font-black">{{ $review->reviewer_name }} <span class="text-amber-200">{{ str_repeat('★', $review->rating) }}</span></p>
                                <p class="mt-1 text-sm text-orange-200">{{ $review->business?->business_name ?: $review->business?->name ?: 'Изтрит изпълнител' }}</p>
                                <p class="mt-3 text-sm leading-6 text-white/70">{{ $review->comment }}</p>
                                <p class="mt-3 text-xs text-white/40">{{ $review->created_at?->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="grid min-w-[180px] gap-2">
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full rounded-2xl bg-emerald-400/10 px-4 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-400/20">Одобри</button>
                                </form>
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full rounded-2xl bg-rose-400/10 px-4 py-3 text-sm font-black text-rose-100 hover:bg-rose-400/20">Отхвърли</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="xl:col-span-2 rounded-3xl border border-white/10 bg-slate-950/50 p-8 text-center">
                        <p class="font-black">Няма чакащи отзиви</p>
                        <p class="mt-2 text-sm text-white/55">Когато клиент изпрати нов отзив, той ще се появи тук за moderation.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mt-6 rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="text-2xl font-black">Изпълнители</h2>
                    <p class="mt-2 text-sm text-white/60">Филтрирай и управлявай основните premium статуси.</p>
                </div>
                <div class="flex max-w-full gap-2 overflow-x-auto pb-1 xl:flex-wrap xl:overflow-visible">
                    @foreach($filters as $value => $label)
                        <a href="{{ $value === 'all' ? route('dashboard') : route('dashboard', ['status' => $value]) }}" class="shrink-0 rounded-2xl border px-4 py-2 text-sm font-bold {{ $businessFilter === $value ? 'border-orange-300/40 bg-orange-300/10 text-orange-100' : 'border-white/10 bg-white/5 text-white/70 hover:bg-white/10 hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 grid gap-4 lg:hidden">
                @forelse($businesses as $business)
                    @php
                        $effectiveStatus = $business->effectiveSubscriptionStatus();
                        $extraCitiesUsed = $business->extraCitiesUsed();
                        $adminBillingEndDate = $effectiveStatus === 'trial' ? $business->trial_ends_at : $business->subscription_ends_at;
                    @endphp
                    <article class="rounded-3xl border border-white/10 bg-slate-950/55 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-lg font-black">{{ $business->business_name ?: $business->name }}</p>
                                <p class="mt-1 truncate text-sm text-white/50">{{ $business->email }}</p>
                                <a href="{{ route('businesses.show', $business) }}" class="mt-2 inline-flex text-sm font-bold text-orange-200 hover:text-white">Виж профил</a>
                            </div>
                            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-black {{ $business->planKey() === 'premium' ? 'bg-orange-400/10 text-orange-100' : 'bg-orange-400/10 text-orange-100' }}">{{ $business->planLabel() }}</span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $effectiveStatus === 'active' ? 'bg-emerald-400/10 text-emerald-200' : ($effectiveStatus === 'trial' ? 'bg-orange-400/10 text-orange-200' : 'bg-rose-400/10 text-rose-200') }}">{{ $statusLabels[$effectiveStatus] ?? $effectiveStatus }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $business->is_verified ? 'Verified' : 'Unverified' }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $business->serviceCityCount() }} / {{ $business->cityLimit() }} града</span>
                        </div>

                        <div class="mt-4 grid gap-2 text-sm text-white/70">
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Град: <strong class="text-white">{{ $business->city ?: 'Няма' }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Категория: <strong class="text-white">{{ $business->business_category ?: 'Няма' }}</strong></p>
                            <p class="rounded-2xl bg-white/5 px-4 py-3">Край: <strong class="text-white">{{ $adminBillingEndDate ? $adminBillingEndDate->format('d.m.Y') : 'Без дата' }}</strong></p>
                            @if($extraCitiesUsed > 0)
                                <p class="rounded-2xl bg-amber-400/10 px-4 py-3 text-amber-100">Над лимита: +{{ $extraCitiesUsed }} града</p>
                            @endif
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <form action="{{ route('admin.businesses.activate', $business) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="min-h-11 w-full rounded-xl bg-emerald-400/10 px-3 py-3 text-xs font-black text-emerald-100 hover:bg-emerald-400/20">Активирай</button>
                            </form>
                            <form action="{{ route('admin.businesses.extend-trial', $business) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="min-h-11 w-full rounded-xl bg-orange-400/10 px-3 py-3 text-xs font-black text-orange-100 hover:bg-orange-400/20">+7 дни</button>
                            </form>
                            <form action="{{ route('admin.businesses.expire', $business) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="min-h-11 w-full rounded-xl bg-amber-400/10 px-3 py-3 text-xs font-black text-amber-100 hover:bg-amber-400/20">Expired</button>
                            </form>
                            <form action="{{ route('admin.businesses.cancel', $business) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="min-h-11 w-full rounded-xl bg-rose-400/10 px-3 py-3 text-xs font-black text-rose-100 hover:bg-rose-400/20">Cancel</button>
                            </form>
                            @if($business->is_verified)
                                <form action="{{ route('admin.businesses.unverify', $business) }}" method="POST" class="col-span-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="min-h-11 w-full rounded-xl bg-white/10 px-3 py-3 text-xs font-black text-white/80 hover:bg-white/15">Махни verified</button>
                                </form>
                            @else
                                <form action="{{ route('admin.businesses.verify', $business) }}" method="POST" class="col-span-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="min-h-11 w-full rounded-xl bg-orange-400/10 px-3 py-3 text-xs font-black text-orange-100 hover:bg-orange-400/20">Провери</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-white/10 bg-slate-950/50 px-4 py-10 text-center text-white/60">
                        Няма изпълнители за избрания филтър.
                    </div>
                @endforelse
            </div>

            <div class="mt-6 hidden overflow-x-auto lg:block">
                <table class="min-w-[1620px] w-full border-separate border-spacing-y-3 text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.18em] text-white/45">
                        <tr>
                            <th class="px-4 py-2">Изпълнител</th>
                            <th class="px-4 py-2">Собственик</th>
                            <th class="px-4 py-2">Град</th>
                            <th class="px-4 py-2">Категория</th>
                            <th class="px-4 py-2">План</th>
                            <th class="px-4 py-2">Градове</th>
                            <th class="px-4 py-2">Лимит</th>
                            <th class="px-4 py-2">Над лимит</th>
                            <th class="px-4 py-2">Subscription</th>
                            <th class="px-4 py-2">Effective</th>
                            <th class="px-4 py-2">Trial дни</th>
                            <th class="px-4 py-2">Край</th>
                            <th class="px-4 py-2">Verified</th>
                            <th class="px-4 py-2">Регистрация</th>
                            <th class="px-4 py-2">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $business)
                            @php
                                $effectiveStatus = $business->effectiveSubscriptionStatus();
                                $extraCitiesUsed = $business->extraCitiesUsed();
                                $adminBillingEndDate = $effectiveStatus === 'trial' ? $business->trial_ends_at : $business->subscription_ends_at;
                            @endphp
                            <tr class="align-top">
                                <td class="rounded-l-3xl border-y border-l border-white/10 bg-slate-950/50 px-4 py-4">
                                    <p class="font-black">{{ $business->business_name ?: $business->name }}</p>
                                    <a href="{{ route('businesses.show', $business) }}" class="mt-1 inline-flex text-xs font-bold text-orange-200 hover:text-white">Виж профил</a>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    <p class="font-semibold">{{ $business->name }}</p>
                                    <p class="mt-1 text-white/50">{{ $business->email }}</p>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4 text-white/70">{{ $business->city ?: 'Няма' }}</td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4 text-white/70">{{ $business->business_category ?: 'Няма' }}</td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $business->planKey() === 'premium' ? 'bg-orange-400/10 text-orange-100' : 'bg-orange-400/10 text-orange-100' }}">
                                        {{ $business->planLabel() }}
                                    </span>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    <p class="font-black text-white">{{ $business->serviceCityCount() }}</p>
                                    <p class="mt-1 text-xs text-white/50">{{ implode(', ', array_slice($business->serviceCities(), 0, 3)) ?: 'Няма' }}</p>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4 text-white/70">
                                    <p class="font-black text-white">{{ $business->cityLimit() }} града</p>
                                    <p class="mt-1 text-xs text-white/50">включени {{ $business->includedCityLimit() }}</p>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    @if($extraCitiesUsed > 0)
                                        <span class="rounded-full bg-amber-400/10 px-3 py-1 text-xs font-black text-amber-100">
                                            +{{ $extraCitiesUsed }} · {{ number_format($business->extraCitiesMonthlyAmount(), 2, ',', ' ') }} €
                                        </span>
                                    @else
                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/50">Няма</span>
                                    @endif
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/80">{{ $business->subscription_status ?: 'trial' }}</span>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $effectiveStatus === 'active' ? 'bg-emerald-400/10 text-emerald-200' : ($effectiveStatus === 'trial' ? 'bg-orange-400/10 text-orange-200' : 'bg-rose-400/10 text-rose-200') }}">
                                        {{ $statusLabels[$effectiveStatus] ?? $effectiveStatus }}
                                    </span>
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4 text-white/70">
                                    {{ $business->trialDaysRemaining() }}
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4 text-white/70">
                                    {{ $adminBillingEndDate ? $adminBillingEndDate->format('d.m.Y') : 'Без дата' }}
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4">
                                    @if($business->is_verified)
                                        <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-200">Verified</span>
                                    @else
                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/50">Unverified</span>
                                    @endif
                                </td>
                                <td class="border-y border-white/10 bg-slate-950/50 px-4 py-4 text-white/70">
                                    {{ $business->created_at?->format('d.m.Y') ?: 'Няма' }}
                                </td>
                                <td class="rounded-r-3xl border-y border-r border-white/10 bg-slate-950/50 px-4 py-4">
                                    <div class="grid min-w-[320px] grid-cols-2 gap-2">
                                        <form action="{{ route('admin.businesses.activate', $business) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full rounded-xl bg-emerald-400/10 px-3 py-2 text-xs font-black text-emerald-100 hover:bg-emerald-400/20">Активирай 30 дни</button>
                                        </form>
                                        <form action="{{ route('admin.businesses.extend-trial', $business) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full rounded-xl bg-orange-400/10 px-3 py-2 text-xs font-black text-orange-100 hover:bg-orange-400/20">+7 дни trial</button>
                                        </form>
                                        <form action="{{ route('admin.businesses.expire', $business) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full rounded-xl bg-amber-400/10 px-3 py-2 text-xs font-black text-amber-100 hover:bg-amber-400/20">Маркирай expired</button>
                                        </form>
                                        <form action="{{ route('admin.businesses.cancel', $business) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full rounded-xl bg-rose-400/10 px-3 py-2 text-xs font-black text-rose-100 hover:bg-rose-400/20">Отмени</button>
                                        </form>
                                        @if($business->is_verified)
                                            <form action="{{ route('admin.businesses.unverify', $business) }}" method="POST" class="col-span-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full rounded-xl bg-white/10 px-3 py-2 text-xs font-black text-white/80 hover:bg-white/15">Махни verified</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.businesses.verify', $business) }}" method="POST" class="col-span-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full rounded-xl bg-orange-400/10 px-3 py-2 text-xs font-black text-orange-100 hover:bg-orange-400/20">Провери</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="rounded-3xl border border-white/10 bg-slate-950/50 px-4 py-10 text-center text-white/60">
                                    Няма изпълнители за избрания филтър.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    @include('partials.mobile-bottom-nav')
</body>
</html>

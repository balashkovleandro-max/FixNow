<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оферти по заявка | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    @php
        $statusLabels = [
            'new' => 'Нова',
            'open' => 'Отворена',
            'contacted' => 'Свързана',
            'in_progress' => 'В процес',
            'completed' => 'Завършена',
            'closed' => 'Затворена',
            'cancelled' => 'Отказана',
        ];

        $urgencyLabels = [
            'urgent' => 'Спешно',
            'this_week' => 'Тази седмица',
            'this_month' => 'До месец',
            'no_deadline' => 'Няма конкретен срок',
            'normal' => 'Нормална',
        ];

        $offerStatusLabels = [
            'sent' => 'Изпратена',
            'viewed' => 'Прегледана',
            'accepted' => 'Приета',
            'rejected' => 'Отказана',
            'not_selected' => 'Не е избрана',
        ];

        $offers = $serviceRequest->offers ?? collect();
        $offerComparisons = $offerComparisons ?? collect();
        $selectedOffer = $serviceRequest->selectedOffer ?: $offers->firstWhere('status', 'accepted');
        $photos = $serviceRequest->photos ?? collect();
        $mainPhoto = $photos->first()?->path ?: $serviceRequest->image;
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_8%,rgba(251,146,60,0.18),transparent_30%),radial-gradient(circle_at_86%_12%,rgba(245,158,11,0.2),transparent_34%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-5 text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-3xl border border-rose-300/20 bg-rose-400/10 p-5 text-rose-100">
                <p class="font-black">Моля, проверете избора.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr] lg:items-start">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-8">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border border-orange-300/25 bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">
                        {{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}
                    </span>
                    <span class="rounded-full border border-orange-300/20 bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">
                        {{ $urgencyLabels[$serviceRequest->urgency] ?? $serviceRequest->urgency }}
                    </span>
                    @if($selectedOffer)
                        <span class="rounded-full border border-emerald-300/25 bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">
                            Избран бизнес
                        </span>
                    @endif
                </div>

                <p class="mt-5 text-sm font-black uppercase tracking-[0.22em] text-orange-300/70">Вашата заявка</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">
                    {{ $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка за услуга' }}
                </h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-white/68 sm:text-base">
                    {{ $serviceRequest->description }}
                </p>

                <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Категория</p>
                        <p class="mt-2 font-black">{{ $serviceRequest->category ?: 'Не е посочена' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Град</p>
                        <p class="mt-2 font-black">{{ $serviceRequest->city }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Бюджет</p>
                        <p class="mt-2 font-black">{{ $serviceRequest->budget ?: 'Не е посочен' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Създадена</p>
                        <p class="mt-2 font-black">{{ $serviceRequest->created_at?->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <aside class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-6">
                @if($mainPhoto)
                    <img src="{{ asset('storage/'.$mainPhoto) }}" alt="Снимка към заявката" loading="lazy" class="h-56 w-full rounded-3xl object-cover">
                @else
                    <div class="flex h-56 w-full items-center justify-center rounded-3xl border border-white/10 bg-slate-950/45 text-center">
                        <div>
                            <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-xl font-black">B</p>
                            <p class="mt-3 text-sm font-bold text-white/65">Няма качени снимки към заявката</p>
                        </div>
                    </div>
                @endif

                @if($photos->count() > 1)
                    <div class="mt-3 grid grid-cols-3 gap-3">
                        @foreach($photos->skip(1)->take(3) as $photo)
                            <img src="{{ asset('storage/'.$photo->path) }}" alt="Допълнителна снимка" loading="lazy" class="h-20 w-full rounded-2xl object-cover">
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 rounded-3xl border border-orange-300/15 bg-orange-400/10 p-4">
                    <p class="font-black text-orange-100">Следете офертите от този линк</p>
                    <p class="mt-2 text-sm leading-6 text-white/60">Не е нужен вход. Линкът е защитен с уникален token към тази заявка.</p>
                </div>
            </aside>
        </section>

        @if($offerComparisons->isNotEmpty())
            <section class="mt-8 rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-orange-300/70">Сравнение на оферти</p>
                        <h2 class="mt-2 text-2xl font-black sm:text-3xl">Изберете по цена, срок и доверие</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-white/60">BON подрежда ключовите сигнали в един преглед, за да вземете спокойно решение.</p>
                    </div>
                    <span class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white/75">{{ $offerComparisons->count() }} предложения</span>
                </div>

                <div class="mt-6 hidden overflow-hidden rounded-3xl border border-white/10 lg:block">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-950/55 text-xs uppercase tracking-[0.18em] text-white/45">
                            <tr>
                                <th class="px-5 py-4">Изпълнител</th>
                                <th class="px-5 py-4">Цена</th>
                                <th class="px-5 py-4">Срок</th>
                                <th class="px-5 py-4">Рейтинг</th>
                                <th class="px-5 py-4">Trust</th>
                                <th class="px-5 py-4">Проекти</th>
                                <th class="px-5 py-4 text-right">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-white/[0.03]">
                            @foreach($offerComparisons as $comparison)
                                @php
                                    $comparisonOffer = $comparison['offer'];
                                    $comparisonBusiness = $comparison['business'];
                                    $comparisonSelected = $selectedOffer && (int) $selectedOffer->id === (int) $comparisonOffer->id;
                                    $canSelectComparison = !$selectedOffer && in_array($comparisonOffer->status, ['sent', 'viewed'], true);
                                @endphp
                                <tr class="{{ $comparisonSelected ? 'bg-emerald-400/10' : '' }}">
                                    <td class="px-5 py-4">
                                        <div class="font-black text-white">{{ $comparison['name'] }}</div>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @foreach(array_slice($comparison['badges'] ?? [], 0, 2) as $badge)
                                                <span class="rounded-full bg-orange-400/10 px-2 py-0.5 text-[11px] font-black text-orange-100">{{ $badge }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-white/75">{{ $comparison['price'] ?: 'Не е посочена' }}</td>
                                    <td class="px-5 py-4 text-white/75">{{ $comparison['timeframe'] ?: 'Не е посочен' }}</td>
                                    <td class="px-5 py-4 text-white/75">{{ $comparison['rating'] ? number_format($comparison['rating'], 1) : '—' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $comparison['trust_score'] }}/100</span>
                                    </td>
                                    <td class="px-5 py-4 text-white/75">{{ $comparison['completed_projects'] }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($comparisonBusiness)
                                                <a href="{{ route('businesses.show', $comparisonBusiness) }}" class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-black text-white hover:bg-white/15">Виж профил</a>
                                            @endif

                                            @if($comparisonSelected)
                                                <span class="rounded-2xl border border-emerald-300/25 bg-emerald-400/10 px-4 py-2 text-xs font-black text-emerald-100">Избран</span>
                                            @elseif($canSelectComparison)
                                                <form action="{{ route('service-requests.offers.accept', ['serviceRequest' => $serviceRequest->public_token, 'offer' => $comparisonOffer]) }}" method="POST">
                                                    @csrf
                                                    <button class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-4 py-2 text-xs font-black text-white shadow-lg shadow-orange-950/30">Избери</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 grid gap-4 lg:hidden">
                    @foreach($offerComparisons as $comparison)
                        @php
                            $comparisonOffer = $comparison['offer'];
                            $comparisonBusiness = $comparison['business'];
                            $comparisonSelected = $selectedOffer && (int) $selectedOffer->id === (int) $comparisonOffer->id;
                            $canSelectComparison = !$selectedOffer && in_array($comparisonOffer->status, ['sent', 'viewed'], true);
                        @endphp
                        <article class="rounded-3xl border {{ $comparisonSelected ? 'border-emerald-300/25 bg-emerald-400/10' : 'border-white/10 bg-slate-950/45' }} p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-black">{{ $comparison['name'] }}</h3>
                                    <p class="mt-1 text-xs font-bold text-white/45">Trust Score {{ $comparison['trust_score'] }}/100</p>
                                </div>
                                @if($comparisonSelected)
                                    <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100">Избран</span>
                                @endif
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                                <p class="rounded-2xl bg-white/5 p-3"><span class="block text-xs text-white/40">Цена</span><strong>{{ $comparison['price'] ?: '—' }}</strong></p>
                                <p class="rounded-2xl bg-white/5 p-3"><span class="block text-xs text-white/40">Срок</span><strong>{{ $comparison['timeframe'] ?: '—' }}</strong></p>
                                <p class="rounded-2xl bg-white/5 p-3"><span class="block text-xs text-white/40">Рейтинг</span><strong>{{ $comparison['rating'] ? number_format($comparison['rating'], 1) : '—' }}</strong></p>
                                <p class="rounded-2xl bg-white/5 p-3"><span class="block text-xs text-white/40">Проекти</span><strong>{{ $comparison['completed_projects'] }}</strong></p>
                            </div>

                            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                @if($comparisonBusiness)
                                    <a href="{{ route('businesses.show', $comparisonBusiness) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-4 text-sm font-black text-white">Виж профил</a>
                                @endif
                                @if($canSelectComparison)
                                    <form action="{{ route('service-requests.offers.accept', ['serviceRequest' => $serviceRequest->public_token, 'offer' => $comparisonOffer]) }}" method="POST">
                                        @csrf
                                        <button class="min-h-11 w-full rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-4 text-sm font-black text-white">Избери</button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black sm:text-3xl">Получени оферти</h2>
                    <p class="mt-2 text-sm text-white/60">Прегледайте предложенията и изберете бизнес, когато сте готови.</p>
                </div>
                <span class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white/80">{{ $offers->count() }} оферти</span>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse($offers as $offer)
                    @php
                        $executor = $offer->business;
                        $isSelected = $selectedOffer && (int) $selectedOffer->id === (int) $offer->id;
                        $executorName = $executor?->business_name ?: $executor?->name ?: 'Бизнес във BON';
                    @endphp
                    <article class="rounded-[28px] border {{ $isSelected ? 'border-emerald-300/30 bg-emerald-400/10' : 'border-white/10 bg-white/10' }} p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex flex-wrap items-center gap-2">
                            @if($isSelected)
                                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100">Избран бизнес</span>
                            @else
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $offerStatusLabels[$offer->status] ?? $offer->status }}</span>
                            @endif
                            @if($executor && method_exists($executor, 'isPremium') && $executor->isPremium())
                                <span class="rounded-full bg-orange-400/15 px-3 py-1 text-xs font-black text-orange-100">Premium</span>
                            @endif
                            @if($executor?->is_verified)
                                <span class="rounded-full bg-orange-400/15 px-3 py-1 text-xs font-black text-orange-100">Потвърден</span>
                            @endif
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/55">{{ $offer->created_at?->format('d.m.Y H:i') }}</span>
                        </div>

                        <h3 class="mt-4 text-2xl font-black">{{ $executorName }}</h3>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Ориентировъчна цена</p>
                                <p class="mt-2 text-lg font-black">{{ $offer->price_estimate }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Срок</p>
                                <p class="mt-2 text-lg font-black">{{ $offer->timeframe }}</p>
                            </div>
                        </div>

                        <p class="mt-4 rounded-2xl border border-white/10 bg-slate-950/45 p-4 text-sm leading-6 text-white/70">
                            {{ $offer->message }}
                        </p>

                        <div class="mt-4 grid gap-2 text-sm text-white/70 sm:grid-cols-2">
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Телефон: <strong class="text-white">{{ $offer->phone ?: 'Не е посочен' }}</strong></p>
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Имейл: <strong class="text-white">{{ $offer->email ?: 'Не е посочен' }}</strong></p>
                        </div>

                        <div class="mt-5">
                            @if($isSelected)
                                <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-5 py-4 text-sm font-bold text-emerald-100">
                                    Тази оферта е приета. Можете да се свържете директно с бизнеся.
                                </div>
                            @elseif($selectedOffer)
                                <div class="rounded-2xl border border-white/10 bg-white/10 px-5 py-4 text-sm font-bold text-white/60">
                                    Вече е избран друг бизнес.
                                </div>
                            @elseif(in_array($offer->status, ['sent', 'viewed'], true))
                                <form action="{{ route('service-requests.offers.accept', ['serviceRequest' => $serviceRequest->public_token, 'offer' => $offer]) }}" method="POST">
                                    @csrf
                                    <button data-track="offer_accept" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-5 py-3 font-black text-white shadow-lg shadow-orange-950/30">
                                        Избери бизнес
                                    </button>
                                </form>
                            @else
                                <div class="rounded-2xl border border-white/10 bg-white/10 px-5 py-4 text-sm font-bold text-white/60">
                                    Офертата вече не е активна.
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-[32px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl lg:col-span-2">
                        <p class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 text-2xl font-black">B</p>
                        <h3 class="mt-5 text-2xl font-black">Все още няма получени оферти</h3>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-white/60">
                            Ще ви уведомим, когато бизнеси изпратят предложения. Запазете този линк, за да проверявате офертите по заявката.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    @include('partials.mobile-bottom-nav')
</body>
</html>

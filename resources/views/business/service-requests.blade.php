<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки и оферти | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    @php
        $statusLabels = [
            'new' => 'Нова',
            'open' => 'Отворена',
            'contacted' => 'Свързан',
            'completed' => 'Завършена',
            'cancelled' => 'Отказана',
            'closed' => 'Затворена',
            'in_progress' => 'В процес',
        ];

        $urgencyLabels = [
            'urgent' => 'Спешно',
            'this_week' => 'Тази седмица',
            'this_month' => 'До месец',
            'no_deadline' => 'Без конкретен срок',
            'normal' => 'Нормална',
        ];

        $statusClasses = [
            'new' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'open' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'contacted' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'completed' => 'border-emerald-300/25 bg-emerald-400/10 text-emerald-100',
            'cancelled' => 'border-rose-300/25 bg-rose-400/10 text-rose-100',
            'closed' => 'border-emerald-300/25 bg-emerald-400/10 text-emerald-100',
            'in_progress' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
        ];

        $offerStatusLabels = [
            'sent' => 'Изпратена',
            'viewed' => 'Прегледана',
            'accepted' => 'Приета',
            'rejected' => 'Отказана',
            'not_selected' => 'Не е избрана',
        ];

        $hasRequestBasedCategories = $hasRequestBasedCategories ?? false;
        $availableServiceRequests = $availableServiceRequests ?? collect();
        $sentOffers = $sentOffers ?? collect();
        $acceptedOffers = $acceptedOffers ?? collect();
        $offerPoints = $offerPoints ?? [
            'balance' => method_exists($business, 'offerPointsBalance') ? $business->offerPointsBalance() : 0,
            'remaining_offers' => method_exists($business, 'remainingOfferCount') ? $business->remainingOfferCount() : 0,
            'cost' => 3,
            'included' => method_exists($business, 'includedMonthlyOfferPoints') ? $business->includedMonthlyOfferPoints() : 0,
        ];
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_10%,rgba(251,146,60,0.16),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_32%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-300 via-orange-500 to-orange-600 font-black">F</span>
                <span class="text-xl font-black">FixNow.bg</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('businesses.show', $business) }}" class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white hover:bg-white/15">Публичен профил</a>
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100">Панел</a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-5 text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-3xl border border-rose-300/20 bg-rose-400/10 p-5 text-rose-100">
                <p class="font-black">Моля, проверете формата.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-8">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Панел на изпълнител</p>
            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black sm:text-5xl">Управлявайте заявки и оферти</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-white/65">
                        Директните запитвания към профила ви са отделени от заявките за оферти. Ако предлагате request-based услуги, тук ще виждате релевантни заявки по град и категория.
                    </p>
                </div>
                <a href="{{ route('request.service') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-950/30">
                    Виж публичната форма
                </a>
            </div>
        </section>

        <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <p class="text-sm font-bold text-white/55">Оставащи точки</p>
                <p class="mt-2 text-4xl font-black">{{ $offerPoints['balance'] }}</p>
                <p class="mt-2 text-sm text-white/60">Можете да изпратите приблизително още {{ $offerPoints['remaining_offers'] }} оферти.</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <p class="text-sm font-bold text-white/55">Цена на оферта</p>
                <p class="mt-2 text-4xl font-black">{{ $offerPoints['cost'] }}</p>
                <p class="mt-2 text-sm text-white/60">1 изпратена оферта = 3 точки.</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <p class="text-sm font-bold text-white/55">Нови релевантни заявки</p>
                <p class="mt-2 text-4xl font-black">{{ $availableServiceRequests->count() }}</p>
                <p class="mt-2 text-sm text-white/60">Само заявки, които съвпадат с вашите градове и категории.</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                <p class="text-sm font-bold text-white/55">Изпратени оферти</p>
                <p class="mt-2 text-4xl font-black">{{ $sentOffers->count() }}</p>
                <p class="mt-2 text-sm text-white/60">Вашите последни оферти към клиентски заявки.</p>
            </div>
        </section>

        <section class="mt-6 rounded-[28px] border border-orange-300/15 bg-orange-400/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-black">Допълнителни точки</h2>
                    <p class="mt-2 text-sm leading-6 text-white/60">Допълнителните пакети точки ще се активират поетапно. Ако имате нужда от повече оферти преди това, свържете се с екипа на FixNow.</p>
                </div>
                <div class="grid gap-2 sm:grid-cols-3">
                    <button disabled class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-black text-white/45">30 точки - 4.99 €</button>
                    <button disabled class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-black text-white/45">75 точки - 9.99 €</button>
                    <button disabled class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-black text-white/45">150 точки - 17.99 €</button>
                </div>
            </div>
        </section>

        @if($hasRequestBasedCategories)
            <section class="mt-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black">Нови заявки за оферти</h2>
                        <p class="mt-2 text-sm text-white/60">Показват се само заявки, които съвпадат с вашите категории/услуги и обслужвани градове.</p>
                    </div>
                    <span class="rounded-2xl border border-orange-300/20 bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100">{{ $availableServiceRequests->count() }} подходящи</span>
                </div>

                <div class="mt-5 grid gap-4 lg:grid-cols-2">
                    @forelse($availableServiceRequests as $serviceRequest)
                        @php
                            $photosCount = $serviceRequest->relationLoaded('photos') ? $serviceRequest->photos->count() : (filled($serviceRequest->image) ? 1 : 0);
                        @endphp
                        <article class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $serviceRequest->category }}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/70">{{ $serviceRequest->city }}</span>
                                <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-bold text-orange-100">{{ $urgencyLabels[$serviceRequest->urgency] ?? $serviceRequest->urgency }}</span>
                            </div>
                            <h3 class="mt-4 text-xl font-black">{{ $serviceRequest->service ?: 'Заявка за услуга' }}</h3>
                            <p class="mt-3 line-clamp-4 text-sm leading-6 text-white/65">{{ $serviceRequest->description }}</p>
                            <div class="mt-4 grid gap-2 text-sm text-white/70 sm:grid-cols-3">
                                <span class="rounded-2xl bg-slate-950/45 px-4 py-3">Бюджет: <strong class="text-white">{{ $serviceRequest->budget ?: 'не е посочен' }}</strong></span>
                                <span class="rounded-2xl bg-slate-950/45 px-4 py-3">Снимки: <strong class="text-white">{{ $photosCount }}</strong></span>
                                <span class="rounded-2xl bg-slate-950/45 px-4 py-3">Дата: <strong class="text-white">{{ $serviceRequest->created_at?->format('d.m.Y') }}</strong></span>
                            </div>

                            <form action="{{ route('business.service-requests.offers.store', $serviceRequest) }}" method="POST" class="mt-5 grid gap-3 rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                                @csrf
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="block">
                                        <span class="text-xs font-black uppercase tracking-[0.18em] text-white/45">Ориентировъчна цена</span>
                                        <input name="price_estimate" value="{{ old('price_estimate') }}" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 text-white outline-none focus:border-orange-300/40" placeholder="Напр. от 120 лв.">
                                    </label>
                                    <label class="block">
                                        <span class="text-xs font-black uppercase tracking-[0.18em] text-white/45">Срок</span>
                                        <input name="timeframe" value="{{ old('timeframe') }}" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 text-white outline-none focus:border-orange-300/40" placeholder="Напр. до 3 дни">
                                    </label>
                                </div>
                                <label class="block">
                                    <span class="text-xs font-black uppercase tracking-[0.18em] text-white/45">Съобщение към клиента</span>
                                    <textarea name="message" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white outline-none focus:border-orange-300/40" placeholder="Опишете как можете да помогнете, какво включва офертата и кога можете да започнете.">{{ old('message') }}</textarea>
                                </label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="block">
                                        <span class="text-xs font-black uppercase tracking-[0.18em] text-white/45">Телефон</span>
                                        <input name="phone" value="{{ old('phone', $business->phone) }}" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 text-white outline-none focus:border-orange-300/40">
                                    </label>
                                    <label class="block">
                                        <span class="text-xs font-black uppercase tracking-[0.18em] text-white/45">Имейл по желание</span>
                                        <input name="email" value="{{ old('email', $business->email) }}" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 text-white outline-none focus:border-orange-300/40">
                                    </label>
                                </div>
                                <button data-track="offer_submit" class="min-h-12 rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 font-black text-white shadow-lg shadow-orange-950/30">
                                    Изпрати оферта - 3 точки
                                </button>
                            </form>
                        </article>
                    @empty
                        <div class="rounded-[28px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl lg:col-span-2">
                        <h3 class="text-2xl font-black">Няма нови заявки във вашите категории и градове</h3>
                            <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Когато клиент пусне заявка във ваш град и категория, тя ще се появи тук и ще можете да изпратите оферта.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @else
            <section class="mt-8 rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                <h2 class="text-2xl font-black">Вашият профил е в directory режим</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-white/60">Категориите ви са основно за директно откриване и контакт. Попълнете request-based категории и градове в профила си, ако искате да получавате подходящи заявки за оферти.</p>
            </section>
        @endif

        <section class="mt-8">
            <h2 class="text-2xl font-black">Приети оферти / активни поръчки</h2>
            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse($acceptedOffers as $offer)
                    <article class="rounded-[28px] border border-emerald-300/20 bg-emerald-400/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100">Избран изпълнител</span>
                            <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100">Активна поръчка</span>
                            <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">Вашата оферта беше приета</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/60">{{ $offer->updated_at?->format('d.m.Y H:i') }}</span>
                        </div>
                        <h3 class="mt-4 text-xl font-black">{{ $offer->serviceRequest?->category ?: 'Заявка' }} · {{ $offer->serviceRequest?->city }}</h3>
                        <p class="mt-2 text-sm leading-6 text-white/70">Клиентът избра вас. Свържете се с него възможно най-скоро, за да уточните детайлите.</p>
                        <div class="mt-4 grid gap-2 text-sm text-white/70 sm:grid-cols-2">
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Клиент: <strong class="text-white">{{ $offer->serviceRequest?->name }}</strong></p>
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Телефон: <strong class="text-white">{{ $offer->serviceRequest?->phone }}</strong></p>
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Оферта: <strong class="text-white">{{ $offer->price_estimate }}</strong></p>
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Срок: <strong class="text-white">{{ $offer->timeframe }}</strong></p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[28px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl lg:col-span-2">
                        <h3 class="text-2xl font-black">Все още няма приети оферти</h3>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Когато клиент избере ваша оферта, тя ще се появи тук като активна поръчка.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mt-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black">Директни запитвания към профила</h2>
                    <p class="mt-2 text-sm text-white/60">Това са заявки, изпратени директно от публичния ви профил на изпълнител или назначени от admin.</p>
                </div>
                <span class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white/80">{{ $serviceRequests->total() }} запитвания</span>
            </div>

            <div class="mt-5 grid gap-4">
                @forelse($serviceRequests as $serviceRequest)
                    <article class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusClasses[$serviceRequest->status] ?? 'border-white/10 bg-white/10 text-white/70' }}">
                                        {{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}
                                    </span>
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/60">
                                        {{ $serviceRequest->created_at?->format('d.m.Y H:i') }}
                                    </span>
                                    @if($serviceRequest->source)
                                        <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-bold text-orange-100">
                                            {{ $serviceRequest->source === 'business_profile' ? 'От профил на изпълнител' : 'Заяви оферта' }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-4 text-2xl font-black">{{ $serviceRequest->name }}</h3>
                                <div class="mt-3 grid gap-2 text-sm text-white/65 sm:grid-cols-2 lg:grid-cols-4">
                                    <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Телефон: <strong class="text-white">{{ $serviceRequest->phone }}</strong></p>
                                    <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Град: <strong class="text-white">{{ $serviceRequest->city }}</strong></p>
                                    <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Категория: <strong class="text-white">{{ $serviceRequest->category ?: 'Не е посочена' }}</strong></p>
                                    <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Имейл: <strong class="text-white">{{ $serviceRequest->email ?: 'Няма' }}</strong></p>
                                </div>
                                <p class="mt-4 rounded-2xl border border-white/10 bg-slate-950/45 p-4 text-sm leading-6 text-white/70">
                                    {{ $serviceRequest->description }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <form action="{{ route('business.service-requests.contacted', $serviceRequest) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="min-h-11 w-full rounded-2xl border border-orange-300/20 bg-orange-400/10 px-4 py-3 text-sm font-black text-orange-100 hover:bg-orange-400/15">Маркирай като свързан</button>
                            </form>
                            <form action="{{ route('business.service-requests.completed', $serviceRequest) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="min-h-11 w-full rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-400/15">Маркирай като завършена</button>
                            </form>
                            <form action="{{ route('business.service-requests.cancelled', $serviceRequest) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="min-h-11 w-full rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm font-black text-rose-100 hover:bg-rose-400/15">Откажи</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[28px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl">
                        <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 text-xl font-black">F</p>
                        <h3 class="mt-4 text-2xl font-black">Все още няма директни запитвания</h3>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Когато клиент изпрати запитване към вашия профил, то ще се появи тук с телефон, град и описание.</p>
                        <a href="{{ route('businesses.show', $business) }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white">Виж публичния профил</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $serviceRequests->links() }}
            </div>
        </section>

        <section class="mt-8">
            <h2 class="text-2xl font-black">Изпратени оферти</h2>
            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse($sentOffers as $offer)
                    <article class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">{{ $offerStatusLabels[$offer->status] ?? $offer->status }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/60">{{ $offer->created_at?->format('d.m.Y H:i') }}</span>
                            <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-bold text-orange-100">-{{ $offer->points_spent }} точки</span>
                        </div>
                        <h3 class="mt-4 text-xl font-black">{{ $offer->serviceRequest?->category ?: 'Заявка' }} · {{ $offer->serviceRequest?->city }}</h3>
                        <p class="mt-2 text-sm text-white/60">Цена: <strong class="text-white">{{ $offer->price_estimate }}</strong> · Срок: <strong class="text-white">{{ $offer->timeframe }}</strong></p>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-white/65">{{ $offer->message }}</p>
                        @if($offer->status === 'not_selected')
                            <p class="mt-4 rounded-2xl border border-amber-300/20 bg-amber-400/10 px-4 py-3 text-sm font-bold text-amber-100">
                                Заявката е затворена. Клиентът избра друг изпълнител.
                            </p>
                        @elseif($offer->status === 'accepted')
                            <p class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm font-bold text-emerald-100">
                                Избран изпълнител: вашата оферта е приета.
                            </p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-[28px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl lg:col-span-2">
                        <h3 class="text-2xl font-black">Още няма изпратени оферти</h3>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Когато изпратите оферта към релевантна заявка, тя ще се появи тук за проследяване.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    @include('partials.mobile-bottom-nav')
</body>
</html>

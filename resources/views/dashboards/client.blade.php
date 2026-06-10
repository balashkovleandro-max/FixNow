<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Клиентски панел | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $requests = $customerServiceRequests ?? collect();
    $stats = $customerRequestStats ?? [
        'total' => $requests->count(),
        'open' => 0,
        'completed' => 0,
        'offers' => 0,
    ];

    $statusLabels = [
        'new' => 'Нова',
        'open' => 'Отворена',
        'contacted' => 'Свързан клиент',
        'closed' => 'Затворена',
        'completed' => 'Завършена',
        'cancelled' => 'Отказана',
        'in_progress' => 'В процес',
    ];

    $statusClasses = [
        'new' => 'border-orange-300/30 bg-orange-300/10 text-orange-100',
        'open' => 'border-orange-300/30 bg-orange-300/10 text-orange-100',
        'contacted' => 'border-emerald-300/30 bg-emerald-300/10 text-emerald-100',
        'closed' => 'border-slate-300/20 bg-white/10 text-white/70',
        'completed' => 'border-emerald-300/30 bg-emerald-300/10 text-emerald-100',
        'cancelled' => 'border-rose-300/30 bg-rose-300/10 text-rose-100',
        'in_progress' => 'border-orange-300/30 bg-orange-300/10 text-orange-100',
    ];

    $offerStatusLabels = [
        'sent' => 'Изпратена',
        'viewed' => 'Прегледана',
        'accepted' => 'Избрана',
        'rejected' => 'Отказана',
        'not_selected' => 'Не е избрана',
    ];
@endphp
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <header class="mb-6 rounded-[28px] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-300 via-orange-500 to-orange-600 font-black">F</div>
                    <div>
                        <p class="text-xl font-black">BON</p>
                        <p class="text-xs text-white/50">Клиентски панел</p>
                    </div>
                </a>

                <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-white/70">
                    <a href="{{ url('/') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Начало</a>
                    <a href="{{ url('/categories') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Категории</a>
                    <a href="{{ route('services.index') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Услуги</a>
                        <a href="{{ route('businesses.index') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Бизнеси</a>
                    <a href="{{ route('request.service') }}" class="rounded-2xl px-4 py-2 hover:bg-white/10 hover:text-white">Пусни заявка</a>
                    <a href="{{ route('dashboard') }}" class="rounded-2xl bg-orange-300/10 px-4 py-2 text-orange-100">Моето табло</a>
                </nav>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-left text-sm font-bold text-white/70 hover:bg-white/10 hover:text-white xl:w-auto">Изход</button>
                </form>
            </div>
        </header>

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Клиентски панел</p>
                    <h1 class="mt-3 text-3xl font-black sm:text-5xl">Моите заявки и оферти</h1>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-white/65">
                        Следете изпратените заявки, получените оферти и избрания бизнес от едно леко клиентско табло. Клиентският акаунт е безплатен и няма абонаментни функции.
                    </p>
                </div>
                <a href="{{ route('request.service') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-6 py-3 text-center font-black text-white shadow-lg shadow-orange-950/40">
                    Пусни нова заявка
                </a>
            </div>
        </section>

        <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['label' => 'Всички заявки', 'value' => $stats['total'] ?? 0, 'note' => 'история на заявките'],
                ['label' => 'Активни заявки', 'value' => $stats['open'] ?? 0, 'note' => 'чакат действие'],
                ['label' => 'Получени оферти', 'value' => $stats['offers'] ?? 0, 'note' => 'към вашите заявки'],
                ['label' => 'Завършени', 'value' => $stats['completed'] ?? 0, 'note' => 'приключени заявки'],
            ] as $stat)
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm text-white/60">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-4xl font-black">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-orange-200">{{ $stat['note'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black">Моите заявки</h2>
                        <p class="mt-1 text-sm text-white/55">Виждате само заявки, свързани с вашия клиентски акаунт.</p>
                    </div>
                    <a href="{{ route('request.service') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-orange-300/30 bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100 hover:bg-orange-300/15">
                        Нова заявка
                    </a>
                </div>

                @if($requests->isEmpty())
                    <div class="mt-6 rounded-3xl border border-dashed border-white/15 bg-slate-950/50 p-6 text-center">
                        <p class="text-xl font-black">Все още нямате заявки</p>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">
                            Когато пуснете заявка за услуга, тя ще се появи тук заедно с получените оферти и избрания бизнес.
                        </p>
                        <a href="{{ route('request.service') }}" class="mt-5 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-6 py-3 font-black text-white">
                            Пусни първа заявка
                        </a>
                    </div>
                @else
                    <div class="mt-6 grid gap-4">
                        @foreach($requests as $serviceRequest)
                            @php
                                $offers = $serviceRequest->relationLoaded('offers') ? $serviceRequest->offers : collect();
                                $selectedOffer = $serviceRequest->selectedOffer ?: $offers->firstWhere('status', 'accepted');
                                $selectedExecutor = $selectedOffer?->business ?: $serviceRequest->assignedBusiness;
                                $photos = $serviceRequest->relationLoaded('photos') ? $serviceRequest->photos : collect();
                                $status = $serviceRequest->status ?: 'new';
                            @endphp
                            <article class="rounded-3xl border border-white/10 bg-slate-950/55 p-5 shadow-lg shadow-black/20">
                                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusClasses[$status] ?? 'border-white/10 bg-white/10 text-white/70' }}">
                                                {{ $statusLabels[$status] ?? $status }}
                                            </span>
                                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold text-white/55">
                                                {{ $serviceRequest->created_at?->format('d.m.Y') }}
                                            </span>
                                            @if($serviceRequest->closed_at)
                                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold text-white/55">
                                                    Приключена: {{ $serviceRequest->closed_at?->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="mt-4 text-xl font-black">
                                            {{ $serviceRequest->category ?: $serviceRequest->service ?: 'Заявка за услуга' }}
                                        </h3>
                                        <p class="mt-1 text-sm font-semibold text-orange-100">{{ $serviceRequest->city ?: 'Без посочен град' }}</p>
                                        <p class="mt-3 line-clamp-4 text-sm leading-6 text-white/65">{{ $serviceRequest->description }}</p>

                                        <div class="mt-4 grid gap-2 text-sm text-white/60 sm:grid-cols-2">
                                            <p><span class="text-white/40">Срок:</span> {{ $serviceRequest->urgency ?: 'Не е посочен' }}</p>
                                            <p><span class="text-white/40">Бюджет:</span> {{ $serviceRequest->budget ?: 'Не е посочен' }}</p>
                                        </div>

                                        @if($photos->isNotEmpty())
                                            <div class="mt-4 flex gap-2 overflow-x-auto pb-1">
                                                @foreach($photos as $photo)
                                                    <img src="{{ asset('storage/'.$photo->path) }}" alt="Снимка към заявката" loading="lazy" class="h-20 w-24 flex-none rounded-2xl object-cover">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 md:w-64">
                                        <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Избран бизнес</p>
                                        @if($selectedExecutor)
                                            <p class="mt-3 font-black">{{ $selectedExecutor->business_name ?: $selectedExecutor->name }}</p>
                                            <p class="mt-1 text-sm text-white/55">{{ $selectedOffer ? 'Приета оферта' : 'Назначен бизнес' }}</p>
                                            @if($serviceRequest->status === 'in_progress')
                                                <p class="mt-3 rounded-2xl border border-orange-300/25 bg-orange-300/10 px-3 py-2 text-sm font-black text-orange-100">Статус: В процес</p>
                                            @endif
                                        @else
                                            <p class="mt-3 text-sm leading-6 text-white/60">Все още няма избран бизнес.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-5 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="font-black">Получени оферти</h4>
                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/60">{{ $offers->count() }}</span>
                                    </div>

                                    @if($offers->isEmpty())
                                        <p class="mt-3 text-sm leading-6 text-white/55">Все още няма получени оферти към тази заявка.</p>
                                    @else
                                        <div class="mt-4 grid gap-3">
                                            @foreach($offers as $offer)
                                                <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4">
                                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                        <div>
                                                            <p class="font-black">{{ $offer->business?->business_name ?: $offer->business?->name ?: 'Бизнес във BON' }}</p>
                                                            <p class="mt-1 text-sm text-white/55">{{ $offer->message }}</p>
                                                        </div>
                                                        <span class="rounded-full border border-orange-300/25 bg-orange-300/10 px-3 py-1 text-xs font-black text-orange-100">
                                                            {{ $offerStatusLabels[$offer->status] ?? $offer->status }}
                                                        </span>
                                                    </div>
                                                    <div class="mt-3 grid gap-2 text-sm text-white/65 sm:grid-cols-3">
                                                        <p><span class="text-white/40">Цена:</span> {{ $offer->price_estimate }}</p>
                                                        <p><span class="text-white/40">Срок:</span> {{ $offer->timeframe }}</p>
                                                        <p><span class="text-white/40">Телефон:</span> {{ $offer->phone ?: 'Не е посочен' }}</p>
                                                    </div>
                                                    @if(!$selectedOffer && in_array($offer->status, ['sent', 'viewed'], true))
                                                        <form action="{{ route('customer.offers.accept', $offer) }}" method="POST" class="mt-4">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button class="min-h-11 w-full rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-950/30">
                                                                Приеми офертата
                                                            </button>
                                                        </form>
                                                    @elseif($offer->status === 'accepted')
                                                        <p class="mt-4 rounded-2xl border border-emerald-300/25 bg-emerald-300/10 px-4 py-3 text-sm font-black text-emerald-100">
                                                            Приета оферта · Избран бизнес
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">Основни настройки</h2>
                    <div class="mt-5 space-y-3 text-sm text-white/65">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                            <p class="text-white/40">Име</p>
                            <p class="mt-1 font-bold text-white">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                            <p class="text-white/40">Имейл</p>
                            <p class="mt-1 break-words font-bold text-white">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                            <p class="text-white/40">Телефон</p>
                            <p class="mt-1 font-bold text-white">{{ auth()->user()->phone ?: 'Не е добавен' }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                            <p class="text-white/40">Град</p>
                            <p class="mt-1 font-bold text-white">{{ auth()->user()->city ?: 'Не е добавен' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-xs leading-5 text-white/45">Клиентският профил е само за заявки и оферти. Той няма публична бизнесска страница, планове или Stripe абонамент.</p>
                </div>

                <div class="rounded-[32px] border border-orange-300/20 bg-orange-300/10 p-6 shadow-xl shadow-orange-950/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">Как работи</h2>
                    <div class="mt-5 space-y-3 text-sm leading-6 text-white/70">
                        <p>1. Пускате заявка с град, категория и описание.</p>
                        <p>2. Подходящи бизнеси изпращат оферти.</p>
                        <p>3. Виждате историята и избрания бизнес тук.</p>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    @include('partials.mobile-bottom-nav')
</body>
</html>

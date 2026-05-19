<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оферти по заявка | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
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
        $selectedOffer = $serviceRequest->selectedOffer ?: $offers->firstWhere('status', 'accepted');
        $photos = $serviceRequest->photos ?? collect();
        $mainPhoto = $photos->first()?->path ?: $serviceRequest->image;
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_8%,rgba(34,211,238,0.18),transparent_30%),radial-gradient(circle_at_86%_12%,rgba(168,85,247,0.2),transparent_34%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


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
                    <span class="rounded-full border border-cyan-300/25 bg-cyan-400/10 px-3 py-1 text-xs font-black text-cyan-100">
                        {{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}
                    </span>
                    <span class="rounded-full border border-violet-300/20 bg-violet-400/10 px-3 py-1 text-xs font-black text-violet-100">
                        {{ $urgencyLabels[$serviceRequest->urgency] ?? $serviceRequest->urgency }}
                    </span>
                    @if($selectedOffer)
                        <span class="rounded-full border border-emerald-300/25 bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">
                            Избран изпълнител
                        </span>
                    @endif
                </div>

                <p class="mt-5 text-sm font-black uppercase tracking-[0.22em] text-cyan-200/70">Вашата заявка</p>
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
                            <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-violet-600 text-xl font-black">F</p>
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

                <div class="mt-5 rounded-3xl border border-cyan-300/15 bg-cyan-400/10 p-4">
                    <p class="font-black text-cyan-100">Следете офертите от този линк</p>
                    <p class="mt-2 text-sm leading-6 text-white/60">Не е нужен вход. Линкът е защитен с уникален token към тази заявка.</p>
                </div>
            </aside>
        </section>

        <section class="mt-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black sm:text-3xl">Получени оферти</h2>
                    <p class="mt-2 text-sm text-white/60">Сравнете предложенията и изберете изпълнител, когато сте готови.</p>
                </div>
                <span class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white/80">{{ $offers->count() }} оферти</span>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse($offers as $offer)
                    @php
                        $executor = $offer->business;
                        $isSelected = $selectedOffer && (int) $selectedOffer->id === (int) $offer->id;
                        $executorName = $executor?->business_name ?: $executor?->name ?: 'Изпълнител във FixNow';
                    @endphp
                    <article class="rounded-[28px] border {{ $isSelected ? 'border-emerald-300/30 bg-emerald-400/10' : 'border-white/10 bg-white/10' }} p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <div class="flex flex-wrap items-center gap-2">
                            @if($isSelected)
                                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100">Избран изпълнител</span>
                            @else
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $offerStatusLabels[$offer->status] ?? $offer->status }}</span>
                            @endif
                            @if($executor && method_exists($executor, 'isPremium') && $executor->isPremium())
                                <span class="rounded-full bg-violet-400/15 px-3 py-1 text-xs font-black text-violet-100">Premium</span>
                            @endif
                            @if($executor?->is_verified)
                                <span class="rounded-full bg-cyan-400/15 px-3 py-1 text-xs font-black text-cyan-100">Потвърден</span>
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
                                    Тази оферта е приета. Можете да се свържете директно с изпълнителя.
                                </div>
                            @elseif($selectedOffer)
                                <div class="rounded-2xl border border-white/10 bg-white/10 px-5 py-4 text-sm font-bold text-white/60">
                                    Вече е избран друг изпълнител.
                                </div>
                            @elseif(in_array($offer->status, ['sent', 'viewed'], true))
                                <form action="{{ route('service-requests.offers.accept', ['serviceRequest' => $serviceRequest->public_token, 'offer' => $offer]) }}" method="POST">
                                    @csrf
                                    <button class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-5 py-3 font-black text-white shadow-lg shadow-blue-950/30">
                                        Избери изпълнител
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
                        <p class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-cyan-400 via-blue-500 to-violet-600 text-2xl font-black">F</p>
                        <h3 class="mt-5 text-2xl font-black">Все още няма получени оферти</h3>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-white/60">
                            Ще ви уведомим, когато изпълнители изпратят предложения. Запазете този линк, за да проверявате офертите по заявката.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    @include('partials.mobile-bottom-nav')
</body>
</html>

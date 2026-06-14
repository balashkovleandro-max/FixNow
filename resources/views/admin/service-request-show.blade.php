<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детайли за заявка #{{ $serviceRequest->id }} | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    @php
        $title = $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка #'.$serviceRequest->id;
        $selectedExecutor = $serviceRequest->selectedOffer?->business ?: $serviceRequest->assignedBusiness;
        $statusLabels = [
            'new' => 'Open',
            'open' => 'Open',
            'contacted' => 'Open',
            'in_progress' => 'Selected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'closed' => 'Closed',
        ];
        $statusClasses = [
            'new' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'open' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'contacted' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'in_progress' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'completed' => 'border-emerald-300/25 bg-emerald-400/10 text-emerald-100',
            'cancelled' => 'border-rose-300/25 bg-rose-400/10 text-rose-100',
            'closed' => 'border-slate-300/20 bg-white/10 text-white/70',
        ];
        $offerStatusLabels = [
            'sent' => 'pending',
            'viewed' => 'pending',
            'accepted' => 'accepted',
            'rejected' => 'not_selected',
            'not_selected' => 'not_selected',
        ];
        $offerStatusClasses = [
            'sent' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'viewed' => 'border-orange-300/25 bg-orange-400/10 text-orange-100',
            'accepted' => 'border-emerald-300/25 bg-emerald-400/10 text-emerald-100',
            'rejected' => 'border-rose-300/25 bg-rose-400/10 text-rose-100',
            'not_selected' => 'border-white/10 bg-white/10 text-white/60',
        ];
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_8%,rgba(251,146,60,0.16),transparent_30%),radial-gradient(circle_at_82%_18%,rgba(245,158,11,0.18),transparent_32%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('admin.service-requests.index') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 font-black">B</span>
                <span class="text-lg font-black sm:text-xl">Admin заявки</span>
            </a>
            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100">Admin табло</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-5 text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">ID #{{ $serviceRequest->id }}</span>
                <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusClasses[$serviceRequest->status] ?? 'border-white/10 bg-white/10 text-white/70' }}">
                    {{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}
                </span>
                @if($serviceRequest->offers->isNotEmpty())
                    <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">Has offers</span>
                @else
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/55">No offers</span>
                @endif
                @if($serviceRequest->selected_offer_id)
                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">Selected</span>
                @endif
            </div>

            <div class="mt-5 grid gap-6 lg:grid-cols-[1fr_360px]">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Заявка към бизнеси</p>
                    <h1 class="mt-3 text-3xl font-black sm:text-5xl">{{ $title }}</h1>
                    <p class="mt-4 max-w-3xl text-sm leading-6 text-white/65">{{ $serviceRequest->description }}</p>
                </div>
                <aside class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                    <p class="text-sm text-white/50">Избран бизнес</p>
                    <p class="mt-2 text-xl font-black">{{ $selectedExecutor?->business_name ?: $selectedExecutor?->name ?: 'Няма избран бизнес' }}</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-white/45">Оферти</p>
                            <p class="mt-1 text-2xl font-black">{{ $serviceRequest->offers->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-white/45">Снимки</p>
                            <p class="mt-1 text-2xl font-black">{{ $serviceRequest->photos->count() }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
            <section class="grid gap-6">
                <article class="rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-2xl font-black">Данни за заявката</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Клиент</p>
                            <p class="mt-1 font-black">{{ $serviceRequest->name ?: 'Няма име' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Контакт</p>
                            <p class="mt-1 font-black">{{ $serviceRequest->phone ?: 'Няма телефон' }}</p>
                            <p class="text-sm text-white/55">{{ $serviceRequest->email ?: 'Няма имейл' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Град</p>
                            <p class="mt-1 font-black">{{ $serviceRequest->city ?: 'Няма' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Категория</p>
                            <p class="mt-1 font-black">{{ $serviceRequest->category ?: 'Няма' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Бюджет</p>
                            <p class="mt-1 font-black">{{ $serviceRequest->budget ?: 'Не е посочен' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-white/40">Срок / спешност</p>
                            <p class="mt-1 font-black">{{ $serviceRequest->urgency ?: 'normal' }}</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-black">Оферти по заявката</h2>
                            <p class="mt-1 text-sm text-white/55">Следете кой бизнес е изпратил оферта и дали клиентът е избрал някого.</p>
                        </div>
                        <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ $serviceRequest->offers->count() }} оферти</span>
                    </div>

                    <div class="mt-5 grid gap-4">
                        @forelse($serviceRequest->offers as $offer)
                            @php
                                $executor = $offer->business;
                                $isSelected = (int) $serviceRequest->selected_offer_id === (int) $offer->id;
                            @endphp
                            <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border px-3 py-1 text-xs font-black {{ $offerStatusClasses[$offer->status] ?? 'border-white/10 bg-white/10 text-white/70' }}">
                                        {{ $isSelected ? 'accepted' : ($offerStatusLabels[$offer->status] ?? $offer->status) }}
                                    </span>
                                    @if($isSelected)
                                        <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">Избран бизнес</span>
                                    @endif
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/55">{{ $offer->created_at?->format('d.m.Y H:i') }}</span>
                                </div>
                                <h3 class="mt-4 text-xl font-black">{{ $executor?->business_name ?: $executor?->name ?: 'Бизнес' }}</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <p class="rounded-2xl bg-white/10 p-4 text-sm text-white/60">Цена: <strong class="text-white">{{ $offer->price_estimate ?: 'Не е посочена' }}</strong></p>
                                    <p class="rounded-2xl bg-white/10 p-4 text-sm text-white/60">Срок: <strong class="text-white">{{ $offer->timeframe ?: 'Не е посочен' }}</strong></p>
                                    <p class="rounded-2xl bg-white/10 p-4 text-sm text-white/60">Телефон: <strong class="text-white">{{ $offer->phone ?: 'Няма' }}</strong></p>
                                    <p class="rounded-2xl bg-white/10 p-4 text-sm text-white/60">Имейл: <strong class="text-white">{{ $offer->email ?: 'Няма' }}</strong></p>
                                </div>
                                <p class="mt-4 rounded-2xl bg-white/10 p-4 text-sm leading-6 text-white/70">{{ $offer->message ?: 'Няма съобщение.' }}</p>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-white/15 bg-white/5 p-6 text-center">
                                <h3 class="text-xl font-black">Още няма оферти по тази заявка</h3>
                                <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/55">Когато бизнес изпрати оферта, тя ще се появи тук за admin диагностика.</p>
                            </div>
                        @endforelse
                    </div>
                </article>

                <article class="rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-2xl font-black">Timeline</h2>
                    <div class="mt-5 grid gap-4">
                        @foreach($timeline as $event)
                            <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-orange-200/70">{{ $event['date']?->format('d.m.Y H:i') }}</p>
                                <h3 class="mt-2 font-black">{{ $event['label'] }}</h3>
                                <p class="mt-1 text-sm text-white/55">{{ $event['note'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <aside class="grid content-start gap-6">
                <article class="rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">Customer offers URL</h2>
                    @if($publicOffersUrl)
                        <p class="mt-2 break-all rounded-2xl bg-slate-950/55 p-4 text-sm text-orange-100">{{ $publicOffersUrl }}</p>
                        <a href="{{ $publicOffersUrl }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-2xl border border-orange-300/20 bg-orange-300/10 px-5 py-3 text-sm font-black text-orange-100">
                            Отвори customer view
                        </a>
                    @else
                        <p class="mt-2 text-sm leading-6 text-white/55">Тази заявка няма public token и не може да се отвори през customer offers URL.</p>
                    @endif
                </article>

                <article class="rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">Admin действия</h2>
                    <div class="mt-4 grid gap-3">
                        <form method="POST" action="{{ route('admin.service-requests.completed', $serviceRequest) }}">
                            @csrf
                            @method('PATCH')
                            <button class="min-h-11 w-full rounded-2xl bg-emerald-400/15 px-5 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-400/20">
                                Mark as completed
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.service-requests.cancelled', $serviceRequest) }}">
                            @csrf
                            @method('PATCH')
                            <button class="min-h-11 w-full rounded-2xl bg-rose-400/15 px-5 py-3 text-sm font-black text-rose-100 hover:bg-rose-400/20">
                                Mark as cancelled
                            </button>
                        </form>
                    </div>
                    @if($serviceRequest->selected_offer_id)
                        <p class="mt-4 rounded-2xl border border-amber-300/20 bg-amber-300/10 p-4 text-sm leading-6 text-amber-100">
                            Reopen не е активиран за заявки с избрана оферта, за да не се наруши текущият процес.
                        </p>
                    @endif
                </article>

                <article class="rounded-[28px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">Технически статус</h2>
                    <div class="mt-4 grid gap-3 text-sm text-white/60">
                        <p class="rounded-2xl bg-slate-950/45 p-4">assigned_business_id: <strong class="text-white">{{ $serviceRequest->assigned_business_id ?: 'null' }}</strong></p>
                        <p class="rounded-2xl bg-slate-950/45 p-4">selected_offer_id: <strong class="text-white">{{ $serviceRequest->selected_offer_id ?: 'null' }}</strong></p>
                        <p class="rounded-2xl bg-slate-950/45 p-4">accepted_offer_at: <strong class="text-white">{{ $serviceRequest->accepted_offer_at?->format('d.m.Y H:i') ?: 'null' }}</strong></p>
                    </div>
                </article>

                <a href="{{ route('admin.service-requests.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-sm font-black text-white hover:bg-white/15">
                    Назад към всички заявки
                </a>
            </aside>
        </div>
    </main>

    @include('partials.mobile-bottom-nav')
</body>
</html>

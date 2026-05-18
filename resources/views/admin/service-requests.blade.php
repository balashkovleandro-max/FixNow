<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin заявки | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    @php
        $filters = [
            'all' => 'Всички',
            'new' => 'Нови',
            'contacted' => 'Свързани',
            'completed' => 'Завършени',
            'cancelled' => 'Отказани',
        ];
        $statusLabels = [
            'new' => 'Нова',
            'contacted' => 'Свързан',
            'completed' => 'Завършена',
            'cancelled' => 'Отказана',
            'closed' => 'Затворена',
            'in_progress' => 'В процес',
        ];
        $offerStatusLabels = [
            'sent' => 'Изпратена',
            'viewed' => 'Прегледана',
            'accepted' => 'Приета',
            'rejected' => 'Отказана',
            'not_selected' => 'Не е избрана',
        ];
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_10%,rgba(34,211,238,0.16),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(168,85,247,0.18),transparent_32%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black">F</span>
                <span class="text-xl font-black">FixNow Admin</span>
            </a>
            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-cyan-300/10 px-4 py-2 text-sm font-black text-cyan-100">Admin табло</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-8">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">Service requests</p>
            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black sm:text-5xl">Всички клиентски заявки</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-white/65">Admin преглед на заявки от “Заяви оферта” и директни заявки към профили на изпълнители.</p>
                </div>
                <span class="rounded-2xl border border-cyan-300/20 bg-cyan-300/10 px-4 py-3 text-sm font-black text-cyan-100">
                    {{ $serviceRequests->total() }} заявки
                </span>
            </div>

            <div class="mt-6 flex gap-2 overflow-x-auto pb-2">
                @foreach($filters as $value => $label)
                    <a href="{{ $value === 'all' ? route('admin.service-requests.index') : route('admin.service-requests.index', ['status' => $value]) }}" class="shrink-0 rounded-2xl border px-4 py-2 text-sm font-black {{ $status === $value ? 'border-cyan-300/40 bg-cyan-300/10 text-cyan-100' : 'border-white/10 bg-white/5 text-white/70 hover:bg-white/10' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </section>

        <section class="mt-6 grid gap-4">
            @forelse($serviceRequests as $serviceRequest)
                <article class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <div class="grid gap-4 lg:grid-cols-[1fr_260px]">
                        <div>
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-cyan-400/10 px-3 py-1 text-xs font-black text-cyan-100">{{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/60">{{ $serviceRequest->created_at?->format('d.m.Y H:i') }}</span>
                                @if($serviceRequest->source)
                                    <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-bold text-violet-100">{{ $serviceRequest->source === 'business_profile' ? 'Профил на изпълнител' : 'Заяви оферта' }}</span>
                                @endif
                            </div>

                            <h2 class="mt-4 text-2xl font-black">{{ $serviceRequest->name }}</h2>
                            <div class="mt-3 grid gap-2 text-sm text-white/65 sm:grid-cols-2 lg:grid-cols-4">
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Телефон: <strong class="text-white">{{ $serviceRequest->phone }}</strong></p>
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Град: <strong class="text-white">{{ $serviceRequest->city }}</strong></p>
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Категория: <strong class="text-white">{{ $serviceRequest->category ?: 'Няма' }}</strong></p>
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Имейл: <strong class="text-white">{{ $serviceRequest->email ?: 'Няма' }}</strong></p>
                            </div>
                            <p class="mt-4 rounded-2xl border border-white/10 bg-slate-950/45 p-4 text-sm leading-6 text-white/70">{{ $serviceRequest->description }}</p>
                        </div>

                        <aside class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm text-white/50">Изпълнител</p>
                            <p class="mt-2 text-lg font-black">{{ $serviceRequest->assignedBusiness?->business_name ?: $serviceRequest->assignedBusiness?->name ?: 'Не е назначен' }}</p>
                            @if($serviceRequest->assignedBusiness)
                                <a href="{{ route('businesses.show', $serviceRequest->assignedBusiness) }}" class="mt-3 inline-flex text-sm font-bold text-cyan-200 hover:text-white">Виж профил</a>
                            @endif

                            <div class="mt-5 grid gap-2">
                                @forelse($serviceRequest->assignments as $assignment)
                                    <div class="rounded-2xl bg-white/5 px-4 py-3 text-sm">
                                        <p class="font-bold">{{ $assignment->business?->business_name ?: $assignment->business?->name ?: 'Изтрит изпълнител' }}</p>
                                        <p class="mt-1 text-white/50">{{ $assignment->status }}{{ $assignment->contacted_at ? ' · ' . $assignment->contacted_at->format('d.m.Y H:i') : '' }}</p>
                                    </div>
                                @empty
                                    <p class="rounded-2xl bg-white/5 px-4 py-3 text-sm text-white/50">Няма assignment записи.</p>
                                @endforelse
                            </div>

                            <div class="mt-5 grid gap-2">
                                <p class="text-sm font-black text-white/70">Оферти</p>
                                @forelse($serviceRequest->offers as $offer)
                                    <div class="rounded-2xl bg-white/5 px-4 py-3 text-sm">
                                        <p class="font-bold">{{ $offer->business?->business_name ?: $offer->business?->name ?: 'Изтрит изпълнител' }}</p>
                                        <p class="mt-1 text-white/50">{{ $offerStatusLabels[$offer->status] ?? $offer->status }} · {{ $offer->price_estimate }} · {{ $offer->timeframe }}</p>
                                    </div>
                                @empty
                                    <p class="rounded-2xl bg-white/5 px-4 py-3 text-sm text-white/50">Няма изпратени оферти.</p>
                                @endforelse
                            </div>
                        </aside>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-violet-600 text-xl font-black">F</p>
                    <h2 class="mt-4 text-2xl font-black">Няма заявки за този филтър</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Когато клиент изпрати заявка от профил на изпълнител или от “Заяви оферта”, тя ще се появи тук с изпълнител, статус и контактни данни.</p>
                    <a href="{{ route('admin.service-requests.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 py-3 text-sm font-black text-white hover:bg-white/15">Виж всички заявки</a>
                </div>
            @endforelse
        </section>

        <div class="mt-6">
            {{ $serviceRequests->links() }}
        </div>
    </main>

    @include('partials.mobile-bottom-nav')
</body>
</html>

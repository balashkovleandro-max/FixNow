<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin заявки | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    @php
        $filters = $filters ?? ['status' => 'all', 'city' => '', 'category' => '', 'has_offers' => 'all', 'selected' => 'all'];
        $statusOptions = [
            'all' => 'Всички',
            'open' => 'Open',
            'in_progress' => 'В процес / accepted',
            'completed' => 'Завършени',
            'cancelled_closed' => 'Cancelled / closed',
        ];
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
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_10%,rgba(251,146,60,0.16),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_32%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 font-black">B</span>
                <span class="text-xl font-black">BON Admin</span>
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
            <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Заявки и оферти</p>
            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black sm:text-5xl">Всички клиентски заявки</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-white/65">
                        Следете целия flow: заявка, получени оферти, избран бизнес и финален статус.
                    </p>
                </div>
                <span class="rounded-2xl border border-orange-300/20 bg-orange-300/10 px-4 py-3 text-sm font-black text-orange-100">
                    {{ $serviceRequests->total() }} заявки
                </span>
            </div>

            <form method="GET" action="{{ route('admin.service-requests.index') }}" class="mt-6 grid gap-3 lg:grid-cols-6">
                <label class="block lg:col-span-1">
                    <span class="text-xs font-black uppercase tracking-[0.16em] text-white/45">Статус</span>
                    <select name="status" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-slate-950 px-4 text-white">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block lg:col-span-1">
                    <span class="text-xs font-black uppercase tracking-[0.16em] text-white/45">Град</span>
                    <input name="city" list="admin-request-cities" value="{{ $filters['city'] ?? '' }}" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 text-white" placeholder="Плевен">
                    <datalist id="admin-request-cities">
                        @foreach($cities as $city)
                            <option value="{{ $city }}"></option>
                        @endforeach
                    </datalist>
                </label>
                <label class="block lg:col-span-1">
                    <span class="text-xs font-black uppercase tracking-[0.16em] text-white/45">Категория</span>
                    <input name="category" list="admin-request-categories" value="{{ $filters['category'] ?? '' }}" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 text-white" placeholder="Избери категория">
                    <datalist id="admin-request-categories">
                        @foreach($categories as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                </label>
                <label class="block lg:col-span-1">
                    <span class="text-xs font-black uppercase tracking-[0.16em] text-white/45">Оферти</span>
                    <select name="has_offers" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-slate-950 px-4 text-white">
                        <option value="all" @selected(($filters['has_offers'] ?? 'all') === 'all')>Всички</option>
                        <option value="yes" @selected(($filters['has_offers'] ?? 'all') === 'yes')>Has offers</option>
                        <option value="no" @selected(($filters['has_offers'] ?? 'all') === 'no')>No offers</option>
                    </select>
                </label>
                <label class="block lg:col-span-1">
                    <span class="text-xs font-black uppercase tracking-[0.16em] text-white/45">Избран</span>
                    <select name="selected" class="mt-2 min-h-12 w-full rounded-2xl border border-white/10 bg-slate-950 px-4 text-white">
                        <option value="all" @selected(($filters['selected'] ?? 'all') === 'all')>Всички</option>
                        <option value="yes" @selected(($filters['selected'] ?? 'all') === 'yes')>Selected</option>
                        <option value="no" @selected(($filters['selected'] ?? 'all') === 'no')>Not selected</option>
                    </select>
                </label>
                <div class="flex items-end gap-2 lg:col-span-1">
                    <button class="min-h-12 flex-1 rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-4 font-black text-white">Филтрирай</button>
                    <a href="{{ route('admin.service-requests.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-4 text-sm font-black text-white hover:bg-white/15">Reset</a>
                </div>
            </form>
        </section>

        <section class="mt-6 grid gap-4">
            @forelse($serviceRequests as $serviceRequest)
                @php
                    $title = $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка #'.$serviceRequest->id;
                    $selectedExecutor = $serviceRequest->selectedOffer?->business ?: $serviceRequest->assignedBusiness;
                @endphp
                <article class="rounded-[28px] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <div class="grid gap-4 lg:grid-cols-[1fr_280px]">
                        <div>
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">ID #{{ $serviceRequest->id }}</span>
                                <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusClasses[$serviceRequest->status] ?? 'border-white/10 bg-white/10 text-white/70' }}">
                                    {{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}
                                </span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/60">{{ $serviceRequest->created_at?->format('d.m.Y H:i') }}</span>
                                @if(($serviceRequest->offers_count ?? $serviceRequest->offers->count()) > 0)
                                    <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">Has offers</span>
                                @else
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/50">No offers</span>
                                @endif
                                @if($serviceRequest->selected_offer_id)
                                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">Selected</span>
                                @endif
                            </div>

                            <h2 class="mt-4 text-2xl font-black">{{ $title }}</h2>
                            <p class="mt-1 text-sm text-white/55">Клиент: <strong class="text-white">{{ $serviceRequest->name }}</strong></p>
                            <div class="mt-3 grid gap-2 text-sm text-white/65 sm:grid-cols-2 lg:grid-cols-4">
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Телефон: <strong class="text-white">{{ $serviceRequest->phone ?: 'Няма' }}</strong></p>
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Имейл: <strong class="text-white">{{ $serviceRequest->email ?: 'Няма' }}</strong></p>
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Град: <strong class="text-white">{{ $serviceRequest->city ?: 'Няма' }}</strong></p>
                                <p class="rounded-2xl bg-slate-950/45 px-4 py-3">Категория: <strong class="text-white">{{ $serviceRequest->category ?: 'Няма' }}</strong></p>
                            </div>
                        </div>

                        <aside class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm text-white/50">Получени оферти</p>
                            <p class="mt-2 text-3xl font-black">{{ $serviceRequest->offers_count ?? $serviceRequest->offers->count() }}</p>
                            <p class="mt-4 text-sm text-white/50">Избран бизнес</p>
                            <p class="mt-1 font-black">{{ $selectedExecutor?->business_name ?: $selectedExecutor?->name ?: 'Няма' }}</p>
                            @if($serviceRequest->selectedOffer)
                                <div class="mt-3 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-3">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-100">Приета оферта</p>
                                    <p class="mt-1 text-sm font-black text-white">{{ $serviceRequest->selectedOffer->price_estimate ?: 'Без посочена цена' }}</p>
                                </div>
                            @endif
                            <a href="{{ route('admin.service-requests.show', $serviceRequest) }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white">
                                Детайли
                            </a>
                        </aside>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-xl font-black">B</p>
                    <h2 class="mt-4 text-2xl font-black">Няма заявки по този филтър</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/60">Променете филтрите или вижте всички заявки, за да проследите статуса и назначените бизнеси.</p>
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

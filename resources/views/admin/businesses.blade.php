@extends('layouts.admin')

@section('title', 'Бизнеси')
@section('page-title', 'Управление на бизнеси')
@section('eyebrow', 'Business control')

@section('content')
    @php
        $filterItems = [
            'all' => 'Всички',
            'active' => 'Активни',
            'stopped' => 'Спрени',
            'paid' => 'Платени',
            'unpaid' => 'Неплатени',
            'standard' => 'Standard',
            'premium' => 'Premium',
            'trial' => 'Trial',
            'expired' => 'Expired',
            'verified' => 'Verified',
            'unverified' => 'Unverified',
        ];

        $businessActions = [
            'activate' => 'Активирай',
            'suspend' => 'Спри/скрий профила',
            'restore' => 'Върни профила',
            'premium' => 'Направи Premium',
            'standard' => 'Направи Standard',
            'remove_plan' => 'Премахни абонамент/план',
            'mark_paid' => 'Маркирай като платен',
            'mark_unpaid' => 'Маркирай като неплатен',
            'trial' => 'Дай trial',
            'expired' => 'Маркирай expired',
            'cancelled' => 'Маркирай cancelled',
            'verify' => 'Verify',
            'unverify' => 'Unverify',
        ];
    @endphp

    <section class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <form method="GET" action="{{ route('admin.businesses.index') }}" class="grid gap-3 lg:grid-cols-[1fr_auto]">
            <div class="grid gap-3 md:grid-cols-[220px_1fr]">
                <select name="status" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none focus:border-blue-300">
                    @foreach($filterItems as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['status'] ?? 'all') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Търси по бизнес, собственик, телефон, имейл, град, категория..." class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none placeholder:text-white/35 focus:border-blue-300">
            </div>
            <button class="min-h-12 rounded-2xl bg-white px-5 text-sm font-black text-[#070B1F]">Филтрирай</button>
        </form>
    </section>

    <section class="mt-4 rounded-[1.75rem] border border-white/10 bg-white/[.08] p-3 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black">Всички бизнес профили</h2>
                <p class="mt-1 text-sm text-white/50">Планове, публична видимост, verified статус и ръчни admin действия.</p>
            </div>
            <p class="text-sm font-bold text-white/50">{{ $businesses->total() }} резултата</p>
        </div>

        <div class="overflow-x-auto rounded-[1.35rem] border border-white/10">
            <table class="min-w-[1180px] w-full text-left text-sm">
                <thead class="bg-white/10 text-xs uppercase tracking-[.16em] text-white/45">
                    <tr>
                        <th class="px-4 py-3">Бизнес</th>
                        <th class="px-4 py-3">Контакти</th>
                        <th class="px-4 py-3">Градове / категории</th>
                        <th class="px-4 py-3">План / статус</th>
                        <th class="px-4 py-3">Дати</th>
                        <th class="px-4 py-3">Видимост</th>
                        <th class="px-4 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($businesses as $business)
                        @php
                            $isSuspended = (bool) ($business->is_suspended ?? false);
                            $cities = implode(', ', $business->serviceCities());
                            $categories = collect($business->serviceCategories())
                                ->map(fn ($profileCategory) => \App\Support\CategoryCatalog::displayName($profileCategory))
                                ->filter()
                                ->unique()
                                ->implode(', ');
                        @endphp
                        <tr class="align-top">
                            <td class="px-4 py-4">
                                <p class="font-black">{{ $business->business_name ?: $business->name }}</p>
                                <p class="mt-1 text-white/50">Собственик: {{ $business->name }}</p>
                                <p class="mt-1 text-xs text-white/35">ID #{{ $business->id }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ $business->phone ?: 'Няма телефон' }}</p>
                                <p class="mt-1 text-white/50">{{ $business->email }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="max-w-[220px] text-white/70">{{ $cities ?: ($business->city ?: 'Няма град') }}</p>
                                <p class="mt-2 max-w-[220px] text-white/45">{{ $categories ?: ($business->business_category ? \App\Support\CategoryCatalog::displayName($business->business_category) : 'Няма категория') }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $business->subscription_plan ?: 'Free' }}</span>
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $isSuspended ? 'suspended' : $business->effectiveSubscriptionStatus() }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-white/55">
                                <p>Рег.: {{ $business->created_at?->format('d.m.Y') }}</p>
                                <p class="mt-1">Последно плащане: {{ $business->subscription_started_at?->format('d.m.Y') ?: '—' }}</p>
                                <p class="mt-1">Изтича: {{ $business->subscription_ends_at?->format('d.m.Y') ?: $business->trial_ends_at?->format('d.m.Y') ?: '—' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="grid gap-2">
                                    <span class="rounded-full {{ $business->is_verified ? 'bg-emerald-400/10 text-emerald-100' : 'bg-amber-400/10 text-amber-100' }} px-3 py-1 text-xs font-black">{{ $business->is_verified ? 'Verified' : 'Unverified' }}</span>
                                    <span class="rounded-full {{ $business->isPubliclyVisible() ? 'bg-blue-400/10 text-blue-100' : 'bg-rose-400/10 text-rose-100' }} px-3 py-1 text-xs font-black">{{ $business->isPubliclyVisible() ? 'Публично видим' : 'Скрит публично' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="grid min-w-[220px] gap-2">
                                    <div class="flex gap-2">
                                        <a href="{{ route('businesses.show', $business) }}" class="flex-1 rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-center text-xs font-black text-white hover:bg-white/15">Виж</a>
                                        <a href="{{ route('admin.businesses.edit', $business) }}" class="flex-1 rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-center text-xs font-black text-white hover:bg-white/15">Редактирай</a>
                                    </div>
                                    <form action="{{ route('admin.businesses.update', $business) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="action" class="min-h-10 min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-xs font-bold text-white">
                                            @foreach($businessActions as $action => $label)
                                                <option value="{{ $action }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-xl bg-white px-3 text-xs font-black text-[#070B1F]">OK</button>
                                    </form>
                                    <button type="button" data-delete-action="{{ route('admin.businesses.destroy', $business) }}" data-delete-title="{{ $business->business_name ?: $business->name }}" class="rounded-xl border border-rose-300/20 bg-rose-400/10 px-3 py-2 text-xs font-black text-rose-100 hover:bg-rose-400/20">
                                        Изтрий
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-white/50">Няма бизнеси по тези критерии.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $businesses->links() }}
        </div>
    </section>

    <div id="delete-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-[1.75rem] border border-white/10 bg-[#081224] p-5 shadow-2xl shadow-black/40">
            <h3 class="text-xl font-black">Потвърди изтриване</h3>
            <p class="mt-3 text-sm leading-6 text-white/60">Сигурен ли си, че искаш да изтриеш <span id="delete-modal-name" class="font-black text-white"></span>? Това действие е необратимо.</p>
            <form id="delete-modal-form" method="POST" class="mt-5 flex flex-col gap-3 sm:flex-row">
                @csrf
                @method('DELETE')
                <button type="button" data-delete-close class="min-h-11 flex-1 rounded-2xl border border-white/10 bg-white/10 px-4 text-sm font-black text-white">Отказ</button>
                <button class="min-h-11 flex-1 rounded-2xl bg-rose-500 px-4 text-sm font-black text-white">Изтрий</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-delete-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const modal = document.getElementById('delete-modal');
                document.getElementById('delete-modal-form').action = button.dataset.deleteAction;
                document.getElementById('delete-modal-name').textContent = button.dataset.deleteTitle || 'този профил';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        document.querySelectorAll('[data-delete-close], #delete-modal').forEach((element) => {
            element.addEventListener('click', (event) => {
                if (event.target !== element && !element.matches('[data-delete-close]')) {
                    return;
                }

                const modal = document.getElementById('delete-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        });
    </script>
@endpush

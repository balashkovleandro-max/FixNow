@extends('layouts.admin')

@section('title', 'Заявки')
@section('page-title', 'Заявки и консултации')
@section('eyebrow', 'Requests')

@section('content')
    @php
        $statuses = ['all' => 'Всички', 'new' => 'new', 'open' => 'open', 'contacted' => 'contacted', 'in_progress' => 'in_progress', 'completed' => 'completed', 'cancelled' => 'cancelled', 'closed' => 'closed'];
        $editableStatuses = collect($statuses)->except('all');
    @endphp

    <section class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <form method="GET" action="{{ route('admin.requests.index') }}" class="grid gap-3 lg:grid-cols-[220px_1fr_auto]">
            <select name="status" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none">
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['status'] ?? 'all') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Търси по клиент, телефон, имейл, град, категория..." class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none placeholder:text-white/35">
            <button class="min-h-12 rounded-2xl bg-white px-5 text-sm font-black text-[#070B1F]">Филтрирай</button>
        </form>
    </section>

    <section class="mt-4 grid gap-3">
        @forelse($requests as $serviceRequest)
            <article class="rounded-[1.5rem] border border-white/10 bg-white/[.08] p-4 shadow-xl shadow-black/15 backdrop-blur-xl">
                <div class="grid gap-4 xl:grid-cols-[1fr_.85fr_.9fr_auto]">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $serviceRequest->status }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $serviceRequest->category ?: 'без категория' }}</span>
                        </div>
                        <p class="mt-3 text-lg font-black">{{ $serviceRequest->name ?: 'Клиент' }}</p>
                        <p class="mt-1 text-sm text-white/50">{{ $serviceRequest->phone ?: 'няма телефон' }} · {{ $serviceRequest->email ?: 'няма имейл' }}</p>
                        <p class="mt-1 text-sm text-white/50">{{ $serviceRequest->city ?: 'няма град' }} · {{ $serviceRequest->created_at?->format('d.m.Y H:i') }}</p>
                    </div>
                    <p class="text-sm leading-6 text-white/65">{{ $serviceRequest->description }}</p>
                    <div class="grid gap-2 text-sm text-white/55">
                        <p>Избран бизнес: <span class="font-bold text-white">{{ $serviceRequest->selectedOffer?->business?->business_name ?: $serviceRequest->assignedBusiness?->business_name ?: '—' }}</span></p>
                        <p>Оферти: <span class="font-bold text-white">{{ $serviceRequest->offers_count ?? $serviceRequest->offers->count() }}</span></p>
                        <p>Снимки: <span class="font-bold text-white">{{ $serviceRequest->photos->count() }}</span></p>
                    </div>
                    <div class="grid gap-2">
                        <form action="{{ route('admin.requests.update', $serviceRequest) }}" method="POST" class="grid gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="min-h-10 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-xs font-bold text-white">
                                @foreach($editableStatuses as $status => $label)
                                    <option value="{{ $status }}" @selected($serviceRequest->status === $status)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="assigned_business_id" class="min-h-10 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-xs font-bold text-white">
                                <option value="">Без назначен бизнес</option>
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}" @selected((int) $serviceRequest->assigned_business_id === (int) $business->id)>{{ $business->business_name ?: $business->name }}</option>
                                @endforeach
                            </select>
                            <button class="min-h-10 rounded-xl bg-white px-3 text-xs font-black text-[#070B1F]">Запази</button>
                        </form>
                        <form action="{{ route('admin.requests.destroy', $serviceRequest) }}" method="POST" onsubmit="return confirm('Да изтрия ли тази заявка?')">
                            @csrf
                            @method('DELETE')
                            <button class="min-h-10 w-full rounded-xl bg-rose-500/15 px-3 text-xs font-black text-rose-100">Изтрий</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-white/10 bg-white/5 p-5 text-white/55">Няма заявки.</p>
        @endforelse
    </section>

    <div class="mt-4">{{ $requests->links() }}</div>
@endsection

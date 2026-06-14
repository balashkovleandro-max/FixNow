@extends('layouts.admin')

@section('title', 'Оферти')
@section('page-title', 'Оферти и кандидатури')
@section('eyebrow', 'Offers')

@section('content')
    <section class="grid gap-4 xl:grid-cols-2">
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <h2 class="text-xl font-black">Оферти по заявки</h2>
            <div class="mt-4 grid gap-3">
                @forelse($serviceOffers as $offer)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-black">{{ $offer->business?->business_name ?: $offer->business?->name ?: 'Бизнес' }}</p>
                                <p class="mt-1 text-sm text-white/50">{{ $offer->price_estimate ?: 'без цена' }} · {{ $offer->timeframe ?: 'без срок' }}</p>
                                <p class="mt-1 text-sm text-white/45">{{ $offer->phone ?: $offer->business?->phone ?: 'няма телефон' }} · {{ $offer->email ?: $offer->business?->email }}</p>
                            </div>
                            <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $offer->status }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-white/60">{{ $offer->message }}</p>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-white/55">Няма оферти.</p>
                @endforelse
            </div>
            @if(method_exists($serviceOffers, 'links'))
                <div class="mt-4">{{ $serviceOffers->links() }}</div>
            @endif
        </article>

        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <h2 class="text-xl font-black">Кандидатури от фрийлансъри</h2>
            <div class="mt-4 grid gap-3">
                @forelse($freelancerApplications as $application)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-black">{{ $application->freelancer?->name ?: 'Фрийлансър' }}</p>
                                <p class="mt-1 text-sm text-white/50">{{ $application->job?->title ?: 'Обява' }} · {{ $application->proposed_price ?: 'без цена' }} · {{ $application->proposed_timeframe ?: 'без срок' }}</p>
                                <p class="mt-1 text-sm text-white/45">{{ $application->freelancer?->email }} · {{ $application->freelancer?->phone ?: 'няма телефон' }}</p>
                            </div>
                            <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-black text-violet-100">{{ $application->status ?? 'sent' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/10 bg-white/5 p-4 text-white/55">Няма кандидатури.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection

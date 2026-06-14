@extends('layouts.admin')

@section('title', 'Отзиви')
@section('page-title', 'Отзиви')
@section('eyebrow', 'Reviews')

@section('content')
    <section class="grid gap-3">
        @forelse($reviews as $review)
            <article class="rounded-[1.5rem] border border-white/10 bg-white/[.08] p-4 shadow-xl shadow-black/15 backdrop-blur-xl">
                <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-amber-400/10 px-3 py-1 text-xs font-black text-amber-100">{{ $review->rating }}/5</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/70">{{ $review->status }}</span>
                        </div>
                        <p class="mt-3 font-black">{{ $review->reviewer_name }} за {{ $review->business?->business_name ?: $review->business?->name }}</p>
                        <p class="mt-2 text-sm leading-6 text-white/60">{{ $review->comment }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="min-h-10 rounded-xl bg-emerald-400/15 px-4 text-xs font-black text-emerald-100">Approve</button>
                        </form>
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="min-h-10 rounded-xl bg-rose-400/15 px-4 text-xs font-black text-rose-100">Reject</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-white/10 bg-white/5 p-5 text-white/55">Няма отзиви.</p>
        @endforelse
    </section>

    @if(method_exists($reviews, 'links'))
        <div class="mt-4">{{ $reviews->links() }}</div>
    @endif
@endsection

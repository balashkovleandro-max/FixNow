@extends('layouts.admin')

@section('title', 'Градове')
@section('page-title', 'Градове')
@section('eyebrow', 'Locations')

@section('content')
    <section class="grid gap-4 lg:grid-cols-2">
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <h2 class="text-xl font-black">Градове от бизнес профили</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse($businessCities as $city)
                    <span class="rounded-full bg-blue-400/10 px-4 py-2 text-sm font-black text-blue-100">{{ $city }}</span>
                @empty
                    <p class="text-white/55">Няма градове.</p>
                @endforelse
            </div>
        </article>
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
            <h2 class="text-xl font-black">Градове от заявки</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse($requestCities as $city)
                    <span class="rounded-full bg-violet-400/10 px-4 py-2 text-sm font-black text-violet-100">{{ $city }}</span>
                @empty
                    <p class="text-white/55">Няма градове.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection

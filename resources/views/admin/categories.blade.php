@extends('layouts.admin')

@section('title', 'Категории')
@section('page-title', 'Категории')
@section('eyebrow', 'Catalog')

@section('content')
    <section class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <h2 class="text-xl font-black">Категории в BON</h2>
        <p class="mt-2 text-sm text-white/55">Преглед на активните категории от catalog/config и database.</p>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($categories as $category)
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="font-black">{{ $category['name'] ?? 'Категория' }}</p>
                    <p class="mt-1 text-xs text-white/45">{{ $category['group'] ?? '—' }} · {{ $category['type'] ?? '—' }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection

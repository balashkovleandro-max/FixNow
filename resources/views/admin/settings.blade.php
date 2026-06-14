@extends('layouts.admin')

@section('title', 'Настройки')
@section('page-title', 'Настройки')
@section('eyebrow', 'System')

@section('content')
    <section class="grid gap-4 lg:grid-cols-3">
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-xl font-black">Admin достъп</h2>
            <p class="mt-3 text-sm leading-6 text-white/58">Достъпът до `/admin` е защитен с `auth` + `admin` middleware. Само `role=admin` може да отваря панела.</p>
        </article>
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-xl font-black">Публична видимост</h2>
            <p class="mt-3 text-sm leading-6 text-white/58">Спрени, expired, cancelled и suspended бизнеси не трябва да се показват публично.</p>
        </article>
        <article class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-xl font-black">Общо записи</h2>
            <p class="mt-3 text-3xl font-black">{{ $stats['total_users'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-white/50">регистрирани потребители</p>
        </article>
    </section>
@endsection

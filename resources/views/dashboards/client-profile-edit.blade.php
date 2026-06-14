<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Клиентски профил | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $selectedCategories = collect(old('preferred_categories', $user->preferred_categories ?? []))->filter()->values()->all();
@endphp
<body class="min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
    <main class="relative min-h-screen overflow-x-clip">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-4xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[1.5rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:rounded-[2rem] sm:p-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">BON</p>
                        <p class="text-sm text-slate-500">Клиентски профил</p>
                    </div>
                </a>

                <div class="flex flex-wrap gap-2 text-sm font-bold">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Табло</a>
                    <a href="{{ route('request.service') }}" class="rounded-2xl bg-blue-50 px-4 py-2 text-blue-700">Публикувай заявка</a>
                </div>
            </header>

            @if($errors->any())
                <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-7 rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Профил за заявки</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Попълни данните, с които получаваш оферти.</h1>
                <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                    Клиентският профил е прост: име, контакт, град и предпочитани категории. Той помага на BON да подрежда заявките и офертите по-ясно.
                </p>

                <form action="{{ route('dashboard.client.profile.update') }}" method="POST" class="mt-8 grid gap-6">
                    @csrf
                    @method('PUT')

                    <div id="identity" class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Име
                            <input name="name" value="{{ old('name', $user->name) }}" required maxlength="160" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label id="contact" class="grid gap-2 text-sm font-black text-slate-700">
                            Имейл
                            <input name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Телефон
                            <input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="80" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label id="location" class="grid gap-2 text-sm font-black text-slate-700">
                            Град
                            <input name="city" value="{{ old('city', $user->city) }}" maxlength="120" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div id="preferences" class="rounded-3xl border border-slate-100 bg-white/70 p-5">
                        <p class="font-black text-slate-900">Предпочитани категории</p>
                        <p class="mt-1 text-sm text-slate-500">Избери темите, по които най-често публикуваш заявки или търсиш оферти.</p>

                        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white/80 px-4 py-3 text-sm font-bold text-slate-700">
                                    <input type="checkbox" name="preferred_categories[]" value="{{ $category }}" @checked(in_array($category, $selectedCategories, true)) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    {{ $category }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                            Запази профила
                        </button>
                        <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-6 text-sm font-black text-slate-700">
                            Назад към таблото
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

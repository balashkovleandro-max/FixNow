<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Страницата не е намерена | BON</title>
    <meta name="description" content="Страницата не е намерена. Върнете се към BON, търсене, бизнеси, фрийлансъри или публикувайте заявка.">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="bon-dark-page min-h-screen overflow-x-hidden bg-[#020617] text-white antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute -top-40 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -top-40 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-500/20 blur-3xl"></div>
        <div class="absolute bottom-[-18rem] left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-300/15 blur-3xl"></div>
    </div>

    @include('partials.public-header')

    <main class="mx-auto flex min-h-[calc(100vh-9rem)] max-w-5xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <section class="w-full rounded-[2rem] border border-white/10 bg-white/10 p-6 text-center shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-10">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black shadow-xl shadow-violet-500/25">404</div>
            <p class="mt-6 text-sm font-black uppercase tracking-[0.24em] text-blue-200">BON navigation</p>
            <h1 class="mx-auto mt-3 max-w-3xl text-3xl font-black tracking-tight sm:text-5xl">Тази страница не е намерена.</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-300">
                Линкът може да е преместен или вече да не съществува. Изберете следваща стъпка и продължете в BON без задънена улица.
            </p>

            <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('home') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-lg shadow-violet-500/20">Начало</a>
                <a href="{{ route('search') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 text-sm font-black text-white hover:bg-white/15">Търсене</a>
                <a href="{{ route('request.service') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 text-sm font-black text-white hover:bg-white/15">Публикувай заявка</a>
                <a href="{{ route('contact') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-5 text-sm font-black text-white hover:bg-white/15">Контакт</a>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

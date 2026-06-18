<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BON | Бизнеси, услуги и места</title>
    <meta name="description" content="BON помага на хората да откриват проверени бизнеси, услуги и места, а на бизнесите - повече видимост, доверие и клиенти.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bon-dark-page antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-x-hidden px-4 py-12 text-white">
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-10 h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.26]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <section class="relative z-10 max-w-3xl rounded-[2rem] border border-white/70 bg-white/75 p-8 text-center shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-3xl font-black text-white shadow-xl shadow-violet-500/25">
                B
            </div>

            <p class="mt-6 text-sm font-black uppercase tracking-[0.22em] text-violet-600">BON</p>
            <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight sm:text-5xl">
                Открий бизнес, услуга или място.
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-600">
                Това е legacy fallback страница. Основната продуктова посока вече е BON - платформа за проверени бизнеси,
                услуги и места, създадена за повече видимост, доверие и клиенти.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5">
                    Към BON начало
                </a>
                <a href="{{ route('businesses.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/70 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600">
                    Разгледай бизнеси
                </a>
            </div>
        </section>
    </main>
</body>
</html>

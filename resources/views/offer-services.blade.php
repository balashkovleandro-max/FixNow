<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Предлагай услуги | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,rgba(34,211,238,0.18),transparent_30%),radial-gradient(circle_at_80%_10%,rgba(168,85,247,0.20),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="mb-10 inline-flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black">F</div>
            <span class="text-xl font-black">FixNow.bg</span>
        </a>

        <section class="grid gap-8 rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10 lg:grid-cols-[1fr_420px]">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">За изпълнители</p>
                <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-6xl">Покажи услугите си пред клиенти, които вече търсят.</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-white/70">FixNow.bg помага на майстори, сервизи, салони и локални професионалисти да изглеждат професионално онлайн.</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6">
                <div class="grid gap-3 text-sm text-white/70">
                    <p class="rounded-2xl bg-white/10 p-4">Профил с категория, град и контакти</p>
                    <p class="rounded-2xl bg-white/10 p-4">Доверителни badges и premium визуално представяне</p>
                    <p class="rounded-2xl bg-white/10 p-4">Запитвания, оферти и мнения като следващ етап</p>
                </div>
                @auth
                    <a href="{{ route('services.create') }}" class="mt-5 block rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 text-center font-black text-white">Добави услуга</a>
                @else
                    <a href="{{ route('register') }}" class="mt-5 block rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 text-center font-black text-white">Стани изпълнител</a>
                @endauth
            </div>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

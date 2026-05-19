<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Как работи FixNow.bg за клиенти и изпълнители - търсене, сравнение, заявки, профили, отзиви и директен контакт.">
    <title>Как работи | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(37,99,235,0.20),transparent_30%),radial-gradient(circle_at_82%_18%,rgba(168,85,247,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    @include('partials.public-header')

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">Как работи</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-6xl">От нужда до доверен изпълнител в три ясни стъпки.</h1>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                @foreach([
                    ['step' => '01', 'title' => 'Търсиш', 'text' => 'Избираш услуга, град и радиус. UI-то е направено за бързо сравнение.'],
                    ['step' => '02', 'title' => 'Сравняваш', 'text' => 'Виждаш рейтинг, категория, доверителни badges, работно време и контакт.'],
                    ['step' => '03', 'title' => 'Свързваш се', 'text' => 'Изпращаш запитване или директно звъниш на изпълнителя.'],
                ] as $item)
                    <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6">
                        <p class="text-3xl font-black text-cyan-200">{{ $item['step'] }}</p>
                        <h2 class="mt-4 text-2xl font-black">{{ $item['title'] }}</h2>
                        <p class="mt-3 text-sm leading-7 text-white/60">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-6 grid gap-4 lg:grid-cols-2">
            <article class="rounded-[32px] border border-cyan-300/20 bg-cyan-300/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-100">За клиенти</p>
                <h2 class="mt-3 text-2xl font-black">Намерете услуга или изпратете заявка</h2>
                <p class="mt-3 text-sm leading-7 text-white/70">Можете да търсите по категория и град, да сравнявате профили, отзиви и badges или директно да изпратите заявка за оферта към подходящи изпълнители.</p>
                <a href="{{ route('request.service') }}" class="mt-5 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white">Заяви оферта</a>
            </article>

            <article class="rounded-[32px] border border-violet-300/20 bg-violet-400/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-100">За изпълнители</p>
                <h2 class="mt-3 text-2xl font-black">Създайте профил и получавайте директни запитвания</h2>
                <p class="mt-3 text-sm leading-7 text-white/70">Изпълнителите получават публичен профил, видимост в резултатите, analytics, заявки от клиенти и инструменти за управление на статус, план и съдържание.</p>
                <a href="{{ route('plans') }}" class="mt-5 inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-6 py-4 font-black text-white hover:bg-white/15">Виж планове</a>
            </article>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

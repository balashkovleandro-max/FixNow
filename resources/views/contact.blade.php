<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Свържете се с FixNow.bg за въпроси за профили на изпълнители, заявки за оферта, партньорства и поддръжка.">
    <title>Контакт | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    @php
        $contactEmail = config('mail.from.address') ?: 'hello@fixnow.bg';
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,rgba(251,146,60,0.16),transparent_30%),radial-gradient(circle_at_80%_10%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto grid max-w-7xl items-start gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_520px] lg:px-8">
        <section class="pt-4 lg:pt-12">
            <p class="mb-4 inline-flex rounded-full border border-orange-300/20 bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100">Контакт</p>
            <h1 class="max-w-2xl text-4xl font-black leading-tight sm:text-6xl">Нека направим услугите по-лесни за намиране.</h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-white/70">
                Пишете ни за профил на изпълнител, заявка, партньорство, проблем с акаунт или идея за развитие на FixNow.bg.
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <a href="{{ route('business.landing') }}" class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl hover:border-orange-300/30">
                    <p class="text-sm font-black text-orange-100">За изпълнители</p>
                    <h2 class="mt-2 text-xl font-black">Добавете профил</h2>
                    <p class="mt-2 text-sm leading-6 text-white/60">Вижте как FixNow.bg може да носи повече видимост, директни обаждания и заявки.</p>
                </a>
                <a href="{{ route('request.service') }}" class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl hover:border-orange-300/30">
                    <p class="text-sm font-black text-orange-100">За клиенти</p>
                    <h2 class="mt-2 text-xl font-black">Заявете оферта</h2>
                    <p class="mt-2 text-sm leading-6 text-white/60">Опишете каква услуга търсите и ще насочим заявката към подходящи изпълнители.</p>
                </a>
            </div>
        </section>

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-8 lg:mt-12">
            <h2 class="text-2xl font-black">Как да се свържете с нас</h2>
            <div class="mt-6 grid gap-4">
                <a href="mailto:{{ $contactEmail }}" class="block rounded-3xl border border-white/10 bg-slate-950/50 p-5 hover:bg-white/10">
                    <p class="text-sm text-white/60">Имейл</p>
                    <p class="mt-1 break-words text-xl font-black text-orange-300">{{ $contactEmail }}</p>
                    <p class="mt-2 text-sm leading-6 text-white/55">За партньорства, профили на изпълнители, правни въпроси и поддръжка.</p>
                </a>

                <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                    <p class="text-sm text-white/60">Поддръжка</p>
                    <p class="mt-1 text-xl font-black">Отговор в работно време</p>
                    <p class="mt-2 text-sm leading-6 text-white/60">За спешна услуга използвайте “Заяви оферта”, за да стигне заявката до подходящи изпълнители.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('request.service') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 text-center font-black text-white">
                        Заяви оферта
                    </a>
                    <a href="{{ route('plans') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-6 py-4 text-center font-black text-white hover:bg-white/15">
                        Виж планове
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

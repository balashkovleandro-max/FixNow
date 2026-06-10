@php
    $steps = [
        ['title' => 'Създаваш професионален профил', 'text' => 'Показваш ясно услугите, опита, портфолиото и начина, по който помагаш на клиентите.'],
        ['title' => 'BON подрежда доверието', 'text' => 'Профилът ти работи като премиум представяне, не като случайна обява в списък.'],
        ['title' => 'Получаваш по-ясни запитвания', 'text' => 'Клиентите виждат какво предлагаш и защо си подходящ за конкретната нужда.'],
        ['title' => 'Развиваш репутация', 'text' => 'Събираш отзиви, показваш резултати и изграждаш по-силен професионален образ.'],
    ];

    $benefits = [
        'Профил, който изглежда професионално и вдъхва доверие',
        'По-добро представяне на услуги, процес, опит и портфолио',
        'Възможност за Premium видимост и по-силно позициониране',
        'Отзиви, badges и сигнали за доверие',
        'Ясен път от интерес към контакт',
        'Подходящо за независими специалисти и малки екипи',
    ];

    $categories = [
        'Дизайнери',
        'Програмисти',
        'Маркетолози',
        'Фотографи',
        'Copywriters',
        'Видео монтажисти',
        'Консултанти',
        'Sales специалисти',
        'SEO специалисти',
        'Бранд стратези',
        'Автоматизации',
        'Други специалисти',
    ];

    $faqs = [
        ['question' => 'BON само за фирми ли е?', 'answer' => 'Не. BON е подходящ и за независими специалисти, които искат по-професионално онлайн присъствие и повече доверие пред клиентите.'],
        ['question' => 'Това фриланс борса ли е?', 'answer' => 'Не. BON не е евтина борса за задачи. Фокусът е върху професионален профил, доверие, видимост и по-качествено представяне.'],
        ['question' => 'Трябва ли да имам фирма?', 'answer' => 'Можеш да започнеш като независим специалист. Ако предлагаш услуги професионално, BON може да ти помогне да ги представиш по-добре.'],
        ['question' => 'Има ли планове за фрилансъри?', 'answer' => 'Плановете използват същата BON логика: Standard за стабилно присъствие и Premium за по-силна видимост, репутация и растеж.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>За фрилансъри | BON</title>
    <meta name="description" content="BON помага на фрилансъри и независими специалисти да изградят професионален профил, да бъдат откривани по-лесно и да печелят повече доверие.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="antialiased">
    <main class="relative min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.98)_0%,rgba(248,250,255,.82)_42%,rgba(248,250,255,1)_100%)]"></div>
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 top-20 h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[28rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/16 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.25]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10">
            @include('partials.public-header')

            <section class="mx-auto grid max-w-[1440px] gap-8 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:px-8 lg:py-24">
                <div>
                    <div class="inline-flex rounded-full border border-violet-200/70 bg-white/80 px-3.5 py-2 text-xs font-bold text-violet-700 shadow-sm backdrop-blur-xl sm:px-4 sm:text-sm">
                        BON Freelancers
                    </div>
                    <h1 class="mt-5 max-w-4xl text-3xl font-black leading-tight tracking-tight sm:mt-6 sm:text-6xl">
                        Покажи услугите си пред <span class="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">правилните клиенти</span>.
                    </h1>
                    <p class="mt-5 max-w-3xl text-base leading-7 text-slate-600 sm:mt-6 sm:text-lg sm:leading-8">
                        BON помага на фрилансъри и независими специалисти да изградят професионален профил,
                        да бъдат откривани по-лесно и да получават повече запитвания от реални клиенти.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 sm:w-auto">
                            Започни като фрилансър
                        </a>
                        <a href="{{ route('plans') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-slate-200/80 bg-white/75 px-6 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:w-auto">
                            Виж плановете
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-8 rounded-full bg-gradient-to-br from-blue-400/18 via-violet-400/18 to-fuchsia-400/18 blur-3xl"></div>
                    <div class="relative rounded-[1.65rem] border border-white/70 bg-white/78 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Профил</p>
                                <h2 class="mt-2 text-2xl font-black tracking-tight">Independent Specialist</h2>
                            </div>
                            <span class="rounded-full bg-gradient-to-r from-blue-600 to-fuchsia-500 px-4 py-2 text-xs font-black text-white shadow-lg shadow-violet-500/20">Premium</span>
                        </div>

                        <div class="mt-7 grid gap-3">
                            @foreach (['Портфолио', 'Услуги', 'Отзиви', 'Контакт', 'Репутация'] as $item)
                                <div class="flex items-center justify-between rounded-2xl border border-white/80 bg-white/75 px-4 py-3 shadow-sm">
                                    <span class="text-sm font-black text-[#070B1F]">{{ $item }}</span>
                                    <span class="h-2 w-20 rounded-full bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500"></span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-7 rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-xl shadow-violet-500/20">
                            <p class="text-sm font-bold text-white/75">Фокус</p>
                            <p class="mt-2 text-2xl font-black">Видимост, доверие и повече качествени запитвания.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <div class="rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Как работи</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">От професионално представяне към повече доверие.</h2>
                    <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($steps as $step)
                            <article class="rounded-3xl border border-slate-100 bg-white/75 p-5 shadow-lg shadow-blue-900/5">
                                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-sm font-black text-white">{{ $loop->iteration }}</div>
                                <h3 class="text-lg font-black">{{ $step['title'] }}</h3>
                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $step['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-[1440px] gap-6 px-4 py-6 sm:px-6 sm:py-8 lg:grid-cols-2 lg:px-8">
                <div class="rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">Предимства</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">За специалисти, които искат да изглеждат сериозно онлайн.</h2>
                    <div class="mt-7 grid gap-3">
                        @foreach ($benefits as $benefit)
                            <div class="flex gap-3 rounded-2xl bg-white/80 p-4 text-sm font-bold text-slate-700 shadow-sm">
                                <span class="font-black text-blue-600">✓</span>
                                {{ $benefit }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Категории специалисти</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">Покажи експертизата си ясно.</h2>
                    <div class="mt-7 flex flex-wrap gap-3">
                        @foreach ($categories as $category)
                            <span class="rounded-full border border-white/80 bg-white/80 px-4 py-2 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-blue-600 hover:to-fuchsia-500 hover:text-white">
                                {{ $category }}
                            </span>
                        @endforeach
                    </div>
                    <div class="mt-8 rounded-3xl border border-blue-100 bg-blue-50/70 p-5">
                        <h3 class="text-lg font-black">Планове за присъствие и растеж</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">Започни със Standard профил или използвай Premium за по-силна видимост, badge и по-добро позициониране.</p>
                        <a href="{{ route('plans') }}" class="mt-4 inline-flex text-sm font-black text-blue-700 hover:text-violet-700">Виж плановете →</a>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <div class="rounded-[1.65rem] border border-white/70 bg-white/76 p-5 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">FAQ</p>
                    <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">Често задавани въпроси</h2>
                    <div class="mt-7 grid gap-4 md:grid-cols-2">
                        @foreach ($faqs as $faq)
                            <article class="rounded-3xl border border-slate-100 bg-white/78 p-5 shadow-sm">
                                <h3 class="font-black">{{ $faq['question'] }}</h3>
                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $faq['answer'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-[1440px] px-4 pb-12 pt-6 sm:px-6 sm:pb-16 sm:pt-8 lg:px-8">
                <div class="rounded-[1.65rem] border border-white/70 bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-5 text-white shadow-2xl shadow-violet-500/20 sm:rounded-[2rem] sm:p-8">
                    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-white/70">Започни с BON</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight sm:text-5xl">Създай профил, който работи за доверието ти.</h2>
                        </div>
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F] shadow-xl sm:w-auto">
                            Създай профил
                        </a>
                    </div>
                </div>
            </section>

            @include('partials.public-footer')
        </div>
    </main>
</body>
</html>

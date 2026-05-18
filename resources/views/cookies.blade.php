<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Политика за бисквитки на FixNow.bg - как използваме сесии, защитни cookies и основни analytics данни за платформата.">
    <title>Политика за бисквитки | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_15%,rgba(34,211,238,0.16),transparent_30%),radial-gradient(circle_at_85%_20%,rgba(168,85,247,0.16),transparent_32%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black">F</span>
                <span class="text-xl font-black">FixNow.bg</span>
            </a>
            <a href="{{ route('privacy') }}" class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white hover:bg-white/15">Поверителност</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <p class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-black text-cyan-100">Cookies</p>
            <h1 class="mt-6 text-4xl font-black leading-tight sm:text-5xl">Политика за бисквитки</h1>
            <p class="mt-4 max-w-3xl text-base leading-7 text-white/70">
                Тази страница обяснява как FixNow.bg използва бисквитки и подобни технологии, за да поддържа вход, сигурност, заявки, analytics и стабилна работа на платформата.
            </p>
        </section>

        <section class="mt-6 grid gap-5">
            @foreach([
                [
                    'title' => '1. Какво са бисквитките',
                    'body' => 'Бисквитките са малки файлове или записи, които браузърът пази, за да може сайтът да разпознава сесията, предпочитанията и защитните токени при следващо зареждане.'
                ],
                [
                    'title' => '2. Задължителни бисквитки',
                    'body' => 'FixNow.bg използва Laravel session и CSRF cookies за вход, защита на формите, поддържане на потребителска сесия и предотвратяване на злоупотреби. Без тях част от сайта няма да работи коректно.'
                ],
                [
                    'title' => '3. Analytics и измерване на стойност',
                    'body' => 'Платформата може да записва събития като преглед на профил на изпълнител, клик към телефон, сайт или заявка. Тези данни помагат на изпълнителите да виждат реалната стойност от FixNow.bg. Когато е възможно, IP адресите се съхраняват като хеширани идентификатори.'
                ],
                [
                    'title' => '4. Stripe и външни услуги',
                    'body' => 'При плащане или управление на абонамент Stripe може да използва свои cookies и технологии според собствените си политики. FixNow.bg не съхранява пълни данни за банкови карти.'
                ],
                [
                    'title' => '5. Управление на бисквитките',
                    'body' => 'Можете да ограничите или изтриете бисквитките през настройките на браузъра си. Имайте предвид, че това може да наруши входа, checkout flow-а, формите и управлението на профил.'
                ],
                [
                    'title' => '6. Промени',
                    'body' => 'Тази политика може да бъде обновявана при добавяне на нови функции, analytics инструменти или промени в техническата инфраструктура.'
                ],
            ] as $section)
                <article class="rounded-3xl border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/70">{{ $section['body'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-6 rounded-3xl border border-amber-300/20 bg-amber-300/10 p-6 text-sm leading-7 text-amber-50">
            <strong>Важно:</strong> Този текст е базова launch версия и не представлява индивидуален правен съвет. Преди официално пускане е препоръчително да бъде прегледан от юрист.
        </section>
    </main>

    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Политика за поверителност на BON - какви данни събираме, защо ги използваме и как се обработват плащанията чрез Stripe.">
    <title>Политика за поверителност | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_18%,rgba(251,146,60,0.16),transparent_30%),radial-gradient(circle_at_84%_12%,rgba(245,158,11,0.16),transparent_32%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <p class="inline-flex rounded-full border border-orange-300/20 bg-orange-300/10 px-4 py-2 text-sm font-black text-orange-100">Поверителност</p>
            <h1 class="mt-6 text-4xl font-black leading-tight sm:text-5xl">Политика за поверителност</h1>
            <p class="mt-4 max-w-3xl text-base leading-7 text-white/70">
                Тази политика обяснява как BON събира и използва данни, за да предоставя профили на бизнеси, заявки, абонаменти и комуникация между потребители и бизнеси.
            </p>
        </section>

        <section class="mt-6 grid gap-5">
            @foreach([
                [
                    'title' => '1. Какви данни събираме',
                    'body' => 'Можем да събираме име, email, телефон, град, бизнес информация, категории, описание, снимки, заявки за оферта, отзиви, техническа информация за сесии, IP адрес или хеширани идентификатори за сигурност и analytics.'
                ],
                [
                    'title' => '2. Защо използваме данните',
                    'body' => 'Данните се използват за създаване и управление на профили, показване на публична бизнес информация, обработка на заявки, контакт между клиент и бизнес, управление на абонаменти, сигурност, поддръжка и подобряване на платформата.'
                ],
                [
                    'title' => '3. Заявки и комуникация',
                    'body' => 'Когато потребител изпрати заявка за оферта, данните от заявката могат да бъдат показани на администратор и на подходящи бизнеси, към които системата е назначила заявката.'
                ],
                [
                    'title' => '4. Плащания чрез Stripe',
                    'body' => 'Плащанията за платени планове се обработват чрез Stripe. BON не съхранява пълни данни за банкови карти. Можем да пазим Stripe customer/subscription идентификатори, статус на абонамент и план, за да управляваме достъпа до функциите.'
                ],
                [
                    'title' => '5. Cookies и сесии',
                    'body' => 'Платформата използва стандартни cookies и session механизми на Laravel за вход, сигурност, CSRF защита и запазване на потребителска сесия. В бъдеще могат да бъдат добавени допълнителни analytics или marketing cookies при нужда.'
                ],
                [
                    'title' => '6. Споделяне с трети страни',
                    'body' => 'Данни могат да се обработват от доставчици като Stripe за плащания и email инфраструктура за известия. Не продаваме лични данни на трети страни.'
                ],
                [
                    'title' => '7. Права на потребителя',
                    'body' => 'Потребителите могат да поискат достъп, корекция или изтриване на лични данни, когато това е приложимо. За профили на бизнеси някои данни може да се пазят за счетоводни, правни или сигурностни цели.'
                ],
                [
                    'title' => '8. Контакт',
                    'body' => 'За въпроси относно лични данни, профили или заявки можете да се свържете с нас на hello@bon.bg.'
                ],
            ] as $section)
                <article class="rounded-3xl border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/70">{{ $section['body'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-6 rounded-3xl border border-amber-300/20 bg-amber-300/10 p-6 text-sm leading-7 text-amber-50">
            <strong>Важно:</strong> Този текст е базова версия за стартова публична страница и не представлява индивидуален правен съвет. Преди официално пускане е препоръчително да бъде прегледан от юрист.
        </section>
    </main>

    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

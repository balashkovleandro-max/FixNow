<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Общи условия за използване на FixNow.bg - платформа за локални услуги, профили на изпълнители, заявки и абонаментни планове.">
    <title>Общи условия | FixNow.bg</title>
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
            <a href="{{ route('contact') }}" class="rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white hover:bg-white/15">Контакт</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-10">
            <p class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-black text-cyan-100">Правна информация</p>
            <h1 class="mt-6 text-4xl font-black leading-tight sm:text-5xl">Общи условия</h1>
            <p class="mt-4 max-w-3xl text-base leading-7 text-white/70">
                Тези общи условия описват основните правила за използване на FixNow.bg като платформа за откриване на услуги, профили на изпълнители и директни запитвания.
            </p>
        </section>

        <section class="mt-6 grid gap-5">
            @foreach([
                [
                    'title' => '1. Какво е FixNow.bg',
                    'body' => 'FixNow.bg е онлайн платформа, която свързва потребители, търсещи услуги, с изпълнители и професионалисти. Платформата предоставя публични профили на изпълнители, търсене, категории, заявки за оферта, отзиви и инструменти за управление на профил.'
                ],
                [
                    'title' => '2. Роля на платформата',
                    'body' => 'FixNow.bg улеснява връзката между клиент и изпълнител, но не е страна по договора, сделката, офертата или конкретното изпълнение на услугата. Условията, цените, сроковете и качеството на услугата се договарят директно между клиента и избрания изпълнител.'
                ],
                [
                    'title' => '3. Отговорност на изпълнителите',
                    'body' => 'Всеки бизнес отговаря за точността, актуалността и законосъобразността на информацията в своя профил, включително име, услуги, градове, цени, снимки, работно време, контакти и публични твърдения.'
                ],
                [
                    'title' => '4. Премахване или скриване на профили',
                    'body' => 'FixNow.bg може да скрива, ограничава или премахва профили, които съдържат невярна информация, подвеждащо съдържание, неподходящи материали, злоупотреби или нарушават правилата на платформата.'
                ],
                [
                    'title' => '5. Платени планове и Stripe',
                    'body' => 'Платените планове Standard и Premium се управляват чрез Stripe. Абонаментът се активира след успешно плащане и потвърждение от Stripe. Потребителят може да управлява абонамента си през страницата за план и Stripe Customer Portal, когато е наличен.'
                ],
                [
                    'title' => '6. Trial, активност и публична видимост',
                    'body' => 'Профилите на изпълнители могат да имат пробен период или активен абонамент. Профили със статус expired или cancelled може да бъдат скрити от публичното търсене, списъци, класации и директни публични страници.'
                ],
                [
                    'title' => '7. Отзиви, заявки и съдържание от потребители',
                    'body' => 'Потребителите могат да изпращат заявки и отзиви. FixNow.bg може да модерира, отхвърля или премахва съдържание, което е обидно, подвеждащо, спам или нарушава правата на други лица.'
                ],
                [
                    'title' => '8. Промени в условията',
                    'body' => 'FixNow.bg може да актуализира тези условия при развитие на платформата, добавяне на нови функции или промени в правни и технически изисквания.'
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

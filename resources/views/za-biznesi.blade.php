<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>За изпълнители | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_16%_12%,rgba(34,211,238,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(168,85,247,0.20),transparent_32%),linear-gradient(180deg,#030712_0%,#061426_52%,#020812_100%)]"></div>
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-cyan-300/50 to-transparent"></div>
    </div>

    <header class="sticky top-0 z-50 border-b border-white/10 bg-[#030712]/80 backdrop-blur-2xl">
        <div class="mx-auto flex max-w-[1500px] items-center justify-between px-4 py-4 sm:px-6 lg:px-12">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black shadow-lg shadow-blue-600/25">F</div>
                <span class="text-xl font-black">FixNow.bg</span>
            </a>

            <nav class="hidden items-center gap-7 lg:flex">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Начало</a>
                <a href="{{ url('/categories') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Категории</a>
                <a href="{{ route('services.index') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Услуги</a>
                <a href="{{ route('businesses.index') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Изпълнители</a>
                <a href="{{ route('request.service') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Заяви оферта</a>
                <a href="{{ route('business.landing') }}" class="text-sm font-black text-cyan-200">За изпълнители</a>
            </nav>

            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-bold text-white/75 hover:bg-white/10 sm:inline-flex">Вход</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-600/25">Стани изпълнител</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-600/25">Моето табло</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_72%_24%,rgba(59,130,246,0.26),transparent_26%),radial-gradient(circle_at_88%_42%,rgba(168,85,247,0.20),transparent_28%)]"></div>
            <div class="mx-auto grid max-w-[1500px] gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1fr_520px] lg:px-12 lg:py-20">
                <div class="max-w-4xl">
                    <p class="mb-5 inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-black text-cyan-100">За майстори, сервизи, салони, авто услуги и локални професионалисти</p>
                    <h1 class="text-4xl font-black leading-tight sm:text-6xl lg:text-7xl">
                        Получавайте повече клиенти от вашия град чрез FixNow
                    </h1>
                    <p class="mt-6 max-w-3xl text-lg leading-8 text-white/70">
                        Създайте професионален профил на изпълнител, показвайте се в търсене, получавайте директни обаждания и заявки, и следете как клиентите реагират на профила ви.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-7 py-4 font-black text-white shadow-lg shadow-blue-600/25">Стани изпълнител</a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-7 py-4 font-black text-white shadow-lg shadow-blue-600/25">Към таблото</a>
                        @endauth
                        <a href="#plans" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-7 py-4 font-black text-white hover:bg-white/10">Виж плановете</a>
                        <a href="{{ route('request.service') }}" class="inline-flex items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-300/10 px-7 py-4 font-black text-cyan-100 hover:bg-cyan-300/15">Виж как клиентите заявяват оферта</a>
                    </div>

                    <div class="mt-10 grid gap-3 sm:grid-cols-3">
                        @foreach([
                            ['value' => '30 дни', 'label' => 'стартов trial'],
                            ['value' => '24/7', 'label' => 'публичен профил'],
                            ['value' => '0%', 'label' => 'комисиона към FixNow'],
                        ] as $stat)
                            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                                <p class="text-3xl font-black text-cyan-200">{{ $stat['value'] }}</p>
                                <p class="mt-2 text-sm text-white/60">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-[28px] border border-cyan-300/20 bg-cyan-300/10 p-5">
                        <p class="font-black text-cyan-100">FixNow.bg стартира поетапно</p>
                        <p class="mt-2 text-sm leading-6 text-white/70">В момента добавяме първите проверени изпълнители по градове и категории. Ранните профили получават повече време да изградят доверие, отзиви и видимост преди масовия трафик.</p>
                    </div>
                </div>

                <div class="rounded-[34px] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/30 backdrop-blur-2xl">
                    <div class="rounded-[28px] border border-white/10 bg-slate-950/70 p-5">
                        <div class="h-48 rounded-3xl bg-gradient-to-br from-cyan-400/20 via-blue-500/10 to-violet-600/25 p-5">
                            <div class="flex justify-between gap-3">
                                <span class="rounded-full bg-violet-400/15 px-3 py-1 text-xs font-black text-violet-100">Premium</span>
                                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100">Потвърден</span>
                            </div>
                        </div>
                        <div class="-mt-10 px-2">
                            <div class="flex h-20 w-20 items-center justify-center rounded-3xl border-4 border-slate-950 bg-gradient-to-br from-cyan-400 to-violet-600 text-3xl font-black">F</div>
                            <h2 class="mt-4 text-2xl font-black">Вашият профил на изпълнител</h2>
                            <p class="mt-2 text-sm leading-6 text-white/60">Категории, градове, услуги, снимки, контактни бутони и доверителни badges на едно място.</p>
                        </div>
                        <div class="mt-5 grid gap-3">
                            @foreach(['Показване в локално търсене', 'Директни обаждания', 'Статистика за интереса'] as $item)
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-white/75">{{ $item }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-12">
            <div class="mb-6">
                <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-200/80">Как работи</p>
                <h2 class="mt-3 text-3xl font-black sm:text-5xl">От профил до реално запитване</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                @foreach([
                    ['step' => '01', 'title' => 'Създавате профил', 'text' => 'Регистрирате профил на изпълнител и добавяте основна информация, контакти и категория.'],
                    ['step' => '02', 'title' => 'Избирате градове и категории', 'text' => 'Определяте къде работите и какви услуги предлагате, според лимита на плана.'],
                    ['step' => '03', 'title' => 'Клиентите ви намират', 'text' => 'Хората откриват профила ви в търсене и се свързват директно чрез телефон или запитване.'],
                ] as $item)
                    <div class="rounded-[30px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                        <p class="text-4xl font-black text-cyan-200">{{ $item['step'] }}</p>
                        <h3 class="mt-5 text-2xl font-black">{{ $item['title'] }}</h3>
                        <p class="mt-3 leading-7 text-white/60">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-12">
            <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-200/80">Ползи</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-5xl">Защо изпълнителите имат причина да плащат</h2>
                    <p class="mt-5 text-base leading-8 text-white/65">FixNow не е обикновен directory. Целта е да превърне профила ви в канал за видимост, доверие и директни клиентски намерения. Активните Standard и Premium абонаменти се показват публично, а неактивните профили се скриват от резултатите.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach([
                        'Показване в локално търсене',
                        'Директни обаждания',
                        'Професионален публичен профил',
                        'Verified badge след одобрение',
                        'Premium предимство и по-високо позициониране',
                        'Статистика за преглеждания и кликове',
                    ] as $benefit)
                        <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-300/10 text-cyan-100">✓</div>
                            <p class="font-black">{{ $benefit }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="plans" class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-12">
            <div class="mb-7 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-200/80">Планове</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-5xl">Standard или Premium</h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-white/60">Standard дава стабилен публичен профил. Premium добавя по-високо позициониране, препоръчан badge, повече градове/услуги/снимки и повече точки за оферти.</p>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Standard</p>
                    <div class="mt-4 flex items-end gap-2">
                        <span class="text-5xl font-black">18.99 €</span>
                        <span class="pb-2 text-white/50">/месец</span>
                    </div>
                    <div class="mt-6 grid gap-3">
                        @foreach(['до 2 града', 'до 2 категории/услуги', 'до 5 снимки', 'нормално показване', 'публичен профил на изпълнител'] as $feature)
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3 text-sm font-bold text-white/75">{{ $feature }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="relative rounded-[32px] border border-violet-300/25 bg-gradient-to-br from-violet-500/16 via-blue-500/10 to-cyan-400/12 p-6 shadow-2xl shadow-violet-950/25 backdrop-blur-xl">
                    <span class="absolute right-5 top-5 rounded-full bg-violet-400/20 px-3 py-1 text-xs font-black text-violet-100">Препоръчан</span>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-200">Premium</p>
                    <div class="mt-4 flex items-end gap-2">
                        <span class="text-5xl font-black">24.99 €</span>
                        <span class="pb-2 text-white/50">/месец</span>
                    </div>
                    <div class="mt-6 grid gap-3">
                        @foreach(['до 5 града', 'до 5 категории/услуги', 'до 15 снимки', 'Premium/Препоръчан badge', 'по-високо показване', 'показване в “Препоръчани изпълнители”', 'галерия с проекти', 'приоритетна поддръжка'] as $feature)
                            <p class="rounded-2xl bg-slate-950/45 px-4 py-3 text-sm font-bold text-white/75">{{ $feature }}</p>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-5 rounded-[28px] border border-amber-300/20 bg-amber-300/10 p-5">
                <p class="font-black text-amber-100">Точки за оферти</p>
                <p class="mt-2 text-sm leading-6 text-white/70">Standard включва 30 точки за оферти месечно, Premium включва 90 точки, а trial стартът дава 45 точки, за да усетите стойността на платформата.</p>
            </div>

            <div class="mt-5 rounded-[28px] border border-rose-300/20 bg-rose-400/10 p-5">
                <p class="font-black text-rose-100">Публична видимост само при активен статус</p>
                <p class="mt-2 text-sm leading-6 text-white/70">След trial периода профилът трябва да има активен абонамент, за да остане видим в търсене, категории, препоръчани секции и публични резултати. Това поддържа качеството на marketplace-а за клиентите.</p>
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-12">
            <div class="grid gap-5 rounded-[34px] border border-cyan-300/20 bg-gradient-to-br from-cyan-400/12 via-blue-500/10 to-violet-600/15 p-6 shadow-xl shadow-blue-950/20 backdrop-blur-xl lg:grid-cols-[1fr_auto] lg:items-center sm:p-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-200/80">Lead generation</p>
                    <h2 class="mt-3 text-3xl font-black">FixNow събира реални заявки за услуги</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-white/70">Клиентите могат да изпратят заявка с град, категория, описание, спешност и бюджет. Това превръща профила ви от обикновен listing в канал за директни потенциални клиенти.</p>
                </div>
                <a href="{{ route('request.service') }}" class="rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-7 py-4 text-center font-black text-white shadow-lg shadow-blue-600/25">Виж формата</a>
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-12">
            <div class="rounded-[34px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-200/80">FAQ</p>
                <h2 class="mt-3 text-3xl font-black">Често задавани въпроси</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['q' => 'Колко време отнема да се добави изпълнител?', 'a' => 'Регистрацията отнема няколко минути. След това можете да попълните профила постепенно.'],
                        ['q' => 'Мога ли да започна безплатно/с trial?', 'a' => 'Да. Всеки изпълнител започва с 30-дневен trial, след което е нужен активен план за публична видимост.'],
                        ['q' => 'Какво получавам с Premium?', 'a' => 'Premium дава повече градове, повече услуги, повече снимки, badge и по-високо показване в резултатите.'],
                        ['q' => 'Как се потвърждава изпълнител?', 'a' => 'Verified badge се добавя след административна проверка на профила и основната информация.'],
                        ['q' => 'Мога ли да сменя плана?', 'a' => 'Да. Управлението на планове и плащания става директно от панела на изпълнител.'],
                    ] as $faq)
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-lg font-black">{{ $faq['q'] }}</p>
                            <p class="mt-3 text-sm leading-6 text-white/60">{{ $faq['a'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-10 sm:px-6 lg:px-12">
            <div class="overflow-hidden rounded-[34px] border border-white/10 bg-gradient-to-br from-cyan-400/16 via-blue-500/12 to-violet-600/18 p-6 shadow-2xl shadow-black/25 backdrop-blur-xl sm:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-100">Стартово предимство</p>
                        <h2 class="mt-3 max-w-3xl text-3xl font-black sm:text-5xl">Първите изпълнители получават стартово предимство</h2>
                        <p class="mt-5 max-w-2xl text-base leading-8 text-white/70">Колкото по-рано изградите профила си, толкова по-бързо започвате да трупате видимост, доверие и активност.</p>
                    </div>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex justify-center rounded-2xl bg-white px-7 py-4 font-black text-slate-950 hover:bg-cyan-50">Стани изпълнител сега</a>
                    @endguest
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex justify-center rounded-2xl bg-white px-7 py-4 font-black text-slate-950 hover:bg-cyan-50">Към таблото</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

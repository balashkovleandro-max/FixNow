<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Планове и цени | FixNow.bg</title>
    <meta name="description" content="Планове за изпълнители във FixNow.bg: Standard 18.99 €/месец и Premium 24.99 €/месец с профил, ревюта, заявки, точки за оферти и Premium видимост.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_10%,rgba(34,211,238,0.16),transparent_32%),radial-gradient(circle_at_82%_12%,rgba(168,85,247,0.16),transparent_30%),linear-gradient(180deg,#020812,#061426_48%,#020812)]"></div>

    <header class="border-b border-white/10 bg-slate-950/55 backdrop-blur-xl">
        <nav class="mx-auto flex max-w-[1440px] items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 text-xl font-black">F</span>
                <span class="text-xl font-black">FixNow.bg</span>
            </a>

            <div class="hidden items-center gap-7 text-sm font-bold text-white/70 lg:flex">
                <a href="{{ url('/') }}" class="hover:text-white">Начало</a>
                <a href="{{ route('businesses.index') }}" class="hover:text-white">Намери изпълнител</a>
                <a href="{{ route('request.service') }}" class="hover:text-white">Пусни заявка</a>
                <a href="{{ route('business.landing') }}" class="hover:text-white">За изпълнители</a>
                <a href="{{ route('plans') }}" class="text-cyan-200">Планове</a>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ auth()->user()->role === 'business' ? route('business.billing') : route('dashboard') }}" class="hidden min-h-11 items-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/80 hover:bg-white/10 sm:inline-flex">Моят план</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-4 py-2 text-sm font-black text-white shadow-lg shadow-blue-600/20">Табло</a>
                @else
                    <a href="{{ route('login') }}" class="hidden min-h-11 items-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/80 hover:bg-white/10 sm:inline-flex">Вход</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-4 py-2 text-sm font-black text-white shadow-lg shadow-blue-600/20">Стани изпълнител</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-[1440px] px-4 py-10 sm:px-6 lg:px-8">
        @if($errors->has('stripe'))
            <div class="mb-6 rounded-3xl border border-rose-300/25 bg-rose-400/10 p-5 text-rose-50">
                {{ $errors->first('stripe') }}
            </div>
        @endif

        <section class="grid gap-8 py-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-14">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.28em] text-cyan-200/80">Абонаменти за изпълнители</p>
                <h1 class="mt-5 max-w-4xl text-3xl font-black leading-tight sm:text-6xl">
                    Ясни планове за видимост, заявки и оферти във FixNow.bg
                </h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-white/70">
                    Започнете с 30-дневен trial, изградете професионален профил и използвайте точки, за да изпращате оферти към подходящи клиентски заявки.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 text-center font-black text-white shadow-xl shadow-blue-600/25">Стартирай безплатно</a>
                    <a href="{{ route('business.landing') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-6 py-4 text-center font-black text-white hover:bg-white/20">Виж ползите</a>
                </div>
            </div>

            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
                <div class="rounded-[28px] border border-cyan-300/20 bg-slate-950/55 p-6">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200">Trial старт</p>
                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                            <p class="text-3xl font-black">30</p>
                            <p class="mt-2 text-sm text-white/60">дни пробен период</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                            <p class="text-3xl font-black">45</p>
                            <p class="mt-2 text-sm text-white/60">точки за оферти</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                            <p class="text-3xl font-black">3</p>
                            <p class="mt-2 text-sm text-white/60">точки за оферта</p>
                        </div>
                    </div>
                    <p class="mt-6 text-sm leading-6 text-white/60">Premium получава по-високо позициониране и повече точки. Standard остава стабилен стартов план за публичен профил, ревюта и директен контакт.</p>
                </div>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            @foreach(['standard', 'premium'] as $planKey)
                @php
                    $plan = $plans[$planKey];
                    $points = $planKey === 'premium' ? 90 : 30;
                @endphp
                <article class="relative overflow-hidden rounded-[32px] border {{ $planKey === 'premium' ? 'border-violet-300/40 bg-violet-400/12 shadow-violet-950/35 ring-1 ring-violet-300/20' : 'border-cyan-300/20 bg-white/10 shadow-black/20' }} p-5 shadow-2xl backdrop-blur-xl sm:p-8">
                    @if($planKey === 'premium')
                        <div class="absolute right-5 top-5 rounded-full bg-gradient-to-r from-cyan-400 to-violet-600 px-4 py-2 text-xs font-black shadow-lg shadow-violet-600/20">Препоръчан</div>
                    @endif
                    <p class="text-sm font-black uppercase tracking-[0.25em] {{ $planKey === 'premium' ? 'text-violet-200' : 'text-cyan-200' }}">{{ $plan['label'] }}</p>
                    <div class="mt-5 flex flex-wrap items-end gap-2">
                        <span class="text-[2.75rem] font-black leading-none sm:text-5xl">{{ number_format($plan['price'], 2, ',', ' ') }} €</span>
                        <span class="pb-2 text-white/60">/месец</span>
                    </div>
                    <p class="mt-2 text-sm font-bold {{ $planKey === 'premium' ? 'text-violet-100' : 'text-cyan-100' }}">
                        {{ $planKey === 'premium' ? 'Premium видимост, badge и приоритет при matching' : 'Публичен профил и стабилно присъствие в търсене' }}
                    </p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-2xl font-black">{{ $plan['city_limit'] }}</p>
                            <p class="text-sm text-white/60">града</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-2xl font-black">{{ $plan['category_limit'] }}</p>
                            <p class="text-sm text-white/60">категории</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-2xl font-black">{{ $plan['photo_limit'] }}</p>
                            <p class="text-sm text-white/60">снимки</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-2xl font-black">{{ $points }}</p>
                            <p class="text-sm text-white/60">точки/месец</p>
                        </div>
                    </div>
                    <ul class="mt-6 grid gap-3 text-sm leading-6 text-white/70">
                        @foreach($plan['features'] as $feature)
                            <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-cyan-300"></span><span>{{ $feature }}</span></li>
                        @endforeach
                    </ul>
                    <div class="mt-8">
                        @auth
                            @if(auth()->user()->role === 'business')
                                <form action="{{ route('business.billing.checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $planKey }}">
                                    <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl {{ $planKey === 'premium' ? 'bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600' : 'border border-white/10 bg-white/10 hover:bg-white/20' }} px-5 py-4 text-center font-black text-white">
                                        {{ $planKey === 'premium' ? 'Вземи Premium' : 'Избери Standard' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl {{ $planKey === 'premium' ? 'bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600' : 'border border-white/10 bg-white/10 hover:bg-white/20' }} px-5 py-4 text-center font-black text-white">
                                    Към таблото
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl {{ $planKey === 'premium' ? 'bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600' : 'border border-white/10 bg-white/10 hover:bg-white/20' }} px-5 py-4 text-center font-black text-white">
                                {{ $planKey === 'premium' ? 'Вземи Premium' : 'Стартирай безплатно' }}
                            </a>
                        @endauth
                    </div>
                </article>
            @endforeach
        </section>

        <section class="mt-10 grid gap-5 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">Сравнение</p>
                <h2 class="mt-3 text-3xl font-black">Standard срещу Premium</h2>
                <p class="mt-3 text-white/60">Premium не е неограничен план. Той дава по-големи лимити, по-добро подреждане и повече точки за оферти, без да променя основната абонаментна цена.</p>
            </div>
            <div class="overflow-hidden rounded-[32px] border border-white/10 bg-white/10 backdrop-blur-xl">
                <div class="grid grid-cols-3 border-b border-white/10 bg-slate-950/45 text-sm font-black">
                    <div class="p-4">Функция</div>
                    <div class="p-4 text-cyan-100">Standard</div>
                    <div class="p-4 text-violet-100">Premium</div>
                </div>
                @foreach([
                    ['Градове', 'до 2', 'до 5'],
                    ['Категории/услуги', 'до 2', 'до 5'],
                    ['Снимки', 'до 5', 'до 15'],
                    ['Точки за оферти', '30/месец', '90/месец'],
                    ['Подреждане', 'нормално', 'по-високо'],
                    ['Заявки', 'ако системата match-не', 'приоритет при matching'],
                    ['Badge', '-', 'Premium/Препоръчан'],
                ] as $row)
                    <div class="grid grid-cols-3 border-b border-white/10 text-sm text-white/70 last:border-b-0">
                        <div class="p-4 font-bold text-white">{{ $row[0] }}</div>
                        <div class="p-4">{{ $row[1] }}</div>
                        <div class="p-4">{{ $row[2] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-10 grid gap-5 lg:grid-cols-2">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 backdrop-blur-xl sm:p-8">
                <h2 class="text-3xl font-black">Често задавани въпроси</h2>
                <div class="mt-6 grid gap-4">
                    @foreach([
                        ['Има ли trial?', 'Да. Всеки изпълнител започва с 30-дневен пробен период и 45 точки за оферти.'],
                        ['Premium активира ли се автоматично?', 'Не при натискане на бутона. Планът се активира само след успешен Stripe webhook.'],
                        ['Колко точки струва една оферта?', 'Една изпратена оферта струва 3 точки. Standard включва 30 точки месечно, Premium - 90 точки месечно.'],
                        ['Има ли доплащане за градове?', 'Не на този етап. Градовете служат за лимити, филтър, релевантност и насочване на заявки.'],
                    ] as $faq)
                        <details class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 open:border-cyan-300/25">
                            <summary class="cursor-pointer list-none font-black">{{ $faq[0] }}</summary>
                            <p class="mt-3 text-sm leading-6 text-white/60">{{ $faq[1] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[32px] border border-cyan-300/20 bg-gradient-to-br from-cyan-400/12 via-blue-500/10 to-violet-600/12 p-6 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-100">Следваща стъпка</p>
                <h2 class="mt-4 text-3xl font-black sm:text-4xl">Създайте профил и вижте първите релевантни заявки.</h2>
                <p class="mt-4 text-white/70">FixNow показва лимити, статус, точки и CTA за checkout в панела на изпълнител. Premium не се активира без успешен Stripe webhook.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-white px-6 py-4 text-center font-black text-slate-950">Стани изпълнител</a>
                    <a href="{{ route('request.service') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-6 py-4 text-center font-black text-white hover:bg-white/20">Пусни заявка</a>
                </div>
            </div>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

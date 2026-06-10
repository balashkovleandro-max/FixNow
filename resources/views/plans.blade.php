@php
    $navItems = [
        ['label' => 'Начало', 'href' => route('home')],
        ['label' => 'За бизнеси', 'href' => route('business.landing')],
        ['label' => 'Инструменти', 'href' => route('bon.tools')],
        ['label' => 'Планове', 'href' => route('plans')],
    ];

    $pricingPlans = [
        [
            'key' => 'standard',
            'name' => 'Standard',
            'audience' => 'Базови BON инструменти',
            'price' => '18.99 € / месец',
            'description' => 'За бизнеси, които искат професионално онлайн присъствие и базови инструменти за видимост.',
            'features' => [
                'Професионален BON профил',
                'Visibility Score за онлайн присъствието',
                'Основен финансов анализ',
                'Business Health Score',
                'Базови статистики за профила',
                'Отзиви и репутация',
                'Месечен mini report',
                'Основни препоръки за подобрение',
                'Достъп до базовите BON инструменти',
            ],
            'button' => 'Избери Standard',
            'accent' => 'blue',
            'recommended' => false,
        ],
        [
            'key' => 'premium',
            'name' => 'Premium',
            'audience' => 'Препоръчан план',
            'price' => '24.99 € / месец',
            'description' => 'За бизнеси, които искат повече анализ, по-добра видимост и конкретни препоръки за растеж.',
            'features' => [
                'Всичко от Standard',
                'Разширен финансов анализ',
                'По-подробен Business Health Score',
                'Калкулатор “Колко клиенти ми трябват?”',
                'Калкулатор за ценообразуване',
                'Разширени статистики',
                'Месечен бизнес доклад',
                'Premium препоръки за растеж',
                'Premium / Препоръчан badge',
                'По-добра видимост в BON',
                'Приоритетна поддръжка',
                'Подготвена логика за бъдещи AI/business advisor препоръки',
            ],
            'button' => 'Избери Premium',
            'accent' => 'purple',
            'recommended' => true,
        ],
    ];

    $faqs = [
        [
            'question' => 'За какво плаща бизнесът?',
            'answer' => 'За професионален BON профил, инструменти за видимост, финансов анализ, бизнес здраве, репутация, месечни доклади и препоръки според избрания план.',
        ],
        [
            'question' => 'Какво получава бизнесът?',
            'answer' => 'Бизнесът получава по-ясна картина за онлайн присъствието, финансите, репутацията и следващите действия за растеж.',
        ],
        [
            'question' => 'Кога Premium възможностите са активни?',
            'answer' => 'Само когато абонаментът е активен или trialing. Неплатени, expired, cancelled или failed профили не получават Premium предимства.',
        ],
        [
            'question' => 'Мога ли да сменя плана?',
            'answer' => 'Да. Ако вече имате активен Stripe абонамент, управлението на промени минава през Customer Portal, за да няма паралелни абонаменти.',
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Планове | BON Business Operating Network</title>
    <meta name="description" content="Абонаментни планове BON Standard и Premium за бизнес видимост, финансов анализ, бизнес здраве, репутация, месечни доклади и препоръки за растеж.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')

    <style>
        .bon-grid {
            background-image:
                linear-gradient(to right, rgba(37, 99, 235, .075) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, .075) 1px, transparent 1px);
            background-size: 72px 72px;
            mask-image: radial-gradient(circle at 50% 22%, black 0%, transparent 78%);
        }

        .bon-dot-field {
            background-image: radial-gradient(rgba(37, 99, 235, .34) 1.4px, transparent 1.4px);
            background-size: 16px 16px;
        }

        .bon-card {
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        }

        .bon-card:hover {
            transform: translateY(-6px);
        }
    </style>
</head>

<body class="antialiased">
    <main class="relative min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.98)_0%,rgba(248,250,255,.78)_42%,rgba(248,250,255,1)_100%)]"></div>
        <div class="bon-grid pointer-events-none absolute inset-0 opacity-[.38]"></div>
        <div class="pointer-events-none absolute -top-40 left-[-12rem] h-[35rem] w-[35rem] rounded-full bg-blue-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-40 right-[-10rem] h-[35rem] w-[35rem] rounded-full bg-fuchsia-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[18rem] h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-violet-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-18rem] left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="bon-dot-field pointer-events-none absolute left-6 top-56 hidden h-40 w-36 opacity-30 lg:block"></div>
        <div class="bon-dot-field pointer-events-none absolute bottom-10 right-6 hidden h-40 w-36 opacity-25 lg:block" style="background-image: radial-gradient(rgba(236,72,153,.36) 1.4px, transparent 1.4px);"></div>

        <div class="relative z-10 px-4 pb-16 sm:px-6 lg:px-8">
            <header class="mx-auto mt-3 max-w-[1440px]">
                <div class="flex min-h-[74px] items-center justify-between rounded-[1.75rem] border border-white/70 bg-white/75 px-4 py-3 shadow-[0_24px_80px_rgba(30,41,100,.09)] backdrop-blur-2xl sm:px-6">
                    <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 sm:gap-4">
                        <div class="relative flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white shadow-xl shadow-violet-500/25">
                            <span class="absolute inset-0 rounded-2xl bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,.42),transparent_38%)]"></span>
                            <span class="relative z-10">B</span>
                        </div>
                        <div class="min-w-0 leading-tight">
                            <div class="text-[23px] font-black tracking-tight text-[#070B1F]">BON</div>
                            <div class="hidden truncate text-sm font-medium text-slate-500 sm:block">Business Operating Network</div>
                        </div>
                    </a>

                    <nav class="hidden items-center gap-9 xl:gap-10 lg:flex">
                        @foreach ($navItems as $item)
                            <a href="{{ $item['href'] }}" class="text-[15px] font-semibold text-[#11183B] transition hover:-translate-y-0.5 hover:text-blue-600">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('login') }}" class="hidden rounded-2xl border border-slate-200/80 bg-white/70 px-5 py-3 text-sm font-bold text-[#070B1F] shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:inline-flex">
                            Вход
                        </a>
                        <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-sm font-bold text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35 sm:px-6">
                            Регистрация
                        </a>
                    </div>
                </div>
            </header>

            <section class="mx-auto max-w-[1440px] pt-10 sm:pt-12 lg:pt-14">
                @if($errors->has('stripe'))
                    <div class="mx-auto mb-8 max-w-3xl rounded-[2rem] border border-rose-200/70 bg-white/80 p-5 text-sm font-semibold text-rose-600 shadow-2xl shadow-rose-900/5 backdrop-blur-2xl">
                        {{ $errors->first('stripe') }}
                    </div>
                @endif

                <div class="mx-auto max-w-4xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-200/70 bg-white/80 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm shadow-blue-900/5 backdrop-blur-xl">
                        <span class="text-violet-600">✦</span>
                        Планове
                    </div>

                    <h1 class="mt-5 text-[38px] font-black leading-[1.04] tracking-[-0.055em] text-[#070B1F] sm:text-[56px] lg:text-[70px]">
                        Избери какви инструменти отключваш в <span class="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">BON</span>.
                    </h1>

                    <p class="mx-auto mt-5 max-w-3xl text-[17px] leading-8 text-slate-600 sm:text-[19px]">
                        Standard и Premium са създадени за бизнеси, които искат анализ, ключови показатели,
                        по-добро онлайн присъствие, повече доверие и конкретни препоръки за растеж.
                    </p>
                </div>

                <section class="mx-auto mt-10 grid max-w-5xl items-stretch gap-5 lg:grid-cols-2">
                    @foreach ($pricingPlans as $plan)
                        @php
                            $isBlue = $plan['accent'] === 'blue';
                            $isPink = $plan['accent'] === 'pink';
                            $accentText = $isBlue ? 'text-blue-600' : ($isPink ? 'text-pink-500' : 'text-violet-600');
                            $buttonClass = $isBlue
                                ? 'from-blue-600 to-violet-600 shadow-blue-600/25 hover:shadow-blue-600/35'
                                : ($isPink
                                    ? 'from-fuchsia-500 to-pink-500 shadow-pink-500/25 hover:shadow-pink-500/35'
                                    : 'from-violet-600 to-fuchsia-500 shadow-violet-600/25 hover:shadow-violet-600/35');
                            $iconClass = $isBlue
                                ? 'from-blue-600 to-violet-600 shadow-blue-600/20'
                                : ($isPink
                                    ? 'from-fuchsia-500 to-pink-500 shadow-pink-500/20'
                                    : 'from-violet-600 to-fuchsia-500 shadow-violet-600/20');
                        @endphp

                        <article class="bon-card relative flex min-h-[560px] flex-col overflow-hidden rounded-[2rem] border {{ $plan['recommended'] ? 'border-blue-200/80 bg-white/85 shadow-[0_34px_90px_rgba(37,99,235,.16)] ring-1 ring-blue-200/60' : 'border-white/70 bg-white/75 shadow-[0_30px_80px_rgba(30,41,100,.10)]' }} p-6 backdrop-blur-2xl sm:p-7">
                            <div class="pointer-events-none absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent {{ $isPink ? 'via-pink-400/70' : ($isBlue ? 'via-blue-400/70' : 'via-violet-400/70') }} to-transparent"></div>

                            @if($plan['recommended'])
                                <div class="absolute right-5 top-5 rounded-full bg-gradient-to-r from-blue-600 to-violet-600 px-4 py-2 text-xs font-black text-white shadow-lg shadow-blue-600/20">
                                    Препоръчан
                                </div>
                            @endif

                            <div class="flex h-[52px] w-[52px] items-center justify-center rounded-2xl bg-gradient-to-br {{ $iconClass }} text-2xl font-black text-white shadow-xl">
                                {{ $isPink ? '♙' : ($isBlue ? '▥' : '✦') }}
                            </div>

                            <div class="mt-6 text-xs font-black uppercase tracking-[0.22em] {{ $accentText }}">
                                {{ $plan['audience'] }}
                            </div>

                            <h2 class="mt-3 text-3xl font-black tracking-tight text-[#070B1F]">
                                {{ $plan['name'] }}
                            </h2>

                            <div class="mt-5 text-[34px] font-black tracking-tight text-[#070B1F]">
                                {{ $plan['price'] }}
                            </div>

                            <p class="mt-4 text-base leading-7 text-slate-600">
                                {{ $plan['description'] }}
                            </p>

                            <ul class="mt-6 grid gap-3 text-sm leading-6 text-slate-600">
                                @foreach ($plan['features'] as $feature)
                                    <li class="flex gap-3">
                                        <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-gradient-to-br {{ $isPink ? 'from-pink-500 to-rose-500' : ($isBlue ? 'from-blue-600 to-violet-600' : 'from-violet-600 to-fuchsia-500') }}"></span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-auto pt-7">
                                @auth
                                    @if(auth()->user()->role === 'business')
                                        <form action="{{ route('business.billing.checkout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan" value="{{ $plan['key'] }}">
                                            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r {{ $buttonClass }} px-5 text-center text-sm font-black text-white shadow-xl transition hover:-translate-y-0.5">
                                                {{ $plan['button'] }} <span>→</span>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('business.landing') }}" class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r {{ $buttonClass }} px-5 text-center text-sm font-black text-white shadow-xl transition hover:-translate-y-0.5">
                                            Добави бизнес профил <span>→</span>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}" data-track="cta_business_signup" class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r {{ $buttonClass }} px-5 text-center text-sm font-black text-white shadow-xl transition hover:-translate-y-0.5">
                                        {{ $plan['button'] }} <span>→</span>
                                    </a>
                                @endauth
                            </div>
                        </article>
                    @endforeach
                </section>

                <section class="mx-auto mt-12 max-w-5xl rounded-[2rem] border border-white/70 bg-white/75 p-5 shadow-[0_28px_80px_rgba(30,41,100,.10)] backdrop-blur-2xl sm:p-7 lg:mt-14">
                    <div class="text-center">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">FAQ</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-[#070B1F] sm:text-4xl">
                            Често задавани въпроси
                        </h2>
                    </div>

                    <div class="mt-7 grid gap-4 md:grid-cols-2">
                        @foreach ($faqs as $faq)
                            <details class="group rounded-3xl border border-slate-200/70 bg-white/70 p-5 shadow-sm shadow-blue-900/5 open:border-violet-200 open:bg-white/90">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-black text-[#070B1F] [&::-webkit-details-marker]:hidden">
                                    {{ $faq['question'] }}
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-500 transition group-open:rotate-45 group-open:bg-violet-50 group-open:text-violet-600">+</span>
                                </summary>
                                <p class="mt-4 text-sm leading-6 text-slate-600">
                                    {{ $faq['answer'] }}
                                </p>
                            </details>
                        @endforeach
                    </div>
                </section>
            </section>
        </div>
    </main>
</body>
</html>

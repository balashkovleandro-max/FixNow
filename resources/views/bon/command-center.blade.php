<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Command Center | BON</title>
    <meta name="description" content="BON Command Center показва какво спира бизнеса, каква е диагнозата и какво действие трябва да последва.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F3F7FF] text-[#060B2B] antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute left-1/3 top-[-16rem] h-[34rem] w-[48rem] rounded-full bg-blue-500/[0.10] blur-3xl"></div>
        <div class="absolute right-[-12rem] top-28 h-[34rem] w-[34rem] rounded-full bg-violet-500/[0.10] blur-3xl"></div>
    </div>

    <div class="p-3 lg:grid lg:min-h-screen lg:grid-cols-[250px_1fr] lg:gap-4">
        <aside class="hidden rounded-[28px] border border-white/80 bg-white/[0.82] p-5 shadow-2xl shadow-blue-950/[0.06] backdrop-blur-2xl lg:flex lg:flex-col">
            <a href="{{ route('bon.index') }}" class="flex items-center gap-3">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-xl font-black text-white shadow-lg shadow-blue-600/[0.16]">B</span>
                <span>
                    <span class="block text-2xl font-black tracking-tight">BON</span>
                    <span class="text-xs font-semibold text-slate-400">Command Center</span>
                </span>
            </a>

            <nav class="mt-9 grid gap-2 text-[15px] font-semibold text-slate-600">
                @foreach([
                    ['Обзор', 'M4 12h16M4 6h16M4 18h10', 'bg-blue-50 text-blue-700 shadow-sm'],
                    ['BON Operator', 'M8 10h8M9 15h6M12 3a7 7 0 0 1 7 7v4a7 7 0 0 1-14 0v-4a7 7 0 0 1 7-7Z', 'hover:bg-slate-50'],
                    ['Диагностика', 'M4 13h4l2-7 4 14 2-7h4', 'hover:bg-slate-50'],
                    ['Действия', 'M9 12l2 2 4-5M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z', 'hover:bg-slate-50'],
                    ['Пазарни сигнали', 'M12 3v18M4 12h16M6 7c2 2 4 3 6 3s4-1 6-3M6 17c2-2 4-3 6-3s4 1 6 3', 'hover:bg-slate-50'],
                    ['Резултати', 'M5 19V9m5 10V5m5 14v-7m5 7V3', 'hover:bg-slate-50'],
                    ['Растеж', 'M5 17 10 12l4 4 6-10', 'hover:bg-slate-50'],
                    ['Настройки', 'M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z', 'hover:bg-slate-50'],
                ] as $item)
                    <a href="#" class="flex items-center gap-3 rounded-[18px] px-4 py-3 transition {{ $item[2] }}">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="{{ $item[1] }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ $item[0] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto rounded-[24px] border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5">
                <div class="grid size-12 place-items-center rounded-2xl bg-white text-blue-600 shadow-sm">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3 5 6v5c0 4.2 2.8 7.7 7 9 4.2-1.3 7-4.8 7-9V6l-7-3Z" stroke="currentColor" stroke-width="2"/>
                        <path d="m9 12 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="mt-4 text-sm font-bold leading-6 text-blue-950">BON е тук, за да спаси и развие твоя бизнес.</p>
            </div>
        </aside>

        <div class="overflow-hidden rounded-[28px] border border-white/80 bg-white/55 shadow-2xl shadow-blue-950/[0.06] backdrop-blur-2xl">
            <header class="flex h-[78px] items-center justify-between border-b border-slate-200/70 bg-white/70 px-5 sm:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('bon.index') }}" class="flex items-center gap-3 lg:hidden">
                        <span class="grid size-10 place-items-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-black text-white">B</span>
                        <span class="font-black">BON</span>
                    </a>
                    <div class="hidden items-baseline gap-5 lg:flex">
                        <span class="text-[44px] font-black leading-none tracking-[-0.05em] text-blue-600">BON</span>
                        <span class="text-base font-semibold text-slate-500">Business Operating Network</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 sm:inline-flex">Демо бизнес</span>
                    <span class="grid size-10 place-items-center rounded-full bg-gradient-to-br from-blue-600 to-violet-600 text-sm font-black text-white">ДБ</span>
                </div>
            </header>

            <main class="grid gap-6 p-5 sm:p-8 xl:grid-cols-[1fr_360px]">
                <section class="min-w-0">
                    <div class="mb-6">
                        <h1 class="text-[36px] font-black leading-[1.05] tracking-[-0.04em] text-[#060B2B] sm:text-[48px]">Какво спира бизнеса ти днес?</h1>
                        <p class="mt-3 text-[16px] leading-7 text-slate-500">BON открива проблемите, предлага следващото действие и помага за реално решение.</p>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-3">
                        <article class="rounded-[26px] border border-red-100 bg-gradient-to-br from-red-50 to-white p-6 shadow-xl shadow-red-950/[0.05]">
                            <div class="flex items-start justify-between gap-4">
                                <p class="text-[15px] font-black text-red-600">Активен проблем</p>
                                <span class="text-red-500">△</span>
                            </div>
                            <h2 class="mt-9 text-[25px] font-black leading-tight tracking-[-0.02em]">Малко клиенти този месец</h2>
                            <div class="mt-9 h-24 rounded-3xl bg-[linear-gradient(160deg,transparent_0%,transparent_48%,rgba(248,113,113,0.22)_49%,rgba(248,113,113,0.05)_100%)]"></div>
                        </article>

                        <article class="rounded-[26px] border border-white/90 bg-white/[0.86] p-6 text-center shadow-xl shadow-blue-950/[0.05]">
                            <p class="text-left text-[15px] font-black">BON Score</p>
                            <div class="mx-auto mt-5 grid size-36 place-items-center rounded-full bg-[conic-gradient(from_155deg,#67E8F9_0_24%,#2563EB_24%_74%,#E6ECF8_74%_100%)] p-3">
                                <div class="grid size-full place-items-center rounded-full bg-white">
                                    <div>
                                        <p class="text-[48px] font-black leading-none">74</p>
                                        <p class="mt-1 text-sm font-bold text-slate-500">/100</p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-slate-500">Добра основа, има важни области за подобрение.</p>
                        </article>

                        <article class="relative overflow-hidden rounded-[26px] border border-white/90 bg-white/[0.86] p-6 shadow-xl shadow-violet-950/[0.05]">
                            <div class="flex items-start justify-between">
                                <p class="text-[15px] font-black">Бизнес диагноза</p>
                                <span class="text-blue-500">✦</span>
                            </div>
                            <p class="mt-8 text-[15px] leading-7 text-[#11183B]">Основният проблем е ниска конверсия от интерес към контакт. Липсват ясна оферта, достатъчно ревюта и силен призив за действие.</p>
                            <div class="absolute -bottom-10 -right-10 size-40 rounded-full border border-blue-100"></div>
                        </article>
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-3">
                        <article class="rounded-[26px] border border-white/90 bg-white/[0.86] p-6 shadow-xl shadow-blue-950/[0.05]">
                            <h2 class="text-[16px] font-black">Следващи действия</h2>
                            <div class="mt-5 grid gap-3">
                                @foreach(['Добави оферта', 'Подобри профила', 'Активирай локална кампания'] as $index => $action)
                                    <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/80 p-3">
                                        <span class="grid size-8 place-items-center rounded-full bg-blue-600 text-xs font-black text-white">{{ $index + 1 }}</span>
                                        <span class="text-sm font-semibold text-[#11183B]">{{ $action }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article class="rounded-[26px] border border-white/90 bg-white/[0.86] p-6 shadow-xl shadow-blue-950/[0.05]">
                            <h2 class="text-[16px] font-black">Пазарни сигнали</h2>
                            <div class="mt-5 grid gap-3">
                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900">Има засилено търсене във вашия район</div>
                                <div class="rounded-2xl border border-violet-100 bg-violet-50 p-4 text-sm font-semibold text-violet-900">Потребителите търсят услуги след 18:00</div>
                            </div>
                        </article>

                        <article class="rounded-[26px] border border-white/90 bg-white/[0.86] p-6 shadow-xl shadow-blue-950/[0.05]">
                            <h2 class="text-[16px] font-black">Резултати</h2>
                            <div class="mt-5 grid grid-cols-2 gap-3">
                                @foreach([['+18%', 'интерес'], ['12', 'нови заявки'], ['4', 'резервации'], ['1 240 лв', 'потенциална стойност']] as $metric)
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-center">
                                        <p class="text-2xl font-black text-blue-600">{{ $metric[0] }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $metric[1] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    </div>

                    <article class="mt-4 rounded-[26px] border border-white/90 bg-white/[0.86] p-6 shadow-xl shadow-blue-950/[0.05]">
                        <h2 class="text-center text-[16px] font-black">Системата за бизнес спасяване и растеж</h2>
                        <div class="mt-6 grid gap-4 md:grid-cols-4">
                            @foreach([['Проблем', 'Откриваме какво спира бизнеса ти'], ['Диагноза', 'BON подрежда данни, поведение и пазар'], ['Действие', 'Ясни препоръки и конкретни стъпки'], ['Решение', 'Повече клиенти и повече резултати']] as $flow)
                                <div class="rounded-2xl bg-slate-50/80 p-4">
                                    <p class="text-sm font-black">{{ $flow[0] }}</p>
                                    <p class="mt-2 text-xs leading-5 text-slate-500">{{ $flow[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </section>

                <aside class="rounded-[28px] border border-white/90 bg-white/[0.86] p-6 shadow-2xl shadow-blue-950/[0.07]">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-black">BON Operator</h2>
                            <p class="mt-1 flex items-center gap-2 text-sm font-semibold text-slate-500"><span class="size-2 rounded-full bg-emerald-400"></span> Онлайн</p>
                        </div>
                        <span class="grid size-9 place-items-center rounded-2xl border border-slate-200 text-slate-400">−</span>
                    </div>

                    <div class="mx-auto mt-7 grid size-40 place-items-center rounded-full bg-gradient-to-br from-blue-50 to-violet-50">
                        <div class="grid size-28 place-items-center rounded-full bg-[#08143F] shadow-2xl shadow-blue-500/[0.18]">
                            <div class="text-center">
                                <div class="mx-auto flex justify-center gap-3">
                                    <span class="size-3 rounded-full bg-cyan-300"></span>
                                    <span class="size-3 rounded-full bg-cyan-300"></span>
                                </div>
                                <div class="mx-auto mt-4 h-3 w-10 rounded-b-full border-b-2 border-cyan-300"></div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-5 text-center text-sm font-semibold text-slate-500">Твоят BON оператор за бизнес растеж</p>

                    <div class="mt-7 grid gap-4">
                        <div class="rounded-[22px] border border-blue-100 bg-blue-50 p-4">
                            <div class="flex items-center justify-between text-xs font-bold text-blue-700">
                                <span>Ти</span>
                                <span>10:42</span>
                            </div>
                            <p class="mt-3 text-sm text-[#11183B]">Рекламата ми не работи.</p>
                        </div>
                        <div class="rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between text-xs font-bold text-violet-700">
                                <span>BON Operator</span>
                                <span>10:42</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-[#11183B]">Първо подобри офертата и профила си, след това пусни локална реклама.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-2 rounded-2xl border border-slate-200 bg-white p-2">
                        <input type="text" placeholder="Попитай BON Operator..." class="min-h-11 w-full rounded-xl border-0 bg-transparent px-3 text-sm outline-none placeholder:text-slate-400">
                        <button type="button" class="grid size-11 place-items-center rounded-xl bg-blue-600 text-white">
                            <span class="sr-only">Изпрати</span>
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 12 14-7-7 14-2-6-5-1Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </aside>
            </main>
        </div>
    </div>
</body>
</html>

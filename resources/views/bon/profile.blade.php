@extends('layouts.bon')

@section('title', 'Studio Dental Care | BON demo profile')
@section('meta_description', 'Демо публичен бизнес профил в BON стил - доверие, активност, BON Score, услуги, оферта и ясни действия.')

@section('content')
    <section class="mx-auto max-w-[1180px] px-5 py-6 pb-28 sm:px-8 lg:pb-12">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-400">
            <a href="{{ route('bon.index') }}" class="hover:text-blue-600">BON</a>
            <span>/</span>
            <span>Бизнеси</span>
            <span>/</span>
            <span>Здраве и грижа</span>
            <span>/</span>
            <span class="text-[#070B1F]">Studio Dental Care</span>
        </nav>

        <section class="overflow-hidden rounded-[28px] border border-white/80 bg-white/[0.82] shadow-2xl shadow-blue-950/[0.06] backdrop-blur-2xl">
            <div class="relative min-h-[310px] bg-gradient-to-br from-violet-100/90 via-white to-pink-100/70">
                <div class="absolute inset-0 opacity-70 [background-image:radial-gradient(circle_at_15%_20%,rgba(124,58,237,0.18),transparent_32%),radial-gradient(circle_at_90%_10%,rgba(236,72,153,0.16),transparent_28%)]"></div>
                <div class="absolute inset-x-0 bottom-0 h-32 rounded-t-[60%] bg-white/70"></div>

                <div class="relative grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_300px] lg:p-10">
                    <div class="grid gap-6 sm:grid-cols-[150px_1fr]">
                        <div class="mx-auto sm:mx-0">
                            <div class="grid size-[150px] place-items-center rounded-[28px] border border-white/90 bg-white/[0.82] shadow-xl shadow-violet-950/[0.08]">
                                <svg class="size-20 text-violet-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 4c2.2 0 2.6 1.2 4 1.2S13.8 4 16 4c2.7 0 4 2 4 4.5 0 3.8-2.2 9.5-4.7 9.5-1.8 0-1.2-4-3.3-4s-1.5 4-3.3 4C6.2 18 4 12.3 4 8.5 4 6 5.3 4 8 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M16.5 5.5 18 4l1.5 1.5L18 7l-1.5-1.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="mt-4 rounded-[14px] border border-violet-200 bg-white/80 px-4 py-2 text-center text-xs font-bold text-violet-700">
                                Потвърден бизнес
                            </div>
                        </div>

                        <div class="pt-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-[32px] font-black tracking-[-0.035em] text-[#070B1F] sm:text-[40px]">Studio Dental Care</h1>
                                <span class="grid size-7 place-items-center rounded-full bg-blue-600 text-white">✓</span>
                            </div>
                            <p class="mt-2 text-[15px] font-semibold text-slate-500">Стоматологична клиника</p>
                            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-500">
                                <span>София, ж.к. Лозенец</span>
                                <span class="text-amber-500">★★★★★</span>
                                <span class="text-[#070B1F]">4.9 <span class="text-slate-500">(128 отзива)</span></span>
                            </div>
                            <p class="mt-5 max-w-2xl text-[15px] leading-7 text-slate-600">
                                Съвременна дентална клиника с фокус върху превенция, естетика и дългосрочно здраве на усмивката. Работим с внимание, технология и грижа към всеки пациент.
                            </p>
                        </div>
                    </div>

                    <aside class="rounded-[24px] border border-white/90 bg-white/[0.82] p-4 shadow-xl shadow-pink-950/[0.06]">
                        <div class="grid gap-3">
                            <a href="{{ route('request.service') }}" class="rounded-[16px] bg-gradient-to-r from-blue-600 to-violet-600 px-5 py-4 text-center text-sm font-bold text-white shadow-lg shadow-blue-600/[0.16]">Изпрати запитване</a>
                            <a href="{{ route('request.service') }}" class="rounded-[16px] bg-gradient-to-r from-pink-500 to-violet-600 px-5 py-4 text-center text-sm font-bold text-white shadow-lg shadow-pink-500/[0.16]">Запази час</a>
                            <a href="tel:+359888123456" class="rounded-[16px] border border-slate-200 bg-white px-5 py-4 text-center text-sm font-bold text-[#11183B]">Обади се</a>
                        </div>
                    </aside>
                </div>
            </div>

            <div class="grid border-t border-slate-100 bg-white/60 sm:grid-cols-3">
                @foreach([
                    ['Работно време', 'Пон - Пет: 09:00 - 19:00', 'Съб: 09:00 - 14:00'],
                    ['Локация', 'София, ж.к. Лозенец', 'ул. Козяк 15'],
                    ['Средно време за отговор', 'Под 1 час', 'Активен профил'],
                ] as $info)
                    <div class="p-5 sm:border-r sm:border-slate-100 last:sm:border-r-0">
                        <p class="text-sm font-black text-[#070B1F]">{{ $info[0] }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-600">{{ $info[1] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $info[2] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_370px] lg:items-start">
            <section class="grid gap-6">
                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <h2 class="text-xl font-black text-[#070B1F]">За бизнеса</h2>
                    <p class="mt-4 text-[15px] leading-7 text-slate-600">Studio Dental Care е модерна дентална клиника, създадена с мисията да предоставя висококачествена грижа и комфорт на всеки пациент. BON профилът показва доверие, активност и ясни действия.</p>
                    <a href="#" class="mt-4 inline-flex text-sm font-bold text-blue-600">Виж повече</a>
                </article>

                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black text-[#070B1F]">Услуги</h2>
                        <a href="#" class="text-sm font-bold text-blue-600">Виж всички услуги</a>
                    </div>
                    <div class="mt-5 grid gap-4 md:grid-cols-3">
                        @foreach([
                            ['Профилактичен преглед', 'Преглед, почистване и план.', '80 лв.'],
                            ['Имплант с коронка', 'Висококачествен имплант и коронка.', '1 890 лв.'],
                            ['Избелване Zoom', 'Професионално избелване.', '450 лв.'],
                        ] as $service)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-5">
                                <h3 class="text-sm font-black text-[#070B1F]">{{ $service[0] }}</h3>
                                <p class="mt-2 text-xs leading-5 text-slate-500">{{ $service[1] }}</p>
                                <p class="mt-5 text-lg font-black text-blue-600">{{ $service[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black text-[#070B1F]">Отзиви</h2>
                        <a href="#" class="text-sm font-bold text-blue-600">Виж всички отзиви</a>
                    </div>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach([
                            ['Мария Димитрова', 'Страхотен екип и отношение. Обясняват всичко подробно и се чувстваш спокойно.'],
                            ['Георги Петров', 'Професионалисти. Бързо, безболезнено и с отличен резултат.'],
                        ] as $review)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-5">
                                <p class="font-black text-[#070B1F]">{{ $review[0] }}</p>
                                <p class="mt-1 text-sm text-amber-500">★★★★★</p>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $review[1] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black text-[#070B1F]">Галерия</h2>
                        <a href="#" class="text-sm font-bold text-blue-600">Виж всички снимки</a>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach(['from-blue-100 to-cyan-50', 'from-violet-100 to-blue-50', 'from-pink-100 to-violet-50', 'from-emerald-100 to-blue-50'] as $gradient)
                            <div class="aspect-[4/3] rounded-[22px] bg-gradient-to-br {{ $gradient }}"></div>
                        @endforeach
                    </div>
                </article>
            </section>

            <aside class="grid gap-5 lg:sticky lg:top-24">
                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <h2 class="text-xl font-black text-[#070B1F]">BON Score</h2>
                    <div class="mt-6 flex items-center gap-5">
                        <div class="grid size-32 place-items-center rounded-full bg-[conic-gradient(from_155deg,#2563EB_0_38%,#EC4899_38%_96%,#E6ECF8_96%_100%)] p-3">
                            <div class="grid size-full place-items-center rounded-full bg-white">
                                <span class="text-4xl font-black text-[#070B1F]">9.6</span>
                            </div>
                        </div>
                        <div class="grid gap-2 text-sm font-semibold text-slate-600">
                            <span>Над 100 отзива</span>
                            <span>Висока отзивчивост</span>
                            <span>Проверена самоличност</span>
                            <span>Лицензиран екип</span>
                        </div>
                    </div>
                </article>

                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <h2 class="text-xl font-black text-[#070B1F]">Бърз контакт</h2>
                    <div class="mt-5 grid gap-3 text-sm font-semibold text-slate-700">
                        <a href="tel:+359888123456" class="rounded-2xl bg-slate-50 px-4 py-3">0888 123 456</a>
                        <a href="mailto:hello@studiodentalcare.bg" class="rounded-2xl bg-slate-50 px-4 py-3">hello@studiodentalcare.bg</a>
                        <a href="#" class="rounded-2xl bg-slate-50 px-4 py-3">studiodentalcare.bg</a>
                    </div>
                </article>

                <article class="rounded-[26px] border border-white/80 bg-white/[0.82] p-6 shadow-xl shadow-blue-950/[0.04]">
                    <h2 class="text-xl font-black text-[#070B1F]">Запази час</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Избери удобен ден и час онлайн.</p>
                    <a href="{{ route('request.service') }}" class="mt-5 inline-flex w-full justify-center rounded-2xl border border-violet-200 bg-violet-50 px-5 py-4 text-sm font-bold text-violet-700">Провери свободни часове</a>
                </article>
            </aside>
        </div>
    </section>
@endsection

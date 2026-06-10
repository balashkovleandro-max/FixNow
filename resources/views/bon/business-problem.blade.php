@extends('layouts.bon')

@section('title', 'Какъв проблем има бизнесът ти? | Business Diagnosis')
@section('meta_description', 'Опиши какво не работи — BON ще ти помогне да откриеш вероятната причина и следващото правилно действие.')

@php
    $inputClass = 'min-h-12 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:bg-white focus:ring-4 focus:ring-blue-100';
    $textareaClass = 'rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:bg-white focus:ring-4 focus:ring-blue-100';
@endphp

@section('content')
    <section class="relative overflow-hidden px-5 py-12 sm:px-8 lg:py-16">
        <div class="pointer-events-none absolute left-1/2 top-12 -z-10 size-[32rem] -translate-x-1/2 rounded-full bg-blue-500/[0.08] blur-3xl"></div>

        <div class="mx-auto max-w-[1120px]">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-blue-600">Business Diagnosis</p>
                <h1 class="mt-4 text-[34px] font-black leading-[1.08] tracking-[-0.035em] text-[#070B1F] sm:text-[48px]">
                    Какъв проблем има бизнесът ти?
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-[16px] leading-8 text-slate-500 sm:text-lg">
                    Опиши какво не работи — BON ще ти помогне да откриеш вероятната причина и следващото правилно действие.
                </p>
            </div>

            <form action="#" method="POST" class="mt-10 grid gap-6">
                @csrf

                <section class="rounded-[32px] border border-white/80 bg-white/85 p-5 shadow-2xl shadow-blue-950/[0.06] backdrop-blur-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-blue-600">1</span>
                        <div>
                            <h2 class="text-xl font-black text-[#070B1F]">Основна информация</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Кой бизнес диагностицираме и как да се свържем с теб.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Име на бизнеса
                            <input type="text" name="business_name" class="{{ $inputClass }}" autocomplete="organization">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Тип бизнес
                            <input type="text" name="business_type" placeholder="Напр. салон, сервиз, магазин, услуга" class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Град
                            <input type="text" name="city" class="{{ $inputClass }}" autocomplete="address-level2">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Контактно лице
                            <input type="text" name="contact_person" class="{{ $inputClass }}" autocomplete="name">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Телефон
                            <input type="tel" name="phone" class="{{ $inputClass }}" autocomplete="tel">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Имейл
                            <input type="email" name="email" class="{{ $inputClass }}" autocomplete="email">
                        </label>
                    </div>
                </section>

                <section class="rounded-[32px] border border-white/80 bg-white/85 p-5 shadow-2xl shadow-violet-950/[0.05] backdrop-blur-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-violet-50 text-violet-600">2</span>
                        <div>
                            <h2 class="text-xl font-black text-[#070B1F]">Проблем</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Избери най-близкия симптом и опиши ситуацията с няколко изречения.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Какъв е проблемът?
                            <select name="problem_type" class="{{ $inputClass }}">
                                <option>Нямам достатъчно клиенти</option>
                                <option>Рекламата не работи</option>
                                <option>Имам празни часове</option>
                                <option>Нямам достатъчно заявки</option>
                                <option>Получавам запитвания, но не продавам</option>
                                <option>Търся персонал</option>
                                <option>Слаб профил / липса на доверие</option>
                                <option>Сайтът не носи резултат</option>
                                <option>Хаос със заявки / организация</option>
                                <option>Искам растеж, но не знам откъде да започна</option>
                                <option>Друго</option>
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Опиши проблема
                            <textarea name="description" rows="6" placeholder="Какво се случва, какво вече си пробвал и какъв резултат очакваш?" class="{{ $textareaClass }}"></textarea>
                        </label>
                    </div>
                </section>

                <section class="rounded-[32px] border border-white/80 bg-white/85 p-5 shadow-2xl shadow-pink-950/[0.05] backdrop-blur-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-pink-50 text-pink-500">3</span>
                        <div>
                            <h2 class="text-xl font-black text-[#070B1F]">Контекст</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">BON гледа не само проблема, а и средата около него.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            От кога го има?
                            <input type="text" name="duration" placeholder="Напр. от 2 месеца" class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Колко е спешно?
                            <select name="urgency" class="{{ $inputClass }}">
                                <option>Спешно</option>
                                <option>Тази седмица</option>
                                <option>До месец</option>
                                <option>Не е спешно</option>
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Как клиентите идват при теб сега?
                            <input type="text" name="customer_source" placeholder="Google, Facebook, препоръки, обекти..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Бюджет за решение
                            <input type="text" name="budget" placeholder="Напр. 300–1000 лв." class="{{ $inputClass }}">
                        </label>
                    </div>

                    <div class="mt-6 grid gap-3 rounded-[28px] bg-slate-50/80 p-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach([
                            ['active_ads', 'Имаш ли активна реклама?'],
                            ['website', 'Имаш ли сайт?'],
                            ['google_business', 'Имаш ли Google Business?'],
                            ['social_profiles', 'Имаш ли социални мрежи?'],
                        ] as [$name, $question])
                            <label class="flex min-h-14 items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm shadow-slate-900/[0.03]">
                                <input type="checkbox" name="{{ $name }}" value="1" class="size-5 rounded border-slate-300 text-blue-600 focus:ring-blue-200">
                                {{ $question }}
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[32px] border border-white/80 bg-white/85 p-5 shadow-2xl shadow-blue-950/[0.05] backdrop-blur-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-cyan-50 text-cyan-600">4</span>
                        <div>
                            <h2 class="text-xl font-black text-[#070B1F]">Линкове</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Добави публични профили, за да може диагнозата да бъде по-точна.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Сайт
                            <input type="url" name="website_url" placeholder="https://..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Instagram
                            <input type="url" name="instagram_url" placeholder="https://instagram.com/..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Facebook
                            <input type="url" name="facebook_url" placeholder="https://facebook.com/..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Google Business
                            <input type="url" name="google_business_url" placeholder="https://..." class="{{ $inputClass }}">
                        </label>
                    </div>
                </section>

                <div class="flex flex-col gap-4 rounded-[30px] border border-white/80 bg-white/80 p-5 shadow-2xl shadow-blue-950/[0.05] backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-xl text-sm leading-6 text-slate-500">
                        Това е първа версия на бизнес диагностиката. Следващата стъпка ще добави записване и result page.
                    </p>
                    <button type="button" class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-7 py-4 text-base font-black text-white shadow-xl shadow-blue-500/[0.20] transition hover:-translate-y-0.5">
                        Получи BON диагноза
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

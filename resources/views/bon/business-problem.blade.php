@extends('layouts.bon')

@section('title', 'Какъв проблем има бизнесът ти? | BON диагностика')
@section('meta_description', 'Опиши какво не работи. BON анализира ситуацията и подрежда вероятната причина, следващите действия и подходящите специалисти.')

@php
    $inputClass = 'min-h-12 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:bg-white focus:ring-4 focus:ring-blue-100';
    $textareaClass = 'rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:bg-white focus:ring-4 focus:ring-blue-100';
    $problemOptions = [
        'Нямам достатъчно клиенти',
        'Рекламата не работи',
        'Имам празни часове',
        'Нямам достатъчно заявки',
        'Получавам запитвания, но не продавам',
        'Търся персонал',
        'Слаб профил / липса на доверие',
        'Сайтът не носи резултат',
        'Хаос със заявки / организация',
        'Искам растеж, но не знам откъде да започна',
        'Друго',
    ];
@endphp

@section('content')
    <section class="relative overflow-hidden px-5 py-12 sm:px-8 lg:py-16">
        <div class="pointer-events-none absolute left-1/2 top-12 -z-10 size-[32rem] -translate-x-1/2 rounded-full bg-blue-500/[0.08] blur-3xl"></div>

        <div class="mx-auto max-w-[1120px]">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-blue-600">BON диагностика</p>
                <h1 class="mt-4 text-[34px] font-black leading-[1.08] tracking-[-0.035em] text-[#070B1F] sm:text-[48px]">
                    Какъв проблем има бизнесът ти?
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-[16px] leading-8 text-slate-500 sm:text-lg">
                    Опиши какво не работи. BON ще подреди вероятната причина, следващите действия и какъв тип специалист може да помогне.
                </p>
            </div>

            @if ($errors->any())
                <div class="mt-8 rounded-[28px] border border-rose-200 bg-rose-50/90 p-5 text-sm font-semibold text-rose-700 shadow-lg shadow-rose-900/[0.04]">
                    <p class="font-black">Провери полетата във формата.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bon.business-problem.store') }}" method="POST" class="mt-10 grid gap-6">
                @csrf

                <section class="rounded-[32px] border border-white/80 bg-white/85 p-5 shadow-2xl shadow-blue-950/[0.06] backdrop-blur-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-blue-600">1</span>
                        <div>
                            <h2 class="text-xl font-black text-[#070B1F]">Основна информация</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Кой бизнес анализираме и как да се свържем с теб, ако искаш продължение.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Име на бизнеса
                            <input type="text" name="business_name" value="{{ old('business_name') }}" class="{{ $inputClass }}" autocomplete="organization">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Тип бизнес
                            <input type="text" name="business_type" value="{{ old('business_type') }}" placeholder="Напр. салон, сервиз, магазин, услуга" class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Град
                            <input type="text" name="city" value="{{ old('city') }}" class="{{ $inputClass }}" autocomplete="address-level2">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Контактно лице
                            <input type="text" name="contact_person" value="{{ old('contact_person', auth()->user()?->name) }}" class="{{ $inputClass }}" autocomplete="name">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Телефон
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" class="{{ $inputClass }}" autocomplete="tel">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Имейл
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="{{ $inputClass }}" autocomplete="email">
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
                            <select name="problem_type" required class="{{ $inputClass }}">
                                @foreach ($problemOptions as $option)
                                    <option value="{{ $option }}" @selected(old('problem_type') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Опиши проблема
                            <textarea name="description" rows="6" placeholder="Какво се случва, какво вече си пробвал и какъв резултат очакваш?" class="{{ $textareaClass }}">{{ old('description') }}</textarea>
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
                            <input type="text" name="duration" value="{{ old('duration') }}" placeholder="Напр. от 2 месеца" class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Колко е спешно?
                            <select name="urgency" class="{{ $inputClass }}">
                                @foreach (['Спешно', 'Тази седмица', 'До месец', 'Не е спешно'] as $option)
                                    <option value="{{ $option }}" @selected(old('urgency') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Как клиентите идват при теб сега?
                            <input type="text" name="customer_source" value="{{ old('customer_source') }}" placeholder="Google, Facebook, препоръки, обекти..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Бюджет за решение
                            <input type="text" name="budget" value="{{ old('budget') }}" placeholder="Напр. 300-1000 лв." class="{{ $inputClass }}">
                        </label>
                    </div>

                    <div class="mt-6 grid gap-3 rounded-[28px] bg-slate-50/80 p-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['active_ads', 'Имаш ли активна реклама?'],
                            ['website', 'Имаш ли сайт?'],
                            ['google_business', 'Имаш ли Google Business?'],
                            ['social_profiles', 'Имаш ли социални мрежи?'],
                        ] as [$name, $question])
                            <label class="flex min-h-14 items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm shadow-slate-900/[0.03]">
                                <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name)) class="size-5 rounded border-slate-300 text-blue-600 focus:ring-blue-200">
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
                            <p class="mt-1 text-sm leading-6 text-slate-500">Добави публични профили, за да може анализът да бъде по-точен.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Сайт
                            <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Instagram
                            <input type="url" name="instagram_url" value="{{ old('instagram_url') }}" placeholder="https://instagram.com/..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Facebook
                            <input type="url" name="facebook_url" value="{{ old('facebook_url') }}" placeholder="https://facebook.com/..." class="{{ $inputClass }}">
                        </label>
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Google Business
                            <input type="url" name="google_business_url" value="{{ old('google_business_url') }}" placeholder="https://..." class="{{ $inputClass }}">
                        </label>
                    </div>
                </section>

                <div class="flex flex-col gap-4 rounded-[30px] border border-white/80 bg-white/80 p-5 shadow-2xl shadow-blue-950/[0.05] backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-xl text-sm leading-6 text-slate-500">
                        Това е първа работеща версия на BON диагностиката. Резултатът се запазва към профила ти, ако си логнат.
                    </p>
                    <button type="submit" class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-7 py-4 text-base font-black text-white shadow-xl shadow-blue-500/[0.20] transition hover:-translate-y-0.5">
                        Получи BON диагноза
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

@extends('layouts.bon')

@section('title', 'BON диагноза | ' . ($diagnostic->business_name ?: 'Бизнес анализ'))
@section('meta_description', 'Резултат от BON диагностика с вероятна причина, следващи действия и подходящи специалисти.')

@section('content')
    <section class="relative overflow-hidden px-5 py-12 sm:px-8 lg:py-16">
        <div class="pointer-events-none absolute left-1/2 top-8 -z-10 size-[34rem] -translate-x-1/2 rounded-full bg-violet-500/[0.10] blur-3xl"></div>

        <div class="mx-auto max-w-[1180px]">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-violet-600">BON диагноза</p>
                <h1 class="mt-4 text-[34px] font-black leading-[1.08] tracking-[-0.035em] text-[#070B1F] sm:text-[48px]">
                    Следващата стъпка вече е по-ясна.
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-[16px] leading-8 text-slate-500 sm:text-lg">
                    Това е rule-based първа версия на анализа. Използвай я като работна карта за действие, не като автоматично решение.
                </p>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <aside class="rounded-[32px] border border-white/80 bg-white/85 p-6 shadow-2xl shadow-blue-950/[0.06] backdrop-blur-2xl sm:p-7">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Активен проблем</p>
                    <h2 class="mt-3 text-2xl font-black text-[#070B1F]">{{ $diagnostic->problem_type }}</h2>

                    <dl class="mt-6 grid gap-3 text-sm">
                        @foreach ([
                            'Бизнес' => $diagnostic->business_name,
                            'Тип' => $diagnostic->business_type,
                            'Град' => $diagnostic->city,
                            'Спешност' => $diagnostic->urgency,
                            'Бюджет' => $diagnostic->budget,
                        ] as $label => $value)
                            @if (filled($value))
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <dt class="font-bold text-slate-500">{{ $label }}</dt>
                                    <dd class="mt-1 font-black text-[#070B1F]">{{ $value }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>

                    @if (filled($diagnostic->description))
                        <div class="mt-5 rounded-3xl border border-slate-100 bg-white p-5">
                            <p class="text-sm font-black text-slate-700">Контекст</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $diagnostic->description }}</p>
                        </div>
                    @endif
                </aside>

                <div class="grid gap-6">
                    <section class="rounded-[32px] border border-white/80 bg-white/85 p-6 shadow-2xl shadow-violet-950/[0.05] backdrop-blur-2xl sm:p-7">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Вероятна причина</p>
                        <p class="mt-4 text-lg leading-8 text-slate-700">{{ $diagnostic->likely_reason }}</p>
                    </section>

                    <section class="rounded-[32px] border border-white/80 bg-white/85 p-6 shadow-2xl shadow-blue-950/[0.05] backdrop-blur-2xl sm:p-7">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Следващи действия</p>
                        <div class="mt-5 grid gap-3">
                            @foreach (($diagnostic->next_steps ?? []) as $step)
                                <div class="flex gap-3 rounded-2xl bg-blue-50/70 p-4">
                                    <span class="grid size-7 shrink-0 place-items-center rounded-full bg-blue-600 text-xs font-black text-white">{{ $loop->iteration }}</span>
                                    <p class="text-sm leading-6 text-slate-700">{{ $step }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <div class="grid gap-6 md:grid-cols-2">
                        <section class="rounded-[32px] border border-white/80 bg-white/85 p-6 shadow-xl shadow-pink-950/[0.05] backdrop-blur-2xl">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-500">Какво да не правиш</p>
                            <div class="mt-4 grid gap-3">
                                @foreach (($diagnostic->warnings ?? []) as $warning)
                                    <p class="rounded-2xl bg-pink-50 px-4 py-3 text-sm leading-6 text-slate-700">{{ $warning }}</p>
                                @endforeach
                            </div>
                        </section>

                        <section class="rounded-[32px] border border-white/80 bg-white/85 p-6 shadow-xl shadow-cyan-950/[0.05] backdrop-blur-2xl">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-600">Подходящи специалисти</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach (($diagnostic->recommended_specialists ?? []) as $specialist)
                                    <span class="rounded-full bg-cyan-50 px-3 py-2 text-xs font-black text-cyan-700">{{ $specialist }}</span>
                                @endforeach
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 rounded-[30px] border border-white/80 bg-white/80 p-5 shadow-2xl shadow-blue-950/[0.05] backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-2xl text-sm leading-6 text-slate-500">
                    Следващата добра стъпка е да превърнеш анализа в конкретен проект, задача или подобрение в профила.
                </p>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('bon.business-problem') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700">
                        Нова диагностика
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 py-3 text-sm font-black text-white shadow-xl shadow-blue-500/[0.20]">
                        Към dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

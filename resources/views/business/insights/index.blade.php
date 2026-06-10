<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Финансов анализ | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
    @php
        $months = [
            1 => 'Януари',
            2 => 'Февруари',
            3 => 'Март',
            4 => 'Април',
            5 => 'Май',
            6 => 'Юни',
            7 => 'Юли',
            8 => 'Август',
            9 => 'Септември',
            10 => 'Октомври',
            11 => 'Ноември',
            12 => 'Декември',
        ];
        $money = fn ($value) => number_format((float) $value, 2, ',', ' ') . ' лв';
        $percent = fn ($value) => number_format((float) $value, 1, ',', ' ') . '%';
        $value = fn ($field, $default = 0) => old($field, $currentReport?->{$field} ?? $default);
        $ratio = fn ($part, $base) => (float) $base > 0 ? ((float) $part / (float) $base) * 100 : 0;
        $latestRevenue = (float) ($currentReport?->revenue ?? 0);
        $personnelCost = (float) ($currentReport?->payroll_cost ?? 0) + (float) ($currentReport?->payroll_taxes_cost ?? 0);
        $rentRatio = $ratio($currentReport?->rent_cost ?? 0, $latestRevenue);
        $personnelRatio = $ratio($personnelCost, $latestRevenue);
        $marketingRatio = $ratio($currentReport?->marketing_cost ?? 0, $latestRevenue);
        $fixedCosts = (float) ($currentReport?->rent_cost ?? 0)
            + $personnelCost
            + (float) ($currentReport?->utilities_cost ?? 0)
            + (float) ($currentReport?->software_cost ?? 0)
            + (float) ($currentReport?->other_fixed_costs ?? 0);
        $variableCosts = (float) ($currentReport?->inventory_cost ?? 0)
            + (float) ($currentReport?->marketing_cost ?? 0)
            + (float) ($currentReport?->transport_cost ?? 0)
            + (float) ($currentReport?->other_variable_costs ?? 0);
        $contributionRatio = $latestRevenue > 0 ? ($latestRevenue - $variableCosts) / $latestRevenue : null;
        $breakEvenRevenue = $contributionRatio && $contributionRatio > 0 ? $fixedCosts / $contributionRatio : null;
        $avgRevenuePerEmployee = ($currentReport?->employees_count ?? 0) > 0 ? $latestRevenue / $currentReport->employees_count : null;
        $avgCostPerEmployee = ($currentReport?->employees_count ?? 0) > 0 ? $personnelCost / $currentReport->employees_count : null;
        $staffRows = old('staff_roles', $currentReport?->staff_roles ?? []);

        while (count($staffRows) < 3) {
            $staffRows[] = ['title' => '', 'monthly_cost' => '', 'hours' => ''];
        }

        $metricCards = [
            ['label' => 'Оборот', 'value' => $currentReport ? $money($currentReport->revenue) : '0,00 лв', 'note' => 'Общ оборот за последния отчет', 'tone' => 'from-blue-500 to-cyan-400'],
            ['label' => 'Разходи', 'value' => $currentReport ? $money($currentReport->total_costs) : '0,00 лв', 'note' => 'Всички въведени разходи', 'tone' => 'from-violet-500 to-purple-500'],
            ['label' => 'Нетна печалба', 'value' => $currentReport ? $money($currentReport->net_profit) : '0,00 лв', 'note' => $currentReport?->isProfitable() ? 'Периодът е на печалба' : 'Нужен е преглед на разходите', 'tone' => $currentReport?->isProfitable() ? 'from-emerald-400 to-cyan-400' : 'from-rose-500 to-pink-500'],
            ['label' => 'Марж', 'value' => $currentReport ? $percent($currentReport->profit_margin) : '0,0%', 'note' => 'Нетна печалба спрямо оборот', 'tone' => 'from-fuchsia-500 to-pink-500'],
            ['label' => 'Персонал', 'value' => $currentReport ? $money($personnelCost) : '0,00 лв', 'note' => $currentReport ? $percent($personnelRatio) . ' от оборота' : 'Разход за екип', 'tone' => 'from-indigo-500 to-blue-500'],
            ['label' => 'Health Score', 'value' => $currentReport ? $currentReport->health_score . '/100' : '—', 'note' => $currentReport ? $scoreLabeler((int) $currentReport->health_score) : 'Запази първи отчет', 'tone' => 'from-blue-600 via-violet-600 to-fuchsia-500'],
        ];
        $costFields = [
            ['name' => 'rent_cost', 'label' => 'Наем'],
            ['name' => 'payroll_cost', 'label' => 'Заплати'],
            ['name' => 'payroll_taxes_cost', 'label' => 'Осигуровки/данъци за персонал'],
            ['name' => 'inventory_cost', 'label' => 'Доставки/стока/материали'],
            ['name' => 'utilities_cost', 'label' => 'Ток/вода/газ'],
            ['name' => 'marketing_cost', 'label' => 'Маркетинг'],
            ['name' => 'software_cost', 'label' => 'Софтуер/абонаменти'],
            ['name' => 'transport_cost', 'label' => 'Транспорт'],
            ['name' => 'other_fixed_costs', 'label' => 'Други фиксирани разходи'],
            ['name' => 'other_variable_costs', 'label' => 'Други променливи разходи'],
        ];
    @endphp

    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute -left-32 -top-28 h-96 w-96 rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="absolute right-[-10rem] top-20 h-[30rem] w-[30rem] rounded-full bg-fuchsia-400/15 blur-3xl"></div>
        <div class="absolute bottom-[-14rem] left-1/3 h-[28rem] w-[28rem] rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.26]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 64px 64px;"></div>
    </div>

    <div class="mx-auto max-w-[1500px] px-4 py-5 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl md:flex-row md:items-center md:justify-between md:px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-4">
                <div class="flex h-[52px] w-[52px] items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                <div>
                    <p class="text-xl font-black tracking-tight">BON</p>
                    <p class="text-sm font-semibold text-slate-500">Финансов анализ</p>
                </div>
            </a>

            <nav class="flex flex-wrap items-center gap-2 text-sm font-bold">
                <a href="{{ route('dashboard') }}" class="rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-slate-700 transition hover:border-blue-200 hover:text-blue-600">Към dashboard</a>
                <a href="{{ route('business.profile.edit') }}" class="rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-slate-700 transition hover:border-blue-200 hover:text-blue-600">Профил</a>
                <a href="{{ route('business.billing') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-white shadow-lg shadow-violet-500/20 transition hover:-translate-y-0.5">План</a>
            </nav>
        </header>

        @if (session('success'))
            <div class="mt-5 rounded-[1.5rem] border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm font-bold text-emerald-800 shadow-lg shadow-emerald-900/5">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-[1.5rem] border border-rose-200 bg-rose-50/90 px-5 py-4 text-sm text-rose-800 shadow-lg shadow-rose-900/5">
                <p class="font-black">Провери въведените данни:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px] lg:items-end">
            <div>
                <p class="inline-flex rounded-full border border-blue-200/70 bg-white/80 px-4 py-2 text-sm font-black text-blue-700 shadow-sm shadow-blue-900/5">Business Insights</p>
                <h1 class="mt-5 max-w-4xl text-4xl font-black tracking-tight text-[#070B1F] sm:text-5xl">
                    Разбери маржовете, разходите и къде изтичат пари.
                </h1>
                <p class="mt-4 max-w-3xl text-lg leading-8 text-slate-600">
                    Въведи месечните данни на бизнеса си. BON изчислява реална печалба, структура на разходите,
                    break-even риск и професионални следващи стъпки за по-добра финансова картина.
                </p>
            </div>

            <div class="rounded-[2rem] border border-white/70 bg-white/75 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Последен период</p>
                <p class="mt-3 text-3xl font-black">
                    {{ $currentReport ? ($months[$currentReport->month] ?? $currentReport->month) . ' ' . $currentReport->year : 'Няма отчет' }}
                </p>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    {{ $currentReport ? 'Последно обновяване: ' . $currentReport->updated_at->format('d.m.Y H:i') : 'Запази първия си отчет, за да видиш анализ.' }}
                </p>
            </div>
        </section>

        <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            @foreach ($metricCards as $card)
                <article class="rounded-[1.75rem] border border-white/70 bg-white/75 p-5 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl">
                    <div class="mb-4 h-1.5 w-16 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></div>
                    <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-2xl font-black tracking-tight text-[#070B1F]">{{ $card['value'] }}</p>
                    <p class="mt-2 text-xs leading-5 text-slate-500">{{ $card['note'] }}</p>
                </article>
            @endforeach
        </section>

        <main class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(360px,0.8fr)]">
            <form action="{{ route('business.insights.store') }}" method="POST" class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7">
                @csrf

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Нов отчет</p>
                        <h2 class="mt-2 text-2xl font-black">Месечни финансови данни</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Всички суми са за избрания месец. Ако оставиш поле празно, то се приема като 0.</p>
                    </div>
                    <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 py-3 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5">
                        Запази и анализирай
                    </button>
                </div>

                <section class="mt-7 rounded-[1.75rem] border border-slate-100 bg-white/70 p-4 sm:p-5">
                    <h3 class="text-lg font-black">Основни</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Месец
                            <select name="month" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                @foreach ($months as $monthNumber => $monthName)
                                    <option value="{{ $monthNumber }}" @selected((int) old('month', $currentReport?->month ?? now()->month) === $monthNumber)>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Година
                            <input name="year" type="number" min="2020" max="{{ now()->year + 1 }}" value="{{ old('year', $currentReport?->year ?? now()->year) }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Общ оборот за месеца
                            <input name="revenue" type="number" step="0.01" min="0" value="{{ $value('revenue') }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Брой клиенти/поръчки/резервации
                            <input name="orders_count" type="number" min="0" value="{{ old('orders_count', $currentReport?->orders_count) }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Средна стойност на клиент/поръчка
                            <input name="average_order_value" type="number" step="0.01" min="0" value="{{ $value('average_order_value', '') }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Брой служители
                            <input name="employees_count" type="number" min="0" value="{{ $value('employees_count') }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>
                </section>

                <section class="mt-5 rounded-[1.75rem] border border-slate-100 bg-white/70 p-4 sm:p-5">
                    <h3 class="text-lg font-black">Разходи</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($costFields as $field)
                            <label class="grid gap-2 text-sm font-bold text-slate-700">
                                {{ $field['label'] }}
                                <input name="{{ $field['name'] }}" type="number" step="0.01" min="0" value="{{ $value($field['name']) }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="mt-5 rounded-[1.75rem] border border-slate-100 bg-white/70 p-4 sm:p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-lg font-black">Персонал по роли</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Опционално. Полезно е за по-късен анализ на натоварване по позиции.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3">
                        @foreach ($staffRows as $index => $row)
                            <div class="grid gap-3 rounded-2xl border border-slate-100 bg-white/70 p-3 md:grid-cols-[1fr_180px_160px]">
                                <input name="staff_roles[{{ $index }}][title]" value="{{ $row['title'] ?? '' }}" placeholder="Роля/позиция" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                <input name="staff_roles[{{ $index }}][monthly_cost]" type="number" step="0.01" min="0" value="{{ $row['monthly_cost'] ?? '' }}" placeholder="Разход/месец" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                <input name="staff_roles[{{ $index }}][hours]" type="number" step="0.01" min="0" value="{{ $row['hours'] ?? '' }}" placeholder="Работни часове" class="min-h-12 rounded-2xl border border-slate-200 bg-white/90 px-4 text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            </div>
                        @endforeach
                    </div>
                </section>
            </form>

            <aside class="grid gap-6">
                <section class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Business Health Score</p>
                            <h2 class="mt-2 text-2xl font-black">{{ $currentReport ? $currentReport->health_score . '/100' : 'Няма данни' }}</h2>
                        </div>
                        <div class="grid h-24 w-24 place-items-center rounded-full bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 p-2 shadow-xl shadow-violet-500/20">
                            <div class="grid h-full w-full place-items-center rounded-full bg-white text-2xl font-black text-[#070B1F]">
                                {{ $currentReport ? $currentReport->health_score : '—' }}
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">
                        {{ $currentReport ? $scoreLabeler((int) $currentReport->health_score) : 'Запази първи отчет, за да видиш оценка.' }}
                    </p>
                    <div class="mt-5 grid gap-3 text-sm">
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold text-slate-600">Персонал / оборот</span><span class="font-black">{{ $percent($personnelRatio) }}</span></div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold text-slate-600">Наем / оборот</span><span class="font-black">{{ $percent($rentRatio) }}</span></div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold text-slate-600">Маркетинг / оборот</span><span class="font-black">{{ $percent($marketingRatio) }}</span></div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold text-slate-600">Break-even</span><span class="font-black">{{ $breakEvenRevenue ? $money($breakEvenRevenue) : '—' }}</span></div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold text-slate-600">Оборот/служител</span><span class="font-black">{{ $avgRevenuePerEmployee ? $money($avgRevenuePerEmployee) : '—' }}</span></div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold text-slate-600">Разход/служител</span><span class="font-black">{{ $avgCostPerEmployee ? $money($avgCostPerEmployee) : '—' }}</span></div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Препоръки</p>
                    <h2 class="mt-2 text-2xl font-black">Следващи финансови стъпки</h2>

                    @if ($currentReport && filled($currentReport->recommendations))
                        <div class="mt-5 grid gap-3">
                            @foreach ($currentReport->recommendations as $recommendation)
                                <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 text-sm leading-6 text-slate-700">
                                    {{ $recommendation }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                            След като запазиш отчет, тук ще видиш професионални препоръки за марж, разходи, персонал и break-even риск.
                        </p>
                    @endif
                </section>

                <section class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">Premium ready</p>
                    <h2 class="mt-2 text-2xl font-black">Подготвено за следващ етап</h2>
                    <ul class="mt-4 grid gap-2 text-sm leading-6 text-slate-600">
                        <li>• Сравнение месец към месец</li>
                        <li>• Годишен анализ и тенденции</li>
                        <li>• Експорт към PDF отчет</li>
                        <li>• По-подробни бизнес препоръки</li>
                    </ul>
                </section>
            </aside>
        </main>

        <section class="mt-6 rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-7">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-slate-500">История</p>
                    <h2 class="mt-2 text-2xl font-black">Запазени месечни анализи</h2>
                </div>
                <p class="text-sm text-slate-500">Показват се последните {{ $reports->count() }} отчета.</p>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Период</th>
                            <th class="whitespace-nowrap px-4 py-3">Оборот</th>
                            <th class="whitespace-nowrap px-4 py-3">Разходи</th>
                            <th class="whitespace-nowrap px-4 py-3">Нетна печалба</th>
                            <th class="whitespace-nowrap px-4 py-3">Марж</th>
                            <th class="whitespace-nowrap px-4 py-3">Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr class="text-slate-700">
                                <td class="whitespace-nowrap px-4 py-4 font-black">{{ $months[$report->month] ?? $report->month }} {{ $report->year }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $money($report->revenue) }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $money($report->total_costs) }}</td>
                                <td class="whitespace-nowrap px-4 py-4 font-black {{ $report->isProfitable() ? 'text-emerald-600' : 'text-rose-600' }}">{{ $money($report->net_profit) }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $percent($report->profit_margin) }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $report->health_score }}/100</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    Все още няма запазени финансови анализи. Попълни формата, за да създадеш първия отчет.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>

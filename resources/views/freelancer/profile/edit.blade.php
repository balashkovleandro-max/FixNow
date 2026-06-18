<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редакция на фрийлансър профил | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $selectedCategories = collect(old('service_categories', $user->serviceCategories()))->filter()->values()->all();
    $selectedCities = collect(old('service_cities', $user->serviceCities()))->filter()->values()->all();
    $selectedLanguages = collect(old('languages', $user->languages ?? []))->filter()->values()->all();
    $bookingEnabled = (bool) old('booking_enabled', data_get($user, 'booking_enabled', false));
@endphp
<body class="bon-dark-page min-h-screen overflow-x-hidden bg-[#020617] text-white">
    <main class="relative min-h-screen overflow-x-clip">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-5xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[1.5rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:rounded-[2rem] sm:p-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">BON Freelancer</p>
                        <p class="text-sm text-slate-500">Профил, умения и портфолио</p>
                    </div>
                </a>
                <div class="flex flex-wrap gap-2 text-sm font-bold">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 text-slate-600 hover:bg-white hover:text-blue-700">Табло</a>
                    <a href="{{ route('freelancers.show', $user) }}" class="rounded-2xl bg-blue-50 px-4 py-2 text-blue-700">Публичен профил</a>
                </div>
            </header>

            @if($errors->any())
                <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-7 rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Профил на фрийлансър</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Покажи ясно какво можеш.</h1>
                <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                    Този профил е отделен от бизнес профилите в BON. Клиентите трябва бързо да разберат какви услуги предлагаш, как работиш и защо си подходящ за техния проект.
                </p>

                <form action="{{ route('freelancer.profile.update') }}" method="POST" class="mt-8 grid gap-6">
                    @csrf
                    @method('PUT')

                    <div id="identity" class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Име / бранд
                            <input name="name" value="{{ old('name', $user->name) }}" required maxlength="160" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Headline
                            <input name="headline" value="{{ old('headline', $user->business_category) }}" maxlength="160" placeholder="Дизайн специалист, уеб разработчик..." class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <label id="bio" class="grid gap-2 text-sm font-black text-slate-700">
                        Профилна снимка / аватар URL
                        <input name="avatar" value="{{ old('avatar', $user->avatar) }}" maxlength="255" placeholder="https://..." class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                    </label>

                    <label class="grid gap-2 text-sm font-black text-slate-700">
                        Кратко представяне
                        <input name="short_description" value="{{ old('short_description', $user->short_description) }}" maxlength="240" placeholder="Кратко изречение, което казва как помагаш на клиентите." class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                    </label>

                    <label class="grid gap-2 text-sm font-black text-slate-700">
                        Bio / описание
                        <textarea name="description" rows="6" maxlength="3000" class="rounded-3xl border border-slate-200 bg-white/85 px-4 py-4 leading-7 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">{{ old('description', $user->description) }}</textarea>
                    </label>

                    <div id="skills" class="rounded-3xl border border-slate-100 bg-white/70 p-5">
                        <p class="font-black text-slate-900">Умения и услуги</p>
                        <p class="mt-1 text-sm text-slate-500">Избери най-релевантните области. Те се показват в listing-а и публичния профил.</p>

                        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white/80 px-4 py-3 text-sm font-bold text-slate-700">
                                    <input type="checkbox" name="service_categories[]" value="{{ $category }}" @checked(in_array($category, $selectedCategories, true)) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    {{ $category }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Град
                            <input name="city" value="{{ old('city', $user->city) }}" maxlength="120" placeholder="София, Пловдив, онлайн..." class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Градове / онлайн работа
                            <input name="service_cities[]" value="{{ old('service_cities.0', $selectedCities[0] ?? '') }}" maxlength="120" placeholder="Онлайн, София, Варна..." class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div id="rates" class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Почасова ставка
                            <input name="hourly_rate" type="number" min="0" step="0.01" value="{{ old('hourly_rate', $user->hourly_rate) }}" placeholder="50" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Цена от / по проект
                            <input name="project_rate" type="number" min="0" step="0.01" value="{{ old('project_rate', $user->project_rate) }}" placeholder="300" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div id="availability" class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Наличност
                            <select name="availability" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                @foreach(['' => 'Избери', 'available' => 'Свободен', 'busy' => 'Зает', 'negotiable' => 'По договаряне'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('availability', $user->availability) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Работа
                            <select name="work_mode" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                @foreach(['' => 'Избери', 'online' => 'Онлайн', 'onsite' => 'На място', 'hybrid' => 'Хибридно'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('work_mode', $user->work_mode) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <label class="flex items-start gap-3 rounded-3xl border border-blue-100 bg-blue-50/80 p-5 text-sm font-black text-blue-900">
                        <input type="checkbox" name="booking_enabled" value="1" @checked($bookingEnabled) class="mt-1 h-4 w-4 rounded border-blue-200 text-blue-600 focus:ring-blue-500">
                        <span>
                            Онлайн записване / резервация
                            <span class="mt-1 block text-xs font-semibold leading-5 text-blue-700/70">Показва публичен бутон “Запази час” само ако функцията е активирана. Подходящо за консултации, тренировки, студиа и услуги с часове.</span>
                        </span>
                    </label>

                    <div id="contact" class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Телефон
                            <input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="80" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Време за отговор
                            <input name="response_time_label" value="{{ old('response_time_label', $user->response_time_label) }}" maxlength="120" placeholder="Отговаря до 2 часа" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white/70 p-5">
                        <p class="font-black text-slate-900">Езици</p>
                        <p class="mt-1 text-sm text-slate-500">Добави до три езика, на които можеш да работиш с клиенти.</p>
                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            @for($i = 0; $i < 3; $i++)
                                <input name="languages[]" value="{{ old('languages.'.$i, $selectedLanguages[$i] ?? '') }}" maxlength="80" placeholder="{{ $i === 0 ? 'Български' : ($i === 1 ? 'Английски' : 'Друг език') }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                            @endfor
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Уебсайт / портфолио
                            <input name="website" type="url" value="{{ old('website', $user->website) }}" placeholder="https://..." class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Опит
                            <input name="years_experience" value="{{ old('years_experience', $user->years_experience) }}" maxlength="80" placeholder="5 години опит" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Instagram
                            <input name="instagram" value="{{ old('instagram', $user->instagram) }}" maxlength="255" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Facebook / LinkedIn
                            <input name="facebook" value="{{ old('facebook', $user->facebook) }}" maxlength="255" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            LinkedIn
                            <input name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" maxlength="255" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            GitHub
                            <input name="github" value="{{ old('github', $user->github) }}" maxlength="255" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                        <label class="grid gap-2 text-sm font-black text-slate-700">
                            Behance
                            <input name="behance" value="{{ old('behance', $user->behance) }}" maxlength="255" class="min-h-12 rounded-2xl border border-slate-200 bg-white/85 px-4 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </label>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                            Запази профила
                        </button>
                        <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-6 text-sm font-black text-slate-700">
                            Назад към таблото
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

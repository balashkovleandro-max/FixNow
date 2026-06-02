<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пусни заявка за оферта | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    @php
        $requestCategories = $requestCategories ?? collect();
        $urgencies = [
            'urgent' => 'Спешно',
            'this_week' => 'Тази седмица',
            'this_month' => 'До месец',
            'no_deadline' => 'Няма конкретен срок',
        ];
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.22),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main class="mx-auto grid max-w-7xl gap-8 px-4 pb-12 pt-4 sm:px-6 lg:grid-cols-[0.92fr_1.08fr] lg:px-8">
        <section class="flex flex-col justify-center">
            <p class="mb-4 inline-flex w-fit rounded-full border border-orange-300/20 bg-orange-300/10 px-4 py-2 text-sm font-semibold text-orange-100">Безплатна заявка за оферта</p>
            <h1 class="max-w-2xl text-4xl font-black leading-tight sm:text-6xl">
                Опишете задачата и получете подходящи оферти.
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-white/70">
                За ремонти, ВиК, електро, почистване, хамали, техника и други request-based услуги. Избирате категория, град и срок, а подходящи изпълнители могат да ви изпратят оферта.
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach([
                    ['value' => '1', 'label' => 'Попълвате заявка'],
                    ['value' => '2', 'label' => 'Изпълнителите виждат релевантни заявки'],
                    ['value' => '3', 'label' => 'Получавате оферти'],
                ] as $step)
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl">
                        <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-sm font-black">{{ $step['value'] }}</span>
                        <p class="mt-3 text-sm font-bold text-white/75">{{ $step['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 rounded-3xl border border-orange-300/15 bg-orange-400/10 p-5 text-sm leading-6 text-white/65">
                <strong class="text-white">Важно:</strong> заявките са само за категории, в които офертите имат смисъл. За автосервизи, фризьори, адвокати, магазини и други directory категории използвайте “Намери изпълнител”.
            </div>
        </section>

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-black/25 backdrop-blur-xl sm:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">
                    <p>{{ session('success') }}</p>
                    @if(session('offers_url'))
                        <a href="{{ session('offers_url') }}" data-track="cta_view_offers" class="mt-3 inline-flex min-h-11 items-center justify-center rounded-2xl bg-emerald-300/15 px-4 py-3 text-sm font-black text-emerald-100 hover:bg-emerald-300/20">
                            Виж получените оферти
                        </a>
                    @endif
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-400/20 bg-red-400/10 p-4 text-red-200">
                    <p class="font-bold">Моля, проверете формата:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('request.service.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-5">
                @csrf

                <div>
                    <label for="category" class="mb-2 block text-sm font-semibold text-white/75">Категория/услуга</label>
                    <select id="category" name="category" class="min-h-12 w-full rounded-2xl border {{ $errors->has('category') ? 'border-red-300/60' : 'border-white/10' }} bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                        <option value="">Изберете категория за оферти</option>
                        @foreach($requestCategories as $category)
                            <option value="{{ $category['name'] }}" {{ old('category', request('category')) === $category['name'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="service" class="mb-2 block text-sm font-semibold text-white/75">Конкретна услуга</label>
                        <input id="service" type="text" name="service" value="{{ old('service') }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('service') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Напр. ремонт на баня, смяна на бойлер">
                        @error('service')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="mb-2 block text-sm font-semibold text-white/75">Град</label>
                        <input id="city" type="text" name="city" value="{{ old('city', request('city')) }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('city') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Напр. Плевен">
                        @error('city')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-semibold text-white/75">Описание на проблема/услугата</label>
                    <textarea id="description" name="description" rows="5" class="w-full rounded-2xl border {{ $errors->has('description') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Опишете какво трябва да се направи, къде се намира обектът и важните детайли.">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="photos" class="mb-2 block text-sm font-semibold text-white/75">Снимки <span class="text-white/40">(по желание)</span></label>
                        <input id="photos" type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="min-h-12 w-full rounded-2xl border {{ $errors->has('photos') || $errors->has('photos.*') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white file:mr-4 file:rounded-xl file:border-0 file:bg-orange-400/20 file:px-4 file:py-2 file:font-bold file:text-orange-100">
                        @error('photos')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                        @error('photos.*')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="budget" class="mb-2 block text-sm font-semibold text-white/75">Бюджет <span class="text-white/40">(по желание)</span></label>
                        <input id="budget" type="text" name="budget" value="{{ old('budget') }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('budget') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Напр. до 500 лв.">
                        @error('budget')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <span class="mb-2 block text-sm font-semibold text-white/75">Срок/спешност</span>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($urgencies as $value => $label)
                            <label class="cursor-pointer rounded-2xl border border-white/10 bg-white/10 p-4 text-sm font-bold text-white/75 has-[:checked]:border-orange-300/50 has-[:checked]:bg-orange-300/10">
                                <input type="radio" name="urgency" value="{{ $value }}" class="sr-only" {{ old('urgency', 'no_deadline') === $value ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('urgency')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-white/75">Име на клиента</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" class="min-h-12 w-full rounded-2xl border {{ $errors->has('name') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Вашето име">
                        @error('name')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-white/75">Телефон</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="min-h-12 w-full rounded-2xl border {{ $errors->has('phone') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="+359 ...">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-white/75">Email <span class="text-white/40">(по желание)</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" class="min-h-12 w-full rounded-2xl border {{ $errors->has('email') ? 'border-red-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="name@example.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" data-track="cta_request" class="fn-amber-cta min-h-12 rounded-2xl px-6 py-4 font-black">
                    Изпрати заявка
                </button>
            </form>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

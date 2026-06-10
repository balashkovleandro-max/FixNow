<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Публикувай услуга | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    @php
        $business = auth()->user();
        $serviceLimit = $business?->categoryLimit() ?? 0;
        $photoLimit = $business?->photoLimit() ?? 0;
        $serviceCount = $business?->services()->count() ?? 0;
        $photoCount = $business?->photoCount() ?? 0;
        $canAddService = !$serviceLimit || $serviceCount < $serviceLimit;
    @endphp

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Назад към таблото</a>
            <a href="{{ route('services.index') }}" class="hidden text-sm font-bold text-orange-200 hover:text-white sm:block">Виж услугите</a>
        </div>

        <section class="grid gap-6 lg:grid-cols-[1fr_340px]">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Нова услуга</p>
                <h1 class="mt-3 text-3xl font-black sm:text-5xl">Публикувай услуга</h1>
                <p class="mt-3 text-white/60">Добави услуга към профила си на бизнес. Полетата остават съвместими със съществуващата Laravel логика.</p>

                <div class="mt-6 grid gap-3 md:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-sm text-white/50">План</p>
                        <p class="mt-1 font-black">{{ $business?->planLabel() }}</p>
                    </div>
                    <div class="rounded-2xl border {{ $serviceCount >= $serviceLimit ? 'border-rose-300/30 bg-rose-400/10' : 'border-white/10 bg-slate-950/45' }} p-4">
                        <p class="text-sm text-white/50">Услуги</p>
                        <p class="mt-1 font-black">{{ $serviceCount }} / {{ $serviceLimit }}</p>
                    </div>
                    <div class="rounded-2xl border {{ $photoCount >= $photoLimit ? 'border-amber-300/30 bg-amber-400/10' : 'border-white/10 bg-slate-950/45' }} p-4">
                        <p class="text-sm text-white/50">Снимки</p>
                        <p class="mt-1 font-black">{{ $photoCount }} / {{ $photoLimit }}</p>
                    </div>
                </div>

                @unless($canAddService)
                    <div class="mt-6 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-rose-100">
                        Достигнат е лимитът за услуги в текущия план. За повече услуги преминете към Premium или коригирайте текущите услуги.
                    </div>
                @endunless

                @if(session('success'))
                    <div class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-400/20 bg-red-400/10 p-4 text-red-200">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="mt-7 grid gap-5">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Заглавие</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Напр. Професионално боядисване на апартамент" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white/75">Категория</label>
                            <select name="category" class="min-h-12 w-full rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                <option value="">Избери категория</option>
                                @foreach(['Ресторанти и кафенета','Хотели','Ремонти и строителство','ВиК','Електро услуги','Автосервизи','Почистване','Красота и грижа','Здраве и уелнес','Спорт и активности'] as $category)
                                    <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white/75">Град</label>
                            <input list="cities" id="city" name="city" value="{{ old('city') }}" placeholder="Започни да пишеш град" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            <datalist id="cities">
                                @foreach(['София','Пловдив','Варна','Бургас','Русе','Стара Загора','Плевен','Сливен','Добрич','Шумен','Перник','Хасково','Ямбол','Пазарджик','Благоевград','Велико Търново'] as $city)
                                    <option value="{{ $city }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Описание</label>
                        <textarea name="description" rows="5" placeholder="Опиши услугата подробно..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white/75">Цена</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}" placeholder="Напр. 150" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white/75">Телефон</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Напр. 0899123456" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Снимка</label>
                        <input type="file" name="image" class="block min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white file:mr-4 file:rounded-xl file:border-0 file:bg-orange-400/20 file:px-4 file:py-2 file:font-bold file:text-orange-100">
                    </div>
                    <button type="submit" class="min-h-12 rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25">
                        Публикувай
                    </button>
                </form>
            </div>

            <aside class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl lg:self-start">
                <h2 class="text-xl font-black">Premium съвети</h2>
                <div class="mt-5 grid gap-3 text-sm text-white/70">
                    <p class="rounded-2xl bg-slate-950/50 p-4">Използвай ясно заглавие и конкретна категория.</p>
                    <p class="rounded-2xl bg-slate-950/50 p-4">Добави реална снимка, за да повишиш доверието.</p>
                    <p class="rounded-2xl bg-slate-950/50 p-4">Посочи телефон за бързи запитвания.</p>
                </div>
            </aside>
        </section>
    </main>
    @include('partials.mobile-bottom-nav')
</body>
</html>

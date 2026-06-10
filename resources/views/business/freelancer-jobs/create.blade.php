<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Нова обява | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F8FAFF] text-[#070B1F]">
    <main class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <a href="{{ route('business.jobs.index') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">Нова обява</p>
                        <p class="text-sm text-slate-500">{{ $business->business_name ?: $business->name }}</p>
                    </div>
                </a>
                <a href="{{ route('business.jobs.index') }}" class="rounded-2xl border border-slate-200 bg-white/80 px-5 py-3 text-sm font-black text-slate-700">Назад</a>
            </header>

            @if($errors->any())
                <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Публикуване</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight">Опиши задачата към фрийлансъри.</h1>
                <p class="mt-4 text-lg leading-8 text-slate-600">Ясното описание помага на правилните специалисти да кандидатстват с конкретно предложение.</p>

                <form action="{{ route('business.jobs.store') }}" method="POST" class="mt-8 grid gap-5">
                    @csrf

                    <div>
                        <label for="title" class="mb-2 block text-sm font-black text-slate-700">Заглавие</label>
                        <input id="title" name="title" value="{{ old('title') }}" required class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="Например: Landing page за нова услуга">
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-black text-slate-700">Описание</label>
                        <textarea id="description" name="description" rows="8" required class="w-full rounded-3xl border border-slate-200 bg-white/80 px-4 py-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="Опиши какъв резултат търсиш, какъв е контекстът и какво очакваш от специалиста.">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="budget" class="mb-2 block text-sm font-black text-slate-700">Бюджет</label>
                            <input id="budget" name="budget" type="number" step="0.01" min="0" value="{{ old('budget') }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="Например: 500">
                        </div>
                        <div>
                            <label for="deadline" class="mb-2 block text-sm font-black text-slate-700">Срок</label>
                            <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="category" class="mb-2 block text-sm font-black text-slate-700">Категория</label>
                            <select id="category" name="category" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                <option value="">Избери категория</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="location" class="mb-2 block text-sm font-black text-slate-700">Локация по желание</label>
                            <input id="location" name="location" value="{{ old('location') }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="София, remote, Пловдив...">
                        </div>
                    </div>

                    <button class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">Публикувай обява</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

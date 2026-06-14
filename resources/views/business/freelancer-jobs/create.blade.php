<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Публикувай проект | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-clip bg-[#F8FAFF] text-[#070B1F]">
    <main class="relative min-h-screen overflow-x-clip">
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-5xl px-4 py-5 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[1.5rem] border border-white/70 bg-white/75 p-4 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl sm:flex-row sm:items-center sm:justify-between sm:rounded-[2rem] sm:p-5">
                <a href="{{ route('business.jobs.index') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
                    <div>
                        <p class="text-xl font-black">Нов проект</p>
                        <p class="text-sm text-slate-500">{{ $business->business_name ?: $business->name }}</p>
                    </div>
                </a>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('freelancer.projects.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-5 text-sm font-black text-slate-700">Виж проекти</a>
                    <a href="{{ route('business.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white/80 px-5 text-sm font-black text-slate-700">Назад</a>
                </div>
            </header>

            @if($errors->any())
                <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-7 rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Публикуване</p>
                <h1 class="mt-3 text-[32px] font-black leading-tight tracking-tight sm:text-5xl">Опиши проекта към фрийлансъри.</h1>
                <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                    Колкото по-ясни са резултатът, бюджетът, срокът и начинът на работа, толкова по-конкретни оферти ще получиш.
                </p>

                <form action="{{ route('business.jobs.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-5">
                    @csrf

                    <div>
                        <label for="title" class="mb-2 block text-sm font-black text-slate-700">Заглавие на проекта</label>
                        <input id="title" name="title" value="{{ old('title') }}" required class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="Например: Landing page за нова услуга">
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-black text-slate-700">Описание</label>
                        <textarea id="description" name="description" rows="8" required class="w-full rounded-3xl border border-slate-200 bg-white/80 px-4 py-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="Опиши какъв резултат търсиш, какъв е контекстът, какво очакваш от специалиста и как ще изглежда добрият финал.">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="category" class="mb-2 block text-sm font-black text-slate-700">Категория</label>
                            <input id="category" name="category" list="freelancer-project-category-options" value="{{ old('category') }}" placeholder="Избери или напиши категория" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                            <datalist id="freelancer-project-category-options">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label for="work_mode" class="mb-2 block text-sm font-black text-slate-700">Начин на работа</label>
                            <select id="work_mode" name="work_mode" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                <option value="">Избери</option>
                                <option value="online" @selected(old('work_mode') === 'online')>Онлайн</option>
                                <option value="onsite" @selected(old('work_mode') === 'onsite')>На място</option>
                                <option value="hybrid" @selected(old('work_mode') === 'hybrid')>Хибридно</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="budget" class="mb-2 block text-sm font-black text-slate-700">Бюджет</label>
                            <input id="budget" name="budget" type="number" step="0.01" min="0" value="{{ old('budget') }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="500">
                        </div>
                        <div>
                            <label for="deadline" class="mb-2 block text-sm font-black text-slate-700">Срок</label>
                            <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </div>
                        <div>
                            <label for="location" class="mb-2 block text-sm font-black text-slate-700">Град, ако е на място</label>
                            <input id="location" name="location" value="{{ old('location') }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100" placeholder="София, Пловдив...">
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="client_name" class="mb-2 block text-sm font-black text-slate-700">Име</label>
                            <input id="client_name" name="client_name" value="{{ old('client_name', auth()->user()?->name) }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </div>
                        <div>
                            <label for="client_phone" class="mb-2 block text-sm font-black text-slate-700">Телефон</label>
                            <input id="client_phone" name="client_phone" value="{{ old('client_phone', auth()->user()?->phone) }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </div>
                        <div>
                            <label for="client_email" class="mb-2 block text-sm font-black text-slate-700">Имейл</label>
                            <input id="client_email" name="client_email" type="email" value="{{ old('client_email', auth()->user()?->email) }}" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 text-slate-900 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                        </div>
                    </div>

                    <div>
                        <label for="attachment" class="mb-2 block text-sm font-black text-slate-700">Файл или снимка, ако има</label>
                        <input id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm text-slate-700 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <button class="min-h-12 rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                        Публикувай проект
                    </button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

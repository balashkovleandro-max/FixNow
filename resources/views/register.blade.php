<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fn-premium-page min-h-screen overflow-x-hidden pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_18%,rgba(251,146,60,0.18),transparent_28%),radial-gradient(circle_at_82%_8%,rgba(245,158,11,0.20),transparent_30%),linear-gradient(180deg,#030712_0%,#061426_52%,#020812_100%)]"></div>

    <header class="border-b border-white/10 bg-[#030712]/80 backdrop-blur-2xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 via-amber-400 to-orange-600 font-black shadow-lg shadow-orange-600/25">F</div>
                <div>
                    <p class="text-xl font-black">FixNow.bg</p>
                    <p class="hidden text-xs text-white/50 sm:block">Профил за клиенти и изпълнители</p>
                </div>
            </a>
            <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-2xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-black text-white hover:bg-white/10">Вход</a>
        </div>
    </header>

    <main class="mx-auto grid min-h-[calc(100vh-80px)] max-w-7xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_500px] lg:px-8">
        <section class="hidden lg:block">
            <p class="mb-4 inline-flex rounded-full border border-orange-300/20 bg-orange-300/10 px-4 py-2 text-sm font-semibold text-orange-100">Създай профил</p>
            <h1 class="max-w-2xl text-5xl font-black leading-tight">
                Присъедини се към premium marketplace за
                <span class="bg-gradient-to-r from-orange-300 via-amber-300 to-orange-500 bg-clip-text text-transparent">локални услуги</span>
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-white/70">
                Търси услуги като клиент или стани изпълнител пред хора, които вече имат конкретна нужда.
            </p>
            <div class="mt-8 grid max-w-xl grid-cols-3 gap-4">
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                    <p class="text-2xl font-black text-orange-300">01</p>
                    <p class="mt-2 text-sm text-white/60">Създай профил</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                    <p class="text-2xl font-black text-orange-200">02</p>
                    <p class="mt-2 text-sm text-white/60">Добави данни</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                    <p class="text-2xl font-black text-amber-200">03</p>
                    <p class="mt-2 text-sm text-white/60">Получавай заявки</p>
                </div>
            </div>
        </section>

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-3 shadow-2xl shadow-black/30 backdrop-blur-2xl">
            <div class="rounded-[26px] border border-white/10 bg-slate-950/70 p-6 sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Регистрация</p>
                <h2 class="mt-3 text-3xl font-black">Създай нов профил</h2>
                <p class="mt-3 text-sm leading-6 text-white/60">Избери дали търсиш услуга като клиент или предлагаш услуги като изпълнител.</p>

                <form action="{{ route('register.post') }}" method="POST" class="mt-7 space-y-4">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Име</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Твоето име" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                        @error('name')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Имейл адрес</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                        @error('email')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Тип профил</label>
                        <select name="role" class="min-h-12 w-full rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                            <option value="customer" {{ in_array(old('role', 'customer'), ['customer', 'client'], true) ? 'selected' : '' }}>Търся услуга</option>
                            <option value="business" {{ old('role') == 'business' ? 'selected' : '' }}>Предлагам услуги</option>
                        </select>
                        @error('role')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white/75">Парола</label>
                            <input type="password" name="password" placeholder="Минимум 8 символа" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            @error('password')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white/75">Повтори парола</label>
                            <input type="password" name="password_confirmation" placeholder="Повтори паролата" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                        </div>
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25 transition hover:scale-[1.01]">
                        Регистрация
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-white/60">
                    Вече имаш профил?
                    <a href="{{ route('login') }}" class="font-bold text-orange-300 hover:text-white">Влез</a>
                </p>
            </div>
        </section>
    </main>
    @include('partials.mobile-bottom-nav')
</body>
</html>

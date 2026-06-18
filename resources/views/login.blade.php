<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход | BON Business Operating Network</title>
    <meta name="description" content="Влез в BON и продължи към своя профил, посока и следващи стъпки.">

    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bon-auth-grid {
            background-image:
                linear-gradient(to right, rgba(37, 99, 235, .075) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, .075) 1px, transparent 1px);
            background-size: 72px 72px;
            mask-image: radial-gradient(circle at 50% 30%, black 0%, transparent 78%);
        }
    </style>
</head>

<body class="bon-dark-page antialiased">
    <main class="relative min-h-screen overflow-x-hidden bg-[#020617] text-white">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(37,99,235,.18)_0%,rgba(2,6,23,.82)_42%,rgba(2,6,23,1)_100%)]"></div>
        <div class="bon-auth-grid pointer-events-none absolute inset-0 opacity-[.36]"></div>
        <div class="pointer-events-none absolute -top-40 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-40 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/22 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-[16rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full bg-violet-400/18 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-18rem] left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-300/18 blur-3xl"></div>

        <div class="relative z-10 flex min-h-screen flex-col px-4 py-3 sm:px-6 sm:py-5 lg:px-8">
            <header class="mx-auto flex w-full max-w-[1180px] items-center justify-between">
                <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3">
                    <div class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-xl shadow-violet-500/25 sm:h-[52px] sm:w-[52px] sm:text-2xl">
                        <span class="absolute inset-0 rounded-2xl bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,.42),transparent_38%)]"></span>
                        <span class="relative z-10">B</span>
                    </div>
                    <div class="leading-tight">
                        <div class="text-xl font-black tracking-tight text-[#070B1F] sm:text-[23px]">BON</div>
                        <div class="hidden text-sm font-medium text-slate-500 sm:block">Business Operating Network</div>
                    </div>
                </a>

                <a href="{{ route('register') }}" onclick="window.trackBonEvent('sign_up_start', { source: 'login_page' })" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-sm font-bold text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35 sm:px-5">
                    Регистрация
                </a>
            </header>

            <section class="mx-auto grid w-full max-w-[1180px] flex-1 items-center gap-6 py-5 sm:gap-8 sm:py-10 lg:grid-cols-[1fr_460px] lg:py-12">
                <aside class="hidden lg:block">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-300/20 bg-white/10 px-4 py-2 text-sm font-semibold text-blue-100 shadow-sm shadow-blue-950/20 backdrop-blur-xl">
                            <span class="text-violet-200">✦</span>
                            BON Access
                        </div>

                        <h1 class="mt-5 text-[52px] font-black leading-[1.04] tracking-[-0.055em] text-white">
                            Влез в <span class="bg-gradient-to-r from-blue-300 via-violet-300 to-fuchsia-300 bg-clip-text text-transparent">business operating platform.</span>
                        </h1>

                        <p class="mt-5 max-w-lg text-lg leading-8 text-slate-300">
                            BON събира профил, инструменти, задачи, консултации и статистика в един професионален работен център.
                        </p>

                        <div class="mt-8 grid max-w-xl grid-cols-3 gap-4">
                            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                                <p class="text-2xl font-black text-blue-200">BON</p>
                                <p class="mt-2 text-sm text-slate-400">анализ</p>
                            </div>
                            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                                <p class="text-2xl font-black text-violet-200">BON</p>
                                <p class="mt-2 text-sm text-slate-400">оператор</p>
                            </div>
                            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                                <p class="text-2xl font-black text-fuchsia-200">→</p>
                                <p class="mt-2 text-sm text-slate-400">действия</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <section class="rounded-[1.45rem] border border-white/70 bg-white/75 p-2 shadow-[0_34px_100px_rgba(30,41,100,.11)] backdrop-blur-2xl sm:rounded-[2rem] sm:p-3">
                    <div class="rounded-[1.25rem] border border-white/70 bg-white/75 p-4 shadow-sm shadow-blue-900/5 sm:rounded-[1.65rem] sm:p-8">
                        <div class="text-center sm:text-left">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Вход</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight text-[#070B1F] sm:text-3xl">Влез в BON</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Продължи към своя профил и управлявай следващите си стъпки.
                            </p>
                        </div>

                        <form action="{{ route('login.post') }}" method="POST" onsubmit="window.trackBonEvent('login_start', { source: 'login_page' })" class="mt-6 space-y-4 sm:mt-7 sm:space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email</label>
                                <input id="email" type="text" name="email" value="{{ old('email') }}" placeholder="example@email.com" class="min-h-12 w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                @error('email')
                                    <p class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                                <input id="password" type="password" name="password" placeholder="Въведи парола" class="min-h-12 w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-violet-300 focus:ring-4 focus:ring-violet-100">
                                @error('password')
                                    <p class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-3 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-blue-600 focus:ring-blue-500">
                                    Запомни ме
                                </label>
                                <a href="{{ route('contact') }}" class="font-bold text-blue-600 hover:text-violet-600">
                                    Забравена парола?
                                </a>
                            </div>

                            <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 py-4 font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35">
                                Вход
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-500">
                            Нямаш акаунт?
                            <a href="{{ route('register') }}" onclick="window.trackBonEvent('sign_up_start', { source: 'login_page_bottom' })" class="font-black text-blue-600 hover:text-violet-600">Създай профил</a>
                        </p>
                    </div>
                </section>
            </section>
        </div>
    </main>
</body>
</html>

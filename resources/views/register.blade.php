@php
    $selectedRole = old('role', request('role', 'customer'));
    $selectedRole = $selectedRole === 'client' ? 'customer' : $selectedRole;
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация | BON Business Operating Network</title>
    <meta name="description" content="Създай BON профил и започни от правилната стъпка — като бизнес, потребител или фрийлансър.">

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

<body class="antialiased">
    <main class="relative min-h-screen overflow-x-clip bg-[#F8FAFF] text-[#070B1F]">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.98)_0%,rgba(248,250,255,.82)_42%,rgba(248,250,255,1)_100%)]"></div>
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

                <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'registration_page_header' })" class="rounded-2xl border border-slate-200/80 bg-white/70 px-4 py-3 text-sm font-bold text-[#070B1F] shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-600 sm:px-5">
                    Вход
                </a>
            </header>

            <section class="mx-auto grid w-full max-w-[1180px] flex-1 items-center gap-6 py-5 sm:gap-8 sm:py-10 lg:grid-cols-[1fr_520px] lg:py-12">
                <aside class="hidden lg:block">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-violet-200/70 bg-white/80 px-4 py-2 text-sm font-semibold text-violet-700 shadow-sm shadow-violet-900/5 backdrop-blur-xl">
                            <span>✦</span>
                            BON Profile
                        </div>

                        <h1 class="mt-5 text-[52px] font-black leading-[1.04] tracking-[-0.055em] text-[#070B1F]">
                            Избери посока и започни от <span class="bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">правилната стъпка.</span>
                        </h1>

                        <p class="mt-5 max-w-lg text-lg leading-8 text-slate-600">
                            BON помага на бизнесите да изградят по-силно онлайн присъствие, а на хората - да откриват по-добре представени и доверени профили.
                        </p>

                        <div class="mt-8 grid max-w-xl gap-4">
                            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Бизнес</p>
                                <p class="mt-2 text-sm leading-6 text-slate-500">Открий какво те спира и подреди следващите действия.</p>
                            </div>
                            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl">
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-500">Потребител</p>
                                <p class="mt-2 text-sm leading-6 text-slate-500">Опиши нуждата си и стигни до правилното решение.</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <section class="rounded-[1.45rem] border border-white/70 bg-white/75 p-2 shadow-[0_34px_100px_rgba(30,41,100,.11)] backdrop-blur-2xl sm:rounded-[2rem] sm:p-3">
                    <div class="rounded-[1.25rem] border border-white/70 bg-white/75 p-4 shadow-sm shadow-blue-900/5 sm:rounded-[1.65rem] sm:p-8">
                        <div class="text-center sm:text-left">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Регистрация</p>
                            <h2 class="mt-3 text-2xl font-black tracking-tight text-[#070B1F] sm:text-3xl">Създай BON профил</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Избери посока — бизнес, потребител или фрийлансър — и започни от правилната стъпка.
                            </p>
                        </div>

                        <form action="{{ route('register.post') }}" method="POST" class="mt-6 space-y-4 sm:mt-7">
                            @csrf

                            <div>
                                <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Име</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Твоето име" class="min-h-12 w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                @error('name')<p class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Имейл</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" class="min-h-12 w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                                @error('email')<p class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-bold text-slate-700">Тип профил</label>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <label class="group cursor-pointer">
                                        <input type="radio" name="role" value="customer" class="peer sr-only" {{ in_array($selectedRole, ['customer', 'client'], true) ? 'checked' : '' }}>
                                        <span class="block rounded-2xl border border-slate-200/80 bg-white/75 p-3.5 shadow-sm shadow-blue-900/5 transition peer-checked:border-pink-300 peer-checked:bg-pink-50/70 peer-checked:ring-4 peer-checked:ring-pink-100 group-hover:-translate-y-0.5 sm:rounded-3xl sm:p-4">
                                            <span class="block text-sm font-black text-pink-500">Потребител</span>
                                            <span class="mt-1 block text-xs leading-5 text-slate-500">Търся решение</span>
                                        </span>
                                    </label>

                                    <label class="group cursor-pointer" onclick="window.trackBonEvent('business_registration_start', { source: 'registration_page' })">
                                        <input type="radio" name="role" value="business" class="peer sr-only" {{ $selectedRole === 'business' ? 'checked' : '' }}>
                                        <span class="block rounded-2xl border border-slate-200/80 bg-white/75 p-3.5 shadow-sm shadow-blue-900/5 transition peer-checked:border-blue-300 peer-checked:bg-blue-50/70 peer-checked:ring-4 peer-checked:ring-blue-100 group-hover:-translate-y-0.5 sm:rounded-3xl sm:p-4">
                                            <span class="block text-sm font-black text-blue-600">Бизнес</span>
                                            <span class="mt-1 block text-xs leading-5 text-slate-500">Искам бизнес профил</span>
                                        </span>
                                    </label>

                                    <label class="group cursor-pointer" onclick="window.trackBonEvent('freelancer_registration_start', { source: 'registration_page' })">
                                        <input type="radio" name="role" value="freelancer" class="peer sr-only" {{ $selectedRole === 'freelancer' ? 'checked' : '' }}>
                                        <span class="block rounded-2xl border border-slate-200/80 bg-white/75 p-3.5 shadow-sm shadow-blue-900/5 transition peer-checked:border-violet-300 peer-checked:bg-violet-50/70 peer-checked:ring-4 peer-checked:ring-violet-100 group-hover:-translate-y-0.5 sm:rounded-3xl sm:p-4">
                                            <span class="block text-sm font-black text-violet-600">Фрийлансър</span>
                                            <span class="mt-1 block text-xs leading-5 text-slate-500">Кандидатствам с кредити</span>
                                        </span>
                                    </label>
                                </div>
                                <p class="mt-3 rounded-2xl border border-violet-100 bg-violet-50/70 px-4 py-3 text-xs font-semibold leading-5 text-violet-700">
                                    Фрийлансър профилът получава 30 кредита месечно и кандидатства по обяви с по 3 кредита.
                                </p>
                                @error('role')<p class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Парола</label>
                                    <input id="password" type="password" name="password" placeholder="Минимум 8 символа" class="min-h-12 w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-violet-300 focus:ring-4 focus:ring-violet-100">
                                    @error('password')<p class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">Повтори парола</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Повтори паролата" class="min-h-12 w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 text-[#070B1F] outline-none transition placeholder:text-slate-400 focus:border-violet-300 focus:ring-4 focus:ring-violet-100">
                                </div>
                            </div>

                            <button type="submit" onclick="window.trackBonEvent('sign_up_start', { source: 'registration_page_submit', role: document.querySelector('input[name=role]:checked')?.value || 'unknown' })" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 py-4 font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35">
                                Създай профил
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-500">
                            Вече имаш акаунт?
                            <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'registration_page_bottom' })" class="font-black text-blue-600 hover:text-violet-600">Влез</a>
                        </p>
                    </div>
                </section>
            </section>
        </div>
    </main>
</body>
</html>

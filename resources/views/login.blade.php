<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_16%_18%,rgba(37,99,235,0.22),transparent_28%),radial-gradient(circle_at_82%_10%,rgba(168,85,247,0.18),transparent_30%),linear-gradient(180deg,#030712_0%,#061426_52%,#020812_100%)]"></div>

    <header class="border-b border-white/10 bg-[#030712]/80 backdrop-blur-2xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black shadow-lg shadow-blue-600/25">F</div>
                <div>
                    <p class="text-xl font-black">FixNow.bg</p>
                    <p class="hidden text-xs text-white/50 sm:block">Премиум marketplace за услуги</p>
                </div>
            </a>
            <nav class="hidden items-center gap-6 md:flex">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Начало</a>
                <a href="{{ route('services.index') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Услуги</a>
                <a href="{{ route('businesses.index') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Изпълнители</a>
                <a href="{{ route('business.landing') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">За изпълнители</a>
                <a href="{{ url('/contact') }}" class="text-sm font-semibold text-white/70 hover:text-cyan-200">Контакт</a>
            </nav>
            <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-cyan-400 to-violet-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-600/25">
                Регистрация
            </a>
        </div>
    </header>

    <main class="mx-auto grid min-h-[calc(100vh-80px)] max-w-7xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_480px] lg:px-8">
        <section class="hidden lg:block">
            <div class="max-w-xl">
                <p class="mb-4 inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-semibold text-cyan-100">Добре дошли отново</p>
                <h1 class="text-5xl font-black leading-tight">
                    Управлявайте профила си в
                    <span class="bg-gradient-to-r from-cyan-300 via-blue-400 to-violet-500 bg-clip-text text-transparent">FixNow.bg</span>
                </h1>
                <p class="mt-6 text-lg leading-8 text-white/70">
                    Следете запитвания, любими изпълнители, публикувани услуги и активност в една модерна платформа.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                        <p class="text-3xl font-black text-cyan-200">24/7</p>
                        <p class="mt-2 text-sm text-white/60">достъп до профила</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                        <p class="text-3xl font-black text-violet-200">EU</p>
                        <p class="mt-2 text-sm text-white/60">premium marketplace UX</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[32px] border border-white/10 bg-white/10 p-3 shadow-2xl shadow-black/30 backdrop-blur-2xl">
            <div class="rounded-[26px] border border-white/10 bg-slate-950/70 p-6 sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-cyan-200/80">Вход</p>
                <h2 class="mt-3 text-3xl font-black">Влез в профила си</h2>
                <p class="mt-3 text-sm leading-6 text-white/60">Продължи към своето табло, запитвания и любими изпълнители.</p>

                <form action="{{ route('login.post') }}" method="POST" class="mt-7 space-y-5">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Имейл адрес</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-cyan-300/50">
                        @error('email')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-white/75">Парола</label>
                        <input type="password" name="password" placeholder="Въведи парола" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-cyan-300/50">
                        @error('password')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-4 text-sm text-white/60">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" class="rounded border-white/20 bg-white/10">
                            Запомни ме
                        </label>
                        <a href="{{ route('contact') }}" class="font-semibold text-cyan-200 hover:text-white">Забравена парола?</a>
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white shadow-lg shadow-blue-600/25 transition hover:scale-[1.01]">
                        Вход
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-white/60">
                    Нямаш профил?
                    <a href="{{ route('register') }}" class="font-bold text-cyan-200 hover:text-white">Регистрирай се</a>
                </p>
            </div>
        </section>
    </main>
    @include('partials.mobile-bottom-nav')
</body>
</html>

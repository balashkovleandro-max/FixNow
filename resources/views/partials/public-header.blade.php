<header class="sticky top-0 z-50 border-b border-white/50 bg-[#F8FAFF]/85 backdrop-blur-2xl">
    <div class="mx-auto flex h-20 max-w-[1480px] items-center justify-between px-4 sm:px-6 lg:px-10">
        <a href="{{ url('/') }}" class="group flex items-center gap-3">
            <span class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-lg font-black text-white shadow-xl shadow-violet-500/25 transition group-hover:-translate-y-0.5">
                B
                <span class="absolute inset-0 rounded-2xl bg-white/15"></span>
            </span>
            <span class="leading-tight">
                <span class="block text-xl font-black tracking-tight text-[#070B1F]">BON</span>
                <span class="hidden text-xs font-semibold text-slate-500 sm:block">Business Operating Network</span>
            </span>
        </a>

        <nav class="hidden items-center gap-2 lg:flex" aria-label="Основна навигация">
            <a href="{{ url('/') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white/70 hover:text-blue-600">Начало</a>
            <a href="{{ route('business.landing') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white/70 hover:text-blue-600">За бизнеси</a>
            <a href="{{ route('bon.freelancers') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white/70 hover:text-blue-600">Фрилансъри</a>
            <a href="{{ route('bon.tools') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white/70 hover:text-blue-600">Инструменти</a>
            <a href="{{ route('plans') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white/70 hover:text-blue-600">Планове</a>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            @guest
                <a href="{{ route('login') }}" class="rounded-2xl border border-slate-200/80 bg-white/70 px-5 py-3 text-sm font-black text-[#070B1F] shadow-sm transition hover:border-blue-200 hover:text-blue-600">
                    Вход
                </a>
                <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35">
                    Регистрация
                </a>
            @endguest

            @auth
                <a href="{{ route('dashboard') }}" class="rounded-2xl border border-slate-200/80 bg-white/70 px-5 py-3 text-sm font-black text-[#070B1F] shadow-sm transition hover:border-blue-200 hover:text-blue-600">
                    Табло
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35">
                        Изход
                    </button>
                </form>
            @endauth
        </div>

        <details class="group relative md:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-2xl border border-white/70 bg-white/75 text-slate-700 shadow-lg shadow-blue-900/5 backdrop-blur-xl transition hover:text-blue-600" aria-label="Меню">
                <svg class="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                </svg>
                <svg class="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
                </svg>
            </summary>

            <div class="absolute right-0 top-14 w-[min(20rem,calc(100vw-2rem))] rounded-[1.75rem] border border-white/70 bg-white/90 p-3 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl">
                <div class="grid gap-1">
                    <a href="{{ url('/') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Начало</a>
                    <a href="{{ route('business.landing') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">За бизнеси</a>
                    <a href="{{ route('bon.freelancers') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Фрилансъри</a>
                    <a href="{{ route('bon.tools') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Инструменти</a>
                    <a href="{{ route('plans') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Планове</a>

                    <div class="mt-2 h-px bg-slate-200/70"></div>

                    @guest
                        <a href="{{ route('login') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Вход</a>
                        <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-center text-sm font-black text-white shadow-lg shadow-violet-500/20">Регистрация</a>
                    @endguest

                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-600 hover:bg-blue-50 hover:text-blue-700">Табло</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-center text-sm font-black text-white shadow-lg shadow-violet-500/20">Изход</button>
                        </form>
                    @endauth
                </div>
            </div>
        </details>
    </div>
</header>

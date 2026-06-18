<header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/82 backdrop-blur-2xl">
    <div class="mx-auto flex h-16 max-w-[1480px] items-center justify-between px-4 sm:h-20 sm:px-6 lg:px-10">
        <a href="{{ url('/') }}" class="group flex items-center gap-3">
            <span class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-base font-black text-white shadow-xl shadow-violet-500/25 transition group-hover:-translate-y-0.5 sm:h-11 sm:w-11 sm:text-lg">
                B
                <span class="absolute inset-0 rounded-2xl bg-white/15"></span>
            </span>
            <span class="leading-tight">
                <span class="block text-lg font-black tracking-tight text-white sm:text-xl">BON</span>
                <span class="hidden text-xs font-semibold text-slate-400 sm:block">Business Operating Network</span>
            </span>
        </a>

        <nav class="hidden items-center gap-2 lg:flex" aria-label="Основна навигация">
            <a href="{{ url('/') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->path() === '/' ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">Начало</a>
            <a href="{{ route('search') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->routeIs('search') ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">Търсене</a>
            <a href="{{ route('business.landing') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->routeIs('business.landing') ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">За бизнеси</a>
            <a href="{{ route('bon.freelancers') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->routeIs('bon.freelancers') ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">Фрилансъри</a>
            <a href="{{ route('freelancer.projects.index') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->routeIs('freelancer.projects.*') ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">Задачи</a>
            <a href="{{ route('bon.tools') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->routeIs('bon.tools') ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">Инструменти</a>
            <a href="{{ route('plans') }}" class="rounded-2xl px-4 py-2.5 text-sm font-bold {{ request()->routeIs('plans') ? 'bg-white/10 text-white' : 'text-slate-300' }} transition hover:bg-white/10 hover:text-white">Планове</a>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            @guest
                <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'header' })" class="rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:border-blue-300/30 hover:bg-white/10">
                    Вход
                </a>
                <a href="{{ route('register') }}" onclick="window.trackBonEvent('sign_up_start', { source: 'header' })" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5 hover:shadow-violet-500/35">
                    Регистрация
                </a>
            @endguest

            @auth
                <a href="{{ route('dashboard') }}" class="rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:border-blue-300/30 hover:bg-white/10">
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

        <details data-mobile-menu class="group relative md:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-white shadow-lg shadow-black/20 backdrop-blur-xl transition hover:bg-white/15" aria-label="Меню">
                <svg class="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                </svg>
                <svg class="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
                </svg>
            </summary>

            <div class="absolute right-0 top-12 max-h-[calc(100dvh-5rem)] w-[min(21rem,calc(100vw-2rem))] overflow-y-auto rounded-[1.75rem] border border-white/10 bg-slate-950/95 p-3 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:top-14">
                <div class="grid gap-1">
                    <a href="{{ url('/') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Начало</a>
                    <a href="{{ route('search') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Търсене</a>
                    <a href="{{ route('business.landing') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">За бизнеси</a>
                    <a href="{{ route('bon.freelancers') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Фрилансъри</a>
                    <a href="{{ route('freelancer.projects.index') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Задачи</a>
                    <a href="{{ route('bon.tools') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Инструменти</a>
                    <a href="{{ route('plans') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Планове</a>

                    <div class="mt-2 h-px bg-white/10"></div>

                    @guest
                        <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'mobile_header' })" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Вход</a>
                        <a href="{{ route('register') }}" onclick="window.trackBonEvent('sign_up_start', { source: 'mobile_header' })" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-center text-sm font-black text-white shadow-lg shadow-violet-500/20">Регистрация</a>
                    @endguest

                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-slate-300 hover:bg-white/10 hover:text-white">Табло</a>
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

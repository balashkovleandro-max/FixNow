<header class="relative z-30 border-b border-slate-200/60 bg-white/80 backdrop-blur-2xl">
    <div class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between px-5 sm:px-8 lg:px-12">
        <a href="{{ route('bon.index') }}" class="flex min-w-0 items-center gap-3" aria-label="BON начало">
            <span class="relative grid size-10 shrink-0 place-items-center rounded-2xl bg-white shadow-lg shadow-blue-950/[0.08] ring-1 ring-slate-200/80">
                <span class="absolute inset-2 rounded-xl bg-gradient-to-br from-blue-600 via-violet-600 to-pink-500 opacity-15"></span>
                <span class="relative bg-gradient-to-br from-blue-700 via-violet-700 to-[#10133A] bg-clip-text text-[25px] font-black leading-none text-transparent">B</span>
            </span>
            <span class="leading-tight">
                <span class="block text-[21px] font-black tracking-tight text-[#060B2B]">BON</span>
                <span class="hidden text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400 sm:block">Business Operating Network</span>
            </span>
        </a>

        <nav class="hidden items-center gap-9 text-[14px] font-semibold text-[#151B3D] lg:flex" aria-label="BON навигация">
            <a href="{{ route('home') }}" class="transition hover:text-blue-600">Начало</a>
            <a href="{{ route('business.landing') }}" class="transition hover:text-blue-600">За бизнеси</a>
            <a href="{{ route('bon.tools') }}" class="transition hover:text-blue-600">Инструменти</a>
            <a href="{{ route('plans') }}" class="transition hover:text-blue-600">Планове</a>
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('login') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-[#151B3D] shadow-sm shadow-slate-900/[0.03] transition hover:border-blue-200 hover:text-blue-600">
                Вход
            </a>
            <a href="{{ route('register') }}" class="rounded-2xl bg-[#080D2F] px-5 py-3 text-sm font-semibold text-white shadow-xl shadow-blue-950/[0.12] transition hover:-translate-y-0.5 hover:bg-[#10184A]">
                Регистрация
            </a>
        </div>

        <details class="group relative lg:hidden">
            <summary class="flex size-11 cursor-pointer list-none items-center justify-center rounded-2xl border border-slate-200 bg-white text-[#060B2B] shadow-sm [&::-webkit-details-marker]:hidden" aria-label="Отвори меню">
                <span class="space-y-1.5">
                    <span class="block h-0.5 w-5 rounded-full bg-[#060B2B]"></span>
                    <span class="block h-0.5 w-5 rounded-full bg-[#060B2B]"></span>
                    <span class="block h-0.5 w-5 rounded-full bg-[#060B2B]"></span>
                </span>
            </summary>
            <div class="absolute right-0 mt-3 w-[min(20rem,calc(100vw-2rem))] rounded-[26px] border border-slate-200 bg-white/[0.96] p-3 shadow-2xl shadow-blue-950/[0.10] backdrop-blur-2xl">
                <a href="{{ route('home') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-blue-50">Начало</a>
                <a href="{{ route('business.landing') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-blue-50">За бизнеси</a>
                <a href="{{ route('bon.tools') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-blue-50">Инструменти</a>
                <a href="{{ route('plans') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-blue-50">Планове</a>
                <div class="mt-2 grid gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('bon.business-problem') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-4 py-3 text-center text-sm font-bold text-white">Имам бизнес проблем</a>
                    <a href="{{ route('login') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-800">Вход</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-[#080D2F] px-4 py-3 text-center text-sm font-semibold text-white">Регистрация</a>
                </div>
            </div>
        </details>
    </div>
</header>

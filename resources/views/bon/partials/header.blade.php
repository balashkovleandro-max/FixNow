<header class="relative z-30 border-b border-white/10 bg-[#06101f]/78 backdrop-blur-2xl">
    <div class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between px-5 sm:px-8 lg:px-12">
        <a href="{{ route('bon.index') }}" class="flex min-w-0 items-center gap-3" aria-label="BON начало">
            <span class="relative grid size-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-pink-500 shadow-lg shadow-violet-950/30 ring-1 ring-white/15">
                <span class="absolute inset-0 rounded-2xl bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,.42),transparent_42%)]"></span>
                <span class="relative text-[25px] font-black leading-none text-white">B</span>
            </span>
            <span class="leading-tight">
                <span class="block text-[21px] font-black tracking-tight text-white">BON</span>
                <span class="hidden text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400 sm:block">Business Operating Network</span>
            </span>
        </a>

        <nav class="hidden items-center gap-9 text-[14px] font-semibold text-slate-300 lg:flex" aria-label="BON навигация">
            <a href="{{ route('home') }}" class="transition hover:text-blue-300">Начало</a>
            <a href="{{ route('business.landing') }}" class="transition hover:text-blue-300">За бизнеси</a>
            <a href="{{ route('bon.tools') }}" class="transition hover:text-blue-300">Инструменти</a>
            <a href="{{ route('plans') }}" class="transition hover:text-blue-300">Планове</a>
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('login') }}" class="rounded-2xl border border-white/12 bg-white/[0.06] px-5 py-3 text-sm font-semibold text-slate-200 shadow-sm shadow-black/10 transition hover:border-blue-400/40 hover:text-white">
                Вход
            </a>
            <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 py-3 text-sm font-semibold text-white shadow-xl shadow-violet-950/30 transition hover:-translate-y-0.5">
                Регистрация
            </a>
        </div>

        <details class="group relative lg:hidden">
            <summary class="flex size-11 cursor-pointer list-none items-center justify-center rounded-2xl border border-white/12 bg-white/[0.06] text-white shadow-sm [&::-webkit-details-marker]:hidden" aria-label="Отвори меню">
                <span class="space-y-1.5">
                    <span class="block h-0.5 w-5 rounded-full bg-white"></span>
                    <span class="block h-0.5 w-5 rounded-full bg-white"></span>
                    <span class="block h-0.5 w-5 rounded-full bg-white"></span>
                </span>
            </summary>
            <div class="absolute right-0 mt-3 w-[min(20rem,calc(100vw-2rem))] rounded-[26px] border border-white/12 bg-[#081426]/[0.96] p-3 shadow-2xl shadow-black/30 backdrop-blur-2xl">
                <a href="{{ route('home') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 hover:bg-white/[0.08]">Начало</a>
                <a href="{{ route('business.landing') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 hover:bg-white/[0.08]">За бизнеси</a>
                <a href="{{ route('bon.tools') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 hover:bg-white/[0.08]">Инструменти</a>
                <a href="{{ route('plans') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 hover:bg-white/[0.08]">Планове</a>
                <div class="mt-2 grid gap-2 border-t border-white/10 pt-3">
                    <a href="{{ route('bon.business-problem') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-4 py-3 text-center text-sm font-bold text-white">Имам бизнес проблем</a>
                    <a href="{{ route('login') }}" class="rounded-2xl border border-white/12 px-4 py-3 text-center text-sm font-semibold text-slate-200">Вход</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 py-3 text-center text-sm font-semibold text-white">Регистрация</a>
                </div>
            </div>
        </details>
    </div>
</header>

@php
    $profileHref = auth()->check() ? route('dashboard') : route('login');
    $profileLabel = auth()->check() ? 'Профил' : 'Вход';
    $itemBase = 'flex min-w-0 flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 text-[11px] font-black transition';
    $itemIdle = 'text-white/55 hover:bg-white/10 hover:text-white';
    $itemActive = 'bg-cyan-300/10 text-cyan-100 shadow-[0_0_24px_rgba(34,211,238,0.12)]';
    $isHome = request()->path() === '/';
@endphp

<nav class="fixed inset-x-0 bottom-0 z-[70] border-t border-white/10 bg-slate-950/92 px-3 pt-2 backdrop-blur-2xl md:hidden" style="padding-bottom: max(0.6rem, env(safe-area-inset-bottom));" aria-label="Мобилна навигация">
    <div class="mx-auto grid max-w-md grid-cols-5 gap-1">
        <a href="{{ url('/') }}" class="{{ $itemBase }} {{ $isHome ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h5v-6h4v6h5V10" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Начало</span>
        </a>
        <a href="{{ route('services.index') }}" class="{{ $itemBase }} {{ request()->routeIs('services.*') || request()->routeIs('seo.*') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5" stroke-linecap="round"/></svg>
            <span>Търсене</span>
        </a>
        <a href="{{ url('/categories') }}" class="{{ $itemBase }} {{ request()->is('categories') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z" stroke-linejoin="round"/></svg>
            <span>Категории</span>
        </a>
        <a href="{{ route('request.service') }}" class="{{ $itemBase }} {{ request()->routeIs('request.service') || request()->routeIs('request.service.store') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/><path d="M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z" stroke-linejoin="round"/></svg>
            <span>Заявка</span>
        </a>
        <a href="{{ $profileHref }}" class="{{ $itemBase }} {{ request()->routeIs('dashboard') || request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('business.billing') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0" stroke-linecap="round"/></svg>
            <span>{{ $profileLabel }}</span>
        </a>
    </div>
</nav>

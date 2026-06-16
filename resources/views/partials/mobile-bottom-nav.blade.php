@php
    $profileHref = auth()->check() ? route('dashboard') : route('login');
    $profileLabel = auth()->check() ? 'Профил' : 'Вход';
    $itemBase = 'flex min-w-0 flex-col items-center justify-center gap-1 rounded-2xl px-1 py-2 text-[11px] font-black leading-tight transition';
    $itemIdle = 'text-slate-500 hover:bg-white/80 hover:text-blue-600';
    $itemActive = 'bg-blue-50 text-blue-700 shadow-[0_0_24px_rgba(37,99,235,0.12)]';
    $isHome = request()->path() === '/';
@endphp

<nav class="fixed inset-x-0 bottom-0 z-[70] border-t border-white/70 bg-white/88 px-2 pt-2 shadow-[0_-18px_48px_rgba(30,64,175,0.12)] backdrop-blur-2xl md:hidden" style="padding-bottom: max(0.55rem, env(safe-area-inset-bottom));" aria-label="Мобилна навигация">
    <div class="mx-auto grid max-w-[430px] grid-cols-5 gap-1">
        <a href="{{ url('/') }}" class="{{ $itemBase }} {{ $isHome ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h5v-6h4v6h5V10" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Начало</span>
        </a>
        <a href="{{ route('business.landing') }}" class="{{ $itemBase }} {{ request()->routeIs('business.landing') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V8l8-4 8 4v12"/><path d="M9 20v-7h6v7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>За бизнеси</span>
        </a>
        <a href="{{ route('bon.tools') }}" class="{{ $itemBase }} {{ request()->routeIs('bon.tools') || request()->routeIs('business.insights.*') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V5"/><path d="M8 17V9M12 17V7M16 17v-5M20 17V4" stroke-linecap="round"/></svg>
            <span>Инструменти</span>
        </a>
        <a href="{{ route('plans') }}" class="{{ $itemBase }} {{ request()->routeIs('plans') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h10" stroke-linecap="round"/></svg>
            <span>Планове</span>
        </a>
        <a href="{{ $profileHref }}" class="{{ $itemBase }} {{ request()->routeIs('dashboard') || request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('business.billing') ? $itemActive : $itemIdle }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0" stroke-linecap="round"/></svg>
            <span>{{ $profileLabel }}</span>
        </a>
    </div>
</nav>

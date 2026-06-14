<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | BON</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-clip bg-[#050816] text-white antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_10%,rgba(37,99,235,.24),transparent_32%),radial-gradient(circle_at_84%_12%,rgba(236,72,153,.18),transparent_34%),radial-gradient(circle_at_50%_52%,rgba(124,58,237,.14),transparent_42%),linear-gradient(180deg,#050816_0%,#07111f_58%,#050816_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-[.16]" style="background-image: linear-gradient(to right, rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 72px 72px;"></div>

    @php
        $adminNav = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Бизнеси', 'route' => 'admin.businesses.index'],
            ['label' => 'Потребители', 'route' => 'admin.users.index'],
            ['label' => 'Заявки/консултации', 'route' => 'admin.requests.index'],
            ['label' => 'Оферти', 'route' => 'admin.offers.index'],
            ['label' => 'Абонаменти', 'route' => 'admin.subscriptions.index'],
            ['label' => 'Плащания', 'route' => 'admin.payments'],
            ['label' => 'Отзиви', 'route' => 'admin.reviews.index'],
            ['label' => 'Кредити', 'route' => 'admin.freelancer-credits.index'],
            ['label' => 'Категории', 'route' => 'admin.categories.index'],
            ['label' => 'Градове', 'route' => 'admin.cities.index'],
            ['label' => 'Настройки', 'route' => 'admin.settings'],
        ];
    @endphp

    <div class="mx-auto grid min-h-screen max-w-[1720px] gap-4 px-3 py-3 sm:px-5 lg:grid-cols-[280px_1fr] lg:py-5">
        <aside class="lg:sticky lg:top-5 lg:h-[calc(100dvh-2.5rem)]">
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/25 backdrop-blur-2xl lg:h-full">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 text-xl font-black shadow-xl shadow-violet-900/40">B</div>
                        <div>
                            <p class="text-xl font-black tracking-tight">BON Admin</p>
                            <p class="text-xs font-semibold text-white/45">Control Center</p>
                        </div>
                    </a>

                    <details class="relative lg:hidden">
                        <summary class="grid h-11 w-11 cursor-pointer list-none place-items-center rounded-2xl border border-white/10 bg-white/10 text-white/70 [&::-webkit-details-marker]:hidden">☰</summary>
                        <div class="absolute right-0 top-12 z-30 w-[min(22rem,calc(100vw-2rem))] rounded-3xl border border-white/10 bg-[#081224]/95 p-3 shadow-2xl shadow-black/30 backdrop-blur-xl">
                            <div class="grid gap-1">
                                @foreach($adminNav as $item)
                                    <a href="{{ route($item['route']) }}" class="rounded-2xl px-4 py-3 text-sm font-black {{ request()->routeIs($item['route']) ? 'bg-white text-[#070B1F]' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                <nav class="mt-7 hidden gap-1 lg:grid">
                    @foreach($adminNav as $item)
                        <a href="{{ route($item['route']) }}" class="rounded-2xl px-4 py-3 text-sm font-black transition {{ request()->routeIs($item['route']) ? 'bg-white text-[#070B1F] shadow-lg shadow-white/10' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('admin.activity.index') }}" class="rounded-2xl px-4 py-3 text-sm font-black transition {{ request()->routeIs('admin.activity.index') ? 'bg-white text-[#070B1F]' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">Audit лог</a>
                </nav>

                <div class="mt-7 grid gap-3 border-t border-white/10 pt-5">
                    <a href="{{ url('/') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-black text-white/70 transition hover:bg-white/10 hover:text-white">Към сайта</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl border border-rose-300/15 bg-rose-400/10 px-4 py-3 text-left text-sm font-black text-rose-100 transition hover:bg-rose-400/20">Изход</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="min-w-0 pb-10">
            <header class="mb-4 rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-blue-200/80">@yield('eyebrow', 'BON Admin')</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight sm:text-3xl">@yield('page-title', 'Admin панел')</h1>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/60">
                        {{ auth()->user()->name ?? 'Admin' }} · {{ now()->format('d.m.Y H:i') }}
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="mb-4 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-sm font-bold text-emerald-100">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-2xl border border-rose-300/20 bg-rose-400/10 p-4 text-sm font-bold text-rose-100">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

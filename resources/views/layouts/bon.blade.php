<!DOCTYPE html>
<html lang="bg" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BON — от проблем до решение')</title>
    <meta name="description" content="@yield('meta_description', 'BON е Business Operating Network — система за бизнес диагностика, стратегия, действие и резултат.')">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen overflow-x-clip bg-[#020617] font-sans text-slate-50 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute left-1/2 top-[-28rem] h-[52rem] w-[72rem] -translate-x-1/2 rounded-full bg-blue-500/[0.18] blur-3xl"></div>
        <div class="absolute right-[-16rem] top-48 h-[36rem] w-[36rem] rounded-full bg-pink-500/[0.14] blur-3xl"></div>
        <div class="absolute bottom-[-20rem] left-[-12rem] h-[42rem] w-[42rem] rounded-full bg-violet-600/[0.16] blur-3xl"></div>
        <div class="absolute inset-x-0 top-0 h-48 bg-gradient-to-b from-[#071226] via-[#071226]/70 to-transparent"></div>
    </div>

    @include('bon.partials.header')

    <main>
        @yield('content')
    </main>

    @stack('body-end')
</body>
</html>

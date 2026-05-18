<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->title }} | FixNow.bg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(37,99,235,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(168,85,247,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @php
            $business = $service->user;
        @endphp

        <a href="{{ route('services.index') }}" class="mb-6 inline-flex rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Назад към услугите</a>

        <section class="grid gap-6 lg:grid-cols-[1fr_420px]">
            <div class="overflow-hidden rounded-[32px] border border-white/10 bg-white/10 shadow-2xl shadow-black/25 backdrop-blur-xl">
                @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="h-[360px] w-full object-cover">
                @else
                    <div class="flex h-[360px] items-center justify-center bg-gradient-to-br from-cyan-400/20 via-blue-500/10 to-violet-600/20 text-white/50">
                        Галерията ще бъде добавена от изпълнителя
                    </div>
                @endif
                <div class="p-6 sm:p-8">
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-cyan-400/10 px-3 py-1 text-xs font-bold text-cyan-200">{{ $service->category }}</span>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/70">{{ $service->city }}</span>
                        @if($business?->is_verified)
                            <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-bold text-emerald-200">Потвърден</span>
                        @endif
                        @if($business?->isPremium())
                            <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-bold text-violet-200">Premium</span>
                        @endif
                    </div>
                    <h1 class="text-3xl font-black leading-tight sm:text-5xl">{{ $service->title }}</h1>
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-white/70">
                        <span class="text-amber-300">★★★★★</span>
                        <span>4.8 рейтинг</span>
                        <span>·</span>
                        <span>Отговор до 30 мин.</span>
                        <span>·</span>
                        <span>Работи в събота</span>
                    </div>
                    <div class="mt-8">
                        <h2 class="text-xl font-black">Описание</h2>
                        <p class="mt-3 text-base leading-8 text-white/70">{{ $service->description }}</p>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        @foreach(['Гаранция за качество','Бърз отговор','Възможност за снимки'] as $chip)
                            <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4 text-sm font-bold text-white/75">{{ $chip }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-8 lg:self-start">
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-xl">
                    @if($service->price)
                        <p class="text-3xl font-black text-cyan-200">{{ number_format($service->price, 2) }} €</p>
                    @else
                        <p class="text-2xl font-black text-cyan-200">Цена по договаряне</p>
                    @endif
                    <p class="mt-2 text-sm text-white/60">Публикувана на {{ $service->created_at->format('d.m.Y') }}</p>

                    <div class="mt-6 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                        <p class="text-sm text-white/50">Публикувана от</p>
                        <p class="mt-1 text-lg font-black">{{ $business->business_name ?? $business->name ?? 'Профил на изпълнител' }}</p>
                        <p class="mt-1 text-sm text-white/60">{{ $service->phone }}</p>
                    </div>

                    <div class="mt-5 grid gap-3">
                        <a href="{{ route('request.service') }}" class="rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-5 py-4 text-center font-black text-white shadow-lg shadow-blue-600/25">Изпрати запитване</a>
                        <a href="tel:{{ $service->phone }}" class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Обади се</a>
                        <a href="{{ route('request.service', ['service' => $service->title]) }}" class="rounded-2xl border border-cyan-300/20 bg-cyan-300/10 px-5 py-4 text-center font-black text-cyan-100">Заяви оферта</a>
                    </div>
                </div>
            </aside>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

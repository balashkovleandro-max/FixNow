<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $intro }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(37,99,235,0.22),transparent_30%),radial-gradient(circle_at_84%_18%,rgba(168,85,247,0.17),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>


    @include('partials.public-header')

<main>
        <section class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-12">
            <div class="grid gap-8 overflow-hidden rounded-[36px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl lg:grid-cols-[1fr_380px] lg:items-end sm:p-10">
                <div>
                    <div class="mb-5 flex flex-wrap gap-2">
                        <span class="rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-black text-cyan-100">{{ $city }}</span>
                        @if($category)
                            <span class="rounded-full border border-violet-300/20 bg-violet-400/10 px-4 py-2 text-sm font-black text-violet-100">{{ $category['label'] }}</span>
                        @else
                            <span class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-white/70">Всички категории</span>
                        @endif
                    </div>

                    <h1 class="max-w-4xl text-4xl font-black leading-tight sm:text-6xl">{{ $h1 }}</h1>
                    <p class="mt-5 max-w-3xl text-base leading-8 text-white/70">{{ $intro }}</p>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="inline-flex justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-7 py-4 font-black text-white shadow-lg shadow-blue-600/25">Заяви оферта</a>
                        <a href="{{ route('business.landing') }}" class="inline-flex justify-center rounded-2xl border border-white/10 bg-white/5 px-7 py-4 font-black text-white hover:bg-white/10">Стани изпълнител</a>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Бързи връзки</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($quickCategoryLinks as $link)
                            <a href="{{ $link['url'] }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:border-cyan-300/30 hover:text-cyan-100">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                    <div class="mt-5 grid gap-2">
                        @foreach($canonicalRoutes as $link)
                            <a href="{{ $link['url'] }}" class="rounded-2xl bg-white/5 px-4 py-3 text-sm font-bold text-white/65 hover:bg-white/10 hover:text-white">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-2xl font-black">Намерени изпълнители</h2>
                    <p class="mt-2 text-sm text-white/55">Показваме само active и trial изпълнители, подредени по Premium, verified, рейтинг, препоръки и активност.</p>
                </div>
                <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-black text-white/70">{{ $businesses->count() }} резултата</span>
            </div>

            @if($businesses->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach($businesses as $business)
                        @include('partials.business-card', ['business' => $business])
                    @endforeach
                </div>
            @else
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-8 text-center shadow-xl shadow-black/20 backdrop-blur-xl">
                    <p class="text-2xl font-black">Все още няма активни изпълнители тук</p>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-white/60">Пусни заявка и ще ти помогнем да намериш подходящ изпълнител. Ако си бизнес в този град или категория, добави профил и стани видим за първите клиенти.</p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('request.service') }}" class="inline-flex justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-600 px-6 py-4 font-black text-white">Пусни заявка</a>
                        <a href="{{ route('business.landing') }}" class="inline-flex justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-4 font-black text-white hover:bg-white/10">Стани изпълнител</a>
                    </div>
                </div>
            @endif
        </section>

        <section class="mx-auto grid max-w-[1500px] gap-5 px-4 py-7 sm:px-6 lg:grid-cols-[1fr_0.8fr] lg:px-12">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                <div class="mb-5 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Ranking</p>
                        <h2 class="mt-2 text-2xl font-black">Топ изпълнители в тази страница</h2>
                    </div>
                    <a href="{{ route('top.businesses') }}" class="text-sm font-bold text-cyan-200 hover:text-cyan-100">Всички класации →</a>
                </div>
                <div class="grid gap-3">
                    @forelse($topBusinesses as $business)
                        <a href="{{ route('businesses.show', $business) }}" class="flex items-center justify-between gap-3 rounded-2xl bg-slate-950/45 px-4 py-3 hover:bg-white/10">
                            <span class="font-bold">{{ $business->business_name ?: $business->name }}</span>
                            <span class="text-xs text-white/50">{{ (int) data_get($business, 'growth_recommendations_count', 0) }} препоръки</span>
                        </a>
                    @empty
                        <p class="rounded-2xl bg-slate-950/45 px-4 py-3 text-sm text-white/55">Класацията ще се появи, когато има активни изпълнители.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200/80">Последни отзиви</p>
                <div class="mt-5 grid gap-3">
                    @forelse($latestReviews as $review)
                        <article class="rounded-2xl bg-slate-950/45 px-4 py-3">
                            <p class="font-black">{{ $review->reviewer_name }} <span class="text-amber-200">{{ str_repeat('★', (int) $review->rating) }}</span></p>
                            <p class="mt-1 text-xs text-cyan-200">{{ $review->business?->business_name ?: $review->business?->name }}</p>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-white/60">{{ $review->comment }}</p>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-slate-950/45 px-4 py-3 text-sm text-white/55">Все още няма одобрени отзиви за тази страница.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-12">
            <div class="grid gap-5 lg:grid-cols-3">
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl lg:col-span-2">
                    <h2 class="text-2xl font-black">{{ $h1 }} във FixNow.bg</h2>
                    <p class="mt-4 text-sm leading-7 text-white/65">
                        Във FixNow.bg можете да намерите проверени изпълнители и услуги в {{ $city }}. Сравнете профили, отзиви, препоръки и се свържете директно или изпратете заявка за оферта.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($internalLinks as $link)
                            <a href="{{ $link['url'] }}" class="rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-2 text-sm font-bold text-white/70 hover:border-cyan-300/30 hover:text-cyan-100">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-xl shadow-black/20 backdrop-blur-xl">
                    <h2 class="text-xl font-black">Популярни градове</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse($popularCities as $popularCity)
                            <a href="{{ route('seo.city', ['city' => $popularCity['slug']]) }}" class="rounded-2xl bg-slate-950/45 px-4 py-2 text-sm font-bold text-white/70 hover:text-cyan-100">{{ $popularCity['name'] }}</a>
                        @empty
                            <a href="{{ route('seo.city', ['city' => 'pleven']) }}" class="rounded-2xl bg-slate-950/45 px-4 py-2 text-sm font-bold text-white/70 hover:text-cyan-100">Плевен</a>
                            <a href="{{ route('seo.city', ['city' => 'sofia']) }}" class="rounded-2xl bg-slate-950/45 px-4 py-2 text-sm font-bold text-white/70 hover:text-cyan-100">София</a>
                        @endforelse
                    </div>

                    <h2 class="mt-6 text-xl font-black">Популярни категории</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse($popularCategories as $popularCategory)
                            <a href="{{ route('businesses.index', ['category' => $popularCategory['name']]) }}" class="rounded-2xl bg-slate-950/45 px-4 py-2 text-sm font-bold text-white/70 hover:text-cyan-100">{{ $popularCategory['name'] }}</a>
                        @empty
                            @foreach($quickCategoryLinks as $link)
                                <a href="{{ $link['url'] }}" class="rounded-2xl bg-slate-950/45 px-4 py-2 text-sm font-bold text-white/70 hover:text-cyan-100">{{ $link['label'] }}</a>
                            @endforeach
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </main>
    @include('partials.public-footer')
    @include('partials.mobile-bottom-nav')
</body>
</html>

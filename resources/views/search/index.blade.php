@php
    $query = request('q');
    $city = request('city');
    $category = request('category');
    $service = request('service');
@endphp

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Търсене в BON | Бизнеси и фрийлансъри</title>
    <meta name="description" content="Търсете публични BON профили по град, услуга, категория или име на бизнес и фрийлансър.">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="bon-dark-page min-h-screen overflow-x-hidden text-white antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute -top-40 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="absolute -top-40 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="absolute bottom-[-18rem] left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.22]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>
    </div>

    @include('partials.public-header')

    <main class="mx-auto max-w-[1440px] px-4 py-8 sm:px-6 lg:px-8">
        <section class="rounded-[2rem] border border-white/70 bg-white/75 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:p-8 lg:p-10">
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">BON Search</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Открий подходящ бизнес или специалист.</h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                        Търсенето е публично и не изисква регистрация. Въведи град, услуга, категория или име и виж профили с ясна информация, доверие и директен контакт.
                    </p>
                </div>

                <form action="{{ route('search') }}" method="GET" class="grid gap-3 rounded-[1.5rem] border border-white/80 bg-white/80 p-3 shadow-xl shadow-blue-900/5 backdrop-blur-xl sm:grid-cols-2 lg:grid-cols-4">
                    <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                        Име / ключова дума
                        <input name="q" value="{{ $query }}" placeholder="Studio, Laravel, салон..." class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                    </label>

                    <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                        Град
                        <input name="city" value="{{ $city }}" placeholder="София, Варна..." class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                    </label>

                    <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                        Категория
                        <input name="category" list="bon-search-category-options" value="{{ $category }}" placeholder="Избери или напиши категория" class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                        <datalist id="bon-search-category-options">
                            @foreach($categories as $option)
                                <option value="{{ $option }}">
                            @endforeach
                        </datalist>
                    </label>

                    <label class="grid gap-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                        Услуга
                        <input name="service" value="{{ $service }}" placeholder="фризьор, уеб дизайн..." class="min-h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-base font-semibold normal-case tracking-normal outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 sm:text-sm">
                    </label>

                    <div class="sm:col-span-2 lg:col-span-4">
                        <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5">
                            Търси в BON
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="mt-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Резултати</p>
                    <h2 class="mt-2 text-2xl font-black sm:text-3xl">{{ $results->total() }} профила</h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-slate-500">
                    Premium, проверени и по-добре попълнени профили получават по-силен визуален контекст в резултатите.
                </p>
            </div>

            @if($results->count())
                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($results as $profile)
                        @php
                            $isFreelancer = $profile->isFreelancer();
                            $displayName = $isFreelancer ? $profile->name : ($profile->business_name ?: $profile->name);
                            $profileUrl = $isFreelancer ? route('freelancers.show', $profile) : route('businesses.show', $profile);
                            $categoriesForProfile = collect($profile->serviceCategories())
                                ->map(fn ($profileCategory) => \App\Support\CategoryCatalog::displayName($profileCategory))
                                ->filter()
                                ->unique()
                                ->values()
                                ->all();
                            $citiesForProfile = array_values(array_filter($profile->serviceCities()));
                            $businessCategory = $profile->business_category ? \App\Support\CategoryCatalog::displayName($profile->business_category) : null;
                            $primaryCategory = $categoriesForProfile[0] ?? ($businessCategory ?: ($isFreelancer ? 'Фрийлансър' : 'Бизнес профил'));
                            $primaryCity = $citiesForProfile[0] ?? ($profile->city ?: 'Онлайн / България');
                            $averageRating = $profile->averageRating();
                            $reviewsCount = $profile->approvedReviewsCount();
                            $bookingEnabled = (bool) ($profile->booking_enabled ?? false);
                            $coverImage = null;

                            if (!$isFreelancer && $profile->relationLoaded('businessPhotos')) {
                                $coverImage = optional($profile->businessPhotos->first())->path;
                            }

                            $avatar = $profile->avatar ?: $coverImage;
                        @endphp

                        <article class="group flex h-full flex-col overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/80 shadow-2xl shadow-blue-900/5 backdrop-blur-2xl transition hover:-translate-y-1 hover:shadow-blue-900/10">
                            <div class="relative h-36 overflow-hidden bg-gradient-to-br from-blue-100 via-violet-100 to-pink-100">
                                @if($avatar)
                                    <img src="{{ asset('storage/' . $avatar) }}" alt="{{ $displayName }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#070B1F]/40 to-transparent"></div>
                                @else
                                    <div class="flex h-full items-center justify-center text-5xl font-black text-white">
                                        <span class="flex h-20 w-20 items-center justify-center rounded-[2rem] bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 shadow-2xl shadow-violet-500/25">
                                            {{ mb_strtoupper(mb_substr($displayName, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                                    @if($profile->isPremium())
                                        <span class="rounded-full bg-fuchsia-500 px-3 py-1 text-xs font-black text-white shadow-lg shadow-fuchsia-500/25">Premium</span>
                                    @endif
                                    @if($profile->is_verified)
                                        <span class="rounded-full bg-emerald-500 px-3 py-1 text-xs font-black text-white shadow-lg shadow-emerald-500/20">Проверен</span>
                                    @endif
                                    @if($bookingEnabled)
                                        <span class="rounded-full bg-blue-600 px-3 py-1 text-xs font-black text-white shadow-lg shadow-blue-600/20">Онлайн записване</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex flex-wrap items-center gap-2 text-sm font-bold">
                                    <span class="text-blue-700">{{ $primaryCategory }}</span>
                                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                    <span class="text-slate-500">{{ $primaryCity }}</span>
                                </div>

                                <h3 class="mt-3 text-xl font-black tracking-tight text-[#070B1F]">
                                    <a href="{{ $profileUrl }}" class="transition hover:text-blue-700">{{ $displayName }}</a>
                                </h3>

                                <p class="mt-3 line-clamp-3 min-h-[4.5rem] text-sm leading-6 text-slate-600">
                                    {{ $profile->short_description ?: $profile->description ?: 'Профил в BON с ясна информация, услуги, контакт и доверителен контекст за клиенти.' }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach(array_slice($categoriesForProfile, 0, 3) as $chip)
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $chip }}</span>
                                    @endforeach
                                    @foreach(array_slice($citiesForProfile, 0, 2) as $chip)
                                        @if($chip !== $primaryCity)
                                            <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700">{{ $chip }}</span>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl border border-slate-100 bg-white/70 p-3">
                                        @if($averageRating)
                                            <p class="font-black text-amber-500">{{ number_format($averageRating, 1, '.', '') }} ★</p>
                                            <p class="mt-1 text-xs font-bold text-slate-400">{{ $reviewsCount }} отзива</p>
                                        @else
                                            <p class="font-black text-slate-700">Нов</p>
                                            <p class="mt-1 text-xs font-bold text-slate-400">без отзиви</p>
                                        @endif
                                    </div>
                                    <div class="rounded-2xl border border-slate-100 bg-white/70 p-3">
                                        <p class="font-black text-violet-700">{{ data_get($profile->trustSummary(), 'trust_score', 0) }}</p>
                                        <p class="mt-1 text-xs font-bold text-slate-400">Trust Score</p>
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                    <a href="{{ $profileUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-4 text-sm font-black text-white shadow-lg shadow-violet-500/20">
                                        Виж профил
                                    </a>

                                    @if($profile->phone)
                                        <a href="{{ $isFreelancer ? 'tel:' . preg_replace('/[^\d+]/', '', $profile->phone) : route('businesses.track.phone', $profile) }}" onclick="window.trackBonEvent('phone_click', { source: 'search_results', profile_id: '{{ $profile->id }}', profile_type: '{{ $isFreelancer ? 'freelancer' : 'business' }}' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white/75 px-4 text-sm font-black text-slate-700">
                                            Обади се
                                        </a>
                                    @endif

                                    @if($bookingEnabled)
                                        <a href="{{ $profileUrl }}#booking" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 px-4 text-sm font-black text-blue-700 sm:col-span-2">
                                            Запази час
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $results->links() }}
                </div>
            @else
                <div class="mt-6 rounded-[2rem] border border-white/70 bg-white/80 p-8 text-center shadow-2xl shadow-blue-900/5 backdrop-blur-2xl">
                    <p class="text-3xl font-black">Няма намерени профили.</p>
                    <p class="mx-auto mt-3 max-w-2xl text-slate-600">Опитай с по-широка ключова дума, различен град или без избрана категория.</p>
                    <a href="{{ route('search') }}" class="mt-6 inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                        Изчисти търсенето
                    </a>
                </div>
            @endif
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} | BON Freelancer</title>
    <meta name="description" content="Публичен BON профил на фрийлансър с Trust Score, значки, завършени проекти и репутация.">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
    <body class="bon-dark-page min-h-screen overflow-x-hidden bg-[#020617] text-white">
    @include('partials.public-header')

    <main class="relative overflow-x-hidden">
        @php
            $bookingEnabled = (bool) data_get($user, 'booking_enabled', false);
        @endphp
        <div class="pointer-events-none absolute -top-44 left-[-12rem] h-[34rem] w-[34rem] rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-44 right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-fuchsia-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.20]" style="background-image: linear-gradient(to right, rgba(37,99,235,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(37,99,235,.08) 1px, transparent 1px); background-size: 72px 72px;"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-3 py-5 sm:px-6 sm:py-8 lg:px-8">
            <section class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-blue-900/10 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-start">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($trustSummary['badges'] as $badge)
                                <span class="rounded-full border border-white/70 bg-white/80 px-3 py-1 text-xs font-black text-blue-700 shadow-sm">{{ $badge }}</span>
                            @endforeach
                            @if($bookingEnabled)
                                <span class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 shadow-sm">Онлайн записване</span>
                            @endif
                        </div>
                        <div class="mt-4">
                            @include('partials.favorite-button', ['profile' => $user, 'variant' => 'dark'])
                            @auth
                                @if(auth()->id() === $user->id)
                                    <a href="{{ route('freelancer.profile.edit') }}" class="mt-3 inline-flex min-h-10 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black text-blue-700">
                                        Редактирай профил
                                    </a>
                                @endif
                            @endauth
                        </div>

                        <div class="mt-6 flex flex-col gap-5 sm:mt-8 sm:flex-row sm:items-center">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-[1.5rem] bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-3xl font-black text-white shadow-xl shadow-violet-500/25 sm:h-24 sm:w-24 sm:rounded-[2rem] sm:text-4xl">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">BON Freelancer</p>
                                <h1 class="mt-2 text-[32px] font-black tracking-tight sm:text-5xl">{{ $user->name }}</h1>
                                <p class="mt-3 text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">Профил на независим специалист в BON Talent Network.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3 sm:mt-8 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-2xl bg-blue-50 p-3.5 sm:rounded-3xl sm:p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-700">Trust Score</p>
                                <p class="mt-2 text-2xl font-black sm:text-3xl">{{ $trustSummary['trust_score'] }}/100</p>
                            </div>
                            <div class="rounded-2xl bg-violet-50 p-3.5 sm:rounded-3xl sm:p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-700">Завършени</p>
                                <p class="mt-2 text-2xl font-black sm:text-3xl">{{ $trustSummary['completed_projects_count'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-fuchsia-50 p-3.5 sm:rounded-3xl sm:p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-fuchsia-700">Успех</p>
                                <p class="mt-2 text-2xl font-black sm:text-3xl">{{ $trustSummary['success_rate'] }}%</p>
                            </div>
                            <div class="rounded-2xl bg-cyan-50 p-3.5 sm:rounded-3xl sm:p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">В BON от</p>
                                <p class="mt-2 text-2xl font-black sm:text-3xl">{{ $trustSummary['registered_year'] ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <aside class="rounded-[1.5rem] border border-white/70 bg-white/85 p-5 shadow-xl shadow-blue-900/5 sm:rounded-[2rem] sm:p-6">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Доверие</p>
                        <div class="mt-5 grid gap-3">
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Имейл</span>
                                <span class="font-black {{ $trustSummary['email_verified'] ? 'text-emerald-600' : 'text-slate-400' }}">{{ $trustSummary['email_verified'] ? 'Потвърден' : 'Непотвърден' }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Телефон</span>
                                <span class="font-black {{ $trustSummary['phone_verified'] ? 'text-emerald-600' : 'text-slate-400' }}">{{ $trustSummary['phone_verified'] ? 'Потвърден' : 'Непотвърден' }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Профил</span>
                                <span class="font-black text-blue-600">{{ $trustSummary['profile_completeness'] }}%</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span class="font-bold text-slate-600">Отговор</span>
                                <span class="font-black text-slate-700">{{ $trustSummary['response_label'] ? 'Средно ' . $trustSummary['response_label'] : 'Няма данни' }}</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            @php
                $freelancerCategories = collect($user->serviceCategories())
                    ->map(fn ($profileCategory) => \App\Support\CategoryCatalog::displayName($profileCategory))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                $freelancerCities = $user->serviceCities();
                $freelancerServices = $user->relationLoaded('services') ? $user->services : collect();
                $onlineWorkLabel = !empty($freelancerCities)
                    ? implode(', ', $freelancerCities)
                    : ($user->city ?: 'Онлайн / дистанционно');
            @endphp

            <section class="mt-6 grid gap-5 sm:mt-8 sm:gap-6 lg:grid-cols-[1fr_0.85fr]">
                <div class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Услуги и работа</p>
                    <h2 class="mt-3 text-2xl font-black sm:text-3xl">Какво предлага този фрийлансър</h2>
                    <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600">
                        {{ $user->description ?: $user->short_description ?: 'Профил на независим специалист с услуги, портфолио и история на кандидатстванията в BON.' }}
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-3xl border border-slate-100 bg-white/80 p-5">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-600">Умения / услуги</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse($freelancerCategories as $category)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $category }}</span>
                                @empty
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ $user->business_category ? \App\Support\CategoryCatalog::displayName($user->business_category) : 'Услуги по заявка' }}</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-100 bg-white/80 p-5">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-600">Локация / онлайн работа</p>
                            <p class="mt-4 text-lg font-black">{{ $onlineWorkLabel }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Може да работи по проекти локално или дистанционно според конкретната обява.</p>
                        </div>
                    </div>

                    @if($freelancerServices->isNotEmpty())
                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            @foreach($freelancerServices->take(4) as $service)
                                <article class="rounded-3xl border border-slate-100 bg-white/85 p-5">
                                    <p class="text-lg font-black">{{ $service->title }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $service->description ?: \App\Support\CategoryCatalog::displayName($service->category) }}</p>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="rounded-[1.5rem] border border-white/70 bg-white/85 p-5 shadow-xl shadow-fuchsia-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-fuchsia-600">Контакт</p>
                    <h2 class="mt-3 text-2xl font-black">Свържи се със специалиста</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Контактите са отделни от бизнес обявите. Този профил е за freelancer услуги и портфолио.</p>

                    <div class="mt-6 grid gap-3">
                        @if($user->phone)
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $user->phone) }}" onclick="window.trackBonEvent('phone_click', { source: 'freelancer_profile_page', profile_id: '{{ $user->id }}', profile_type: 'freelancer' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 text-sm font-black text-white shadow-lg shadow-blue-600/20">
                                Обади се
                            </a>
                        @endif
                        @if($user->email)
                            <a href="mailto:{{ $user->email }}" onclick="window.trackBonEvent('contact_click', { source: 'freelancer_profile_page_email', profile_id: '{{ $user->id }}', profile_type: 'freelancer' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-black text-slate-700 shadow-sm hover:text-blue-700">
                                Изпрати имейл
                            </a>
                        @endif
                        <a href="{{ $user->email ? 'mailto:' . $user->email . '?subject=' . rawurlencode('Покана за проект през BON') : route('login') }}" onclick="window.trackBonEvent('contact_click', { source: 'freelancer_profile_page_invite', profile_id: '{{ $user->id }}', profile_type: 'freelancer' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-violet-200 bg-violet-50 px-5 text-sm font-black text-violet-700">
                            Покани към проект
                        </a>
                        @if($bookingEnabled)
                            <a href="#booking" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 px-5 text-sm font-black text-blue-700">
                                Запази час
                            </a>
                        @endif
                    </div>
                </aside>
            </section>

            @if($bookingEnabled)
                <section id="booking" class="mt-6 rounded-[1.5rem] border border-blue-100 bg-blue-50/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:mt-8 sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Онлайн записване: налично</p>
                    <h2 class="mt-3 text-2xl font-black sm:text-3xl">Запази час или консултация</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-blue-900/70">
                        Този фрийлансър е активирал възможност за записване. Докато пълният календар се подготвя, използвайте директен контакт, за да уточните свободен час и формат на услугата.
                    </p>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                        @if($user->phone)
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $user->phone) }}" onclick="window.trackBonEvent('phone_click', { source: 'freelancer_booking_panel', profile_id: '{{ $user->id }}', profile_type: 'freelancer' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-lg shadow-violet-500/20">
                                Обади се за час
                            </a>
                        @endif
                        @if($user->email)
                            <a href="mailto:{{ $user->email }}?subject={{ rawurlencode('Записване през BON') }}" onclick="window.trackBonEvent('contact_click', { source: 'freelancer_booking_panel', profile_id: '{{ $user->id }}', profile_type: 'freelancer' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-blue-200 bg-white/80 px-6 text-sm font-black text-blue-700">
                                Изпрати запитване
                            </a>
                        @endif
                    </div>
                </section>
            @endif

            <section class="mt-6 rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:mt-8 sm:rounded-[2rem] sm:p-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Проекти / Портфолио</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Завършени работи и резултати</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">Портфолиото показва реални примери: сайтове, дизайни, кампании, снимки, видеа, резултати и линкове към работа.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $portfolioItems->count() }} проекта</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($portfolioItems as $item)
                        <article class="overflow-hidden rounded-3xl border border-slate-100 bg-white/85 shadow-lg shadow-blue-900/5">
                            @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" loading="lazy" class="h-44 w-full object-cover">
                            @else
                                <div class="grid h-44 place-items-center bg-gradient-to-br from-blue-50 via-violet-50 to-pink-50">
                                    <span class="flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white">B</span>
                                </div>
                            @endif
                            <div class="p-4 sm:p-5">
                                <h3 class="text-lg font-black">{{ $item->title }}</h3>
                                @if($item->description)
                                    <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $item->description }}</p>
                                @endif
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="rounded-2xl bg-slate-100 px-4 py-2 text-xs font-black text-slate-600">{{ $user->business_category ? \App\Support\CategoryCatalog::displayName($user->business_category) : 'Портфолио' }}</span>
                                    <span class="rounded-2xl bg-slate-100 px-4 py-2 text-xs font-black text-slate-600">{{ $item->created_at?->format('d.m.Y') }}</span>
                                    @if($item->project_url)
                                        <a href="{{ $item->project_url }}" target="_blank" rel="noopener" class="rounded-2xl bg-blue-50 px-4 py-2 text-xs font-black text-blue-700">Линк</a>
                                    @endif
                                    @if($item->pdf_path)
                                        <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" rel="noopener" class="rounded-2xl bg-violet-50 px-4 py-2 text-xs font-black text-violet-700">PDF</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-200 bg-white/70 p-8 text-center text-sm text-slate-500 md:col-span-2 lg:col-span-3">
                            Този фрийлансър все още не е добавил публично портфолио.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="mt-6 grid gap-5 sm:mt-8 sm:gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-violet-600">Репутация</p>
                    <h2 class="mt-3 text-2xl font-black sm:text-3xl">Защо клиентите избират този специалист</h2>
                    <div class="mt-6 grid gap-3">
                        @foreach($trustSummary['reasons'] as $reason)
                            <div class="rounded-3xl border border-slate-100 bg-white/80 p-4 text-sm leading-6 text-slate-600">
                                {{ $reason }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-white/70 bg-white/80 p-5 shadow-xl shadow-blue-900/5 backdrop-blur-2xl sm:rounded-[2rem] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-600">Активност</p>
                    <h2 class="mt-3 text-2xl font-black sm:text-3xl">Публична история</h2>
                    <div class="mt-6 grid gap-3">
                        @forelse($applications as $application)
                            <div class="rounded-3xl border border-slate-100 bg-white/80 p-4">
                                <p class="font-black">{{ $application->job?->title ?: 'Обява' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $application->created_at?->format('d.m.Y') }} · статус {{ $application->status }}</p>
                            </div>
                        @empty
                            <p class="rounded-3xl border border-slate-100 bg-white/80 p-5 text-sm text-slate-500">Този профил все още няма публична история от завършени или потвърдени проекти.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('partials.public-footer')
</body>
</html>

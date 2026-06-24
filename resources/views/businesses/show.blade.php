<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->business_name ?: $user->name }} | BON</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags(($user->short_description ?: $user->description ?: 'Профил на бизнес във BON с услуги, контакти, отзиви и директно запитване.')), 155) }}">
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="fn-premium-page bon-dark-page min-h-screen overflow-x-hidden pb-44 text-white md:pb-28 lg:pb-0">
    @php
        $businessName = $user->business_name ?: $user->name;
        $businessCategories = collect($user->serviceCategories())
            ->map(fn ($profileCategory) => \App\Support\CategoryCatalog::displayName($profileCategory))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $category = !empty($businessCategories)
            ? implode(', ', $businessCategories)
            : ($user->business_category ? \App\Support\CategoryCatalog::displayName($user->business_category) : 'Локален бизнес');
        $serviceCities = $user->serviceCities();
        $cityLabel = !empty($serviceCities) ? implode(', ', $serviceCities) : ($user->city ?: 'България');
        $shortDescription = data_get($user, 'short_description') ?: data_get($user, 'description')
            ?: 'Професионален профил на бизнес във BON с бърз контакт, ясна информация и публично представяне за клиенти, които търсят надеждна услуга.';
        $longDescription = data_get($user, 'description') ?: $shortDescription;
        $servedAreas = data_get($user, 'service_areas') ?: data_get($user, 'обслужвани_райони');
        $yearsExperience = data_get($user, 'years_experience') ?: data_get($user, 'години_опит');
        $emergencyServices = (bool) (data_get($user, 'emergency_services') ?: data_get($user, 'спешни_услуги'));
        $paymentMethods = data_get($user, 'payment_methods') ?: data_get($user, 'методи_на_плащане');
        $facebook = data_get($user, 'facebook') ?: data_get($user, 'фейсбук');
        $instagram = data_get($user, 'instagram') ?: data_get($user, 'инстаграм');
        $whatsapp = data_get($user, 'whatsapp') ?: data_get($user, 'whatsapp_number');
        $viber = data_get($user, 'viber') ?: data_get($user, 'viber_number');
        $normalizeUrl = function (?string $value) {
            if (!$value) {
                return null;
            }

            $value = trim($value);

            return str_starts_with($value, 'http://') || str_starts_with($value, 'https://')
                ? $value
                : 'https://' . $value;
        };
        $facebookUrl = $normalizeUrl($facebook);
        $instagramUrl = $normalizeUrl($instagram);
        $phoneForApps = $user->phone ? preg_replace('/[^\d+]/', '', $user->phone) : null;
        $whatsappUrl = $whatsapp ? 'https://wa.me/' . preg_replace('/[^\d]/', '', $whatsapp) : null;
        $viberUrl = $viber ?: null;
        $businessPhotos = $user->relationLoaded('businessPhotos') ? $user->businessPhotos : collect();
        $galleryImages = $businessPhotos
            ->pluck('path')
            ->filter()
            ->unique()
            ->values();
        $mainImage = $galleryImages->first();
        $similarBusinesses = $similarBusinesses ?? collect();
        $approvedReviews = $approvedReviews ?? collect();
        $reviewsCount = (int) ($reviewsCount ?? $approvedReviews->count());
        $recommendationsCount = (int) ($recommendationsCount ?? $user->recommendationsCount());
        $publicBadges = $user->publicBadges();
        $trustSummary = $trustSummary ?? $user->trustSummary();
        $trustBadges = collect($trustSummary['badges'] ?? []);
        $trustReasons = $trustSummary['reasons'] ?? [];
        $businessJobs = $businessJobs ?? collect();
        $bookingEnabled = (bool) data_get($user, 'booking_enabled', false);
        $averageRating = $averageRating ? round((float) $averageRating, 1) : null;
        $ratingStars = function ($rating) {
            $filled = max(0, min(5, (int) round((float) $rating)));

            return str_repeat('★', $filled) . str_repeat('☆', 5 - $filled);
        };
        $statusLabel = match ($user->effectiveSubscriptionStatus()) {
            'active' => 'Активен',
            'trial' => 'Trial',
            'expired' => 'Скрит',
            'cancelled' => 'Отменен',
            default => 'Неактивен',
        };
    @endphp

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(47,140,255,0.24),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(217,70,239,0.20),transparent_30%),linear-gradient(180deg,#020617,#061426,#020617)]"></div>


    @include('partials.public-header')

<main class="bon-public-profile mx-auto max-w-7xl px-3 py-4 sm:px-6 sm:py-6 lg:px-8">
        <section class="bon-profile-hero fn-glass-card overflow-hidden rounded-[1.5rem] sm:rounded-[34px]">
            <div class="grid gap-0 lg:grid-cols-[1.08fr_0.92fr]">
                <div class="relative min-h-0 p-5 sm:min-h-[430px] sm:p-8 lg:p-10">
                    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_12%_15%,rgba(47,140,255,0.22),transparent_28%),radial-gradient(circle_at_80%_12%,rgba(217,70,239,0.16),transparent_28%)]"></div>

                    <div data-testid="main-business-badges" class="flex flex-wrap items-center gap-2">
                        @if($user->isPremium())
                            <span class="rounded-full bg-orange-400/15 px-3 py-1 text-xs font-black text-orange-100 ring-1 ring-orange-300/20">Premium</span>
                        @endif
                        @if($user->is_verified)
                            <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-100 ring-1 ring-emerald-300/20">Потвърден</span>
                        @endif
                        @if($user->isTrialActive())
                            <span class="rounded-full bg-orange-400/15 px-3 py-1 text-xs font-black text-orange-100 ring-1 ring-orange-300/20">Trial</span>
                        @endif
                        @if($bookingEnabled)
                            <span class="rounded-full bg-blue-400/15 px-3 py-1 text-xs font-black text-blue-100 ring-1 ring-blue-300/20">Онлайн записване</span>
                        @endif
                        @foreach($publicBadges as $badge)
                            @unless(in_array($badge, ['Препоръчан', 'Потвърден'], true))
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/80 ring-1 ring-white/10">{{ $badge }}</span>
                            @endunless
                        @endforeach
                        @foreach($trustBadges->reject(fn ($badge) => in_array($badge, ['Premium', 'Верифициран'], true))->take(3) as $badge)
                            <span class="rounded-full bg-blue-400/15 px-3 py-1 text-xs font-black text-blue-100 ring-1 ring-blue-300/20">{{ $badge }}</span>
                        @endforeach
                    </div>

                    <div class="mt-6 flex flex-col gap-5 sm:mt-8 sm:flex-row sm:items-end sm:gap-6">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-[22px] border border-white/15 bg-gradient-to-br from-orange-500 via-amber-400 to-orange-600 text-3xl font-black shadow-2xl shadow-orange-950/30 sm:h-28 sm:w-28 sm:rounded-[28px] sm:text-5xl">
                            {{ strtoupper(mb_substr($businessName, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">{{ $category }}</p>
                            <h1 class="mt-3 max-w-3xl text-[32px] font-black leading-tight sm:text-6xl">{{ $businessName }}</h1>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/70 sm:mt-4 sm:text-base sm:leading-8">{{ $shortDescription }}</p>
                        </div>
                    </div>

                    <div class="bon-metric-strip mt-6 grid gap-3 sm:mt-8 sm:grid-cols-2 lg:grid-cols-6">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Локация</p>
                            <p class="mt-2 font-black">{{ $cityLabel }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Услуги</p>
                            <p class="mt-2 font-black">{{ $user->services->count() ?: 'Профил' }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Статус</p>
                            <p class="mt-2 font-black">{{ $statusLabel }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Оценка</p>
                            @if($reviewsCount > 0)
                                <p class="mt-2 font-black">{{ number_format($averageRating, 1, '.', '') }}/5</p>
                                <p class="mt-1 text-xs text-amber-200">{{ $ratingStars($averageRating) }} · {{ $reviewsCount }} {{ $reviewsCount === 1 ? 'отзив' : 'отзива' }}</p>
                            @else
                                <p class="mt-2 font-black">Няма оценки</p>
                            @endif
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-white/40">Препоръки</p>
                            <p class="mt-2 font-black">{{ $recommendationsCount }}</p>
                            <p class="mt-1 text-xs text-amber-200">от клиенти във BON</p>
                        </div>
                        <div class="rounded-2xl border border-blue-300/20 bg-blue-400/10 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-100/70">Trust Score</p>
                            <p class="mt-2 font-black text-blue-100">{{ $trustSummary['trust_score'] }}/100</p>
                            <p class="mt-1 text-xs text-blue-100/70">{{ $trustSummary['response_label'] ? 'Отговаря за ' . $trustSummary['response_label'] : 'Репутация' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 md:mt-6 md:grid-cols-3" data-testid="business-profile-trust-strip">
                        <div class="rounded-2xl border {{ $user->is_verified ? 'border-emerald-300/25 bg-emerald-400/10' : 'border-white/10 bg-slate-950/45' }} p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-sm font-black {{ $user->is_verified ? 'text-emerald-100' : 'text-white/70' }}">{{ $user->is_verified ? 'Потвърден бизнес' : 'Очаква потвърждение' }}</p>
                            <p class="mt-2 text-xs leading-5 text-white/55">{{ $user->is_verified ? 'Админ е проверил основните данни на профила.' : 'Профилът е видим, но все още няма verified badge.' }}</p>
                        </div>
                        <div class="rounded-2xl border {{ $user->isPremium() ? 'border-orange-300/25 bg-orange-400/10' : 'border-white/10 bg-slate-950/45' }} p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-sm font-black {{ $user->isPremium() ? 'text-orange-100' : 'text-white/70' }}">{{ $user->isPremium() ? 'Premium профил' : 'Standard профил' }}</p>
                            <p class="mt-2 text-xs leading-5 text-white/55">{{ $user->isPremium() ? 'Получава по-силно позициониране и Premium badge.' : 'Публичен профил с нормално показване.' }}</p>
                        </div>
                        <div class="rounded-2xl border border-orange-300/20 bg-orange-400/10 p-3.5 sm:rounded-3xl sm:p-4">
                            <p class="text-sm font-black text-orange-100">Директен контакт</p>
                            <p class="mt-2 text-xs leading-5 text-white/55">Телефон, запитване и social/website кликове се проследяват като реална активност.</p>
                        </div>
                    </div>

                    @if(session('recommendation_success'))
                        <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm font-bold text-emerald-100">
                            {{ session('recommendation_success') }}
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col gap-3 sm:mt-8 sm:flex-row">
                        <a href="{{ route('businesses.track.inquiry', $user) }}" data-track="cta_send_inquiry" onclick="window.trackBonEvent('contact_click', { source: 'profile_page', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-3.5 font-black text-white shadow-lg shadow-orange-600/25 sm:py-4">Изпрати запитване</a>
                        @if($user->phone)
                            <a href="{{ route('businesses.track.phone', $user) }}" onclick="window.trackBonEvent('phone_click', { source: 'profile_page', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3.5 font-black text-white hover:bg-white/10 sm:py-4">Обади се</a>
                        @endif
                        @if($bookingEnabled)
                            <a href="#booking" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-blue-300/20 bg-blue-400/10 px-6 py-3.5 font-black text-blue-100 hover:bg-blue-400/15 sm:py-4">Запази час</a>
                        @endif
                        @if($user->isPubliclyVisible())
                            <form action="{{ route('businesses.recommendations.store', $user) }}" method="POST" class="sm:min-w-40">
                                @csrf
                                <button type="submit" class="w-full rounded-2xl border border-orange-300/20 bg-orange-400/10 px-6 py-3.5 font-black text-orange-100 hover:bg-orange-400/15 sm:py-4">
                                    Препоръчай
                                </button>
                            </form>
                        @endif
                        @include('partials.favorite-button', ['profile' => $user, 'variant' => 'dark'])
                        <button type="button" onclick="if (navigator.share) { navigator.share({ title: '{{ addslashes($businessName) }}', url: window.location.href }); } else if (navigator.clipboard) { navigator.clipboard.writeText(window.location.href); }" class="min-h-12 rounded-2xl border border-white/10 bg-white/5 px-6 py-3.5 font-black text-white hover:bg-white/10 sm:py-4">Сподели</button>
                    </div>
                </div>

                <div class="grid gap-3 bg-slate-950/35 p-3 sm:grid-cols-2 sm:p-4 lg:p-5">
                    <div class="relative min-h-[220px] overflow-hidden rounded-[22px] border border-white/10 sm:col-span-2 sm:min-h-[280px] sm:rounded-[28px]">
                        @if($mainImage)
                                <img src="{{ asset('storage/' . $mainImage) }}" alt="{{ $businessName }}" class="h-full min-h-[220px] w-full object-cover sm:min-h-[280px]">
                        @else
                            <div class="flex h-full min-h-[220px] items-center justify-center bg-gradient-to-br from-blue-500/20 via-violet-400/10 to-fuchsia-600/25 sm:min-h-[280px]">
                                <div class="px-6 text-center">
                                    <p class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-950/60 text-2xl font-black text-blue-100">B</p>
                                    <p class="mt-4 font-black">Снимките ще се появят тук</p>
                                    <p class="mt-2 text-sm text-white/55">Бизнесът все още не е качил галерия.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @foreach([0, 1] as $galleryIndex)
                        @php
                            $galleryImage = $galleryImages->get($galleryIndex + 1);
                        @endphp
                        <div class="aspect-[4/3] overflow-hidden rounded-3xl border border-white/10">
                            @if($galleryImage)
                                <img src="{{ asset('storage/' . $galleryImage) }}" alt="{{ $businessName }} снимка" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-gradient-to-br from-blue-500/20 via-violet-400/10 to-fuchsia-600/20 text-sm font-bold text-white/50">Галерия</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
            <div class="grid gap-6">
                @if($bookingEnabled)
                    <section id="booking" class="rounded-[1.5rem] border border-blue-300/20 bg-gradient-to-br from-blue-500/10 via-violet-500/10 to-fuchsia-500/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                        <p class="text-sm font-black uppercase tracking-[0.24em] text-blue-100/80">Онлайн записване: налично</p>
                        <h2 class="mt-3 text-2xl font-black sm:text-3xl">Запази час през BON</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-white/65">
                            Този профил е активирал онлайн записване. Докато календарният модул се развива, използвайте директен контакт или запитване, за да потвърдите свободен час.
                        </p>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('businesses.track.inquiry', $user) }}" onclick="window.trackBonEvent('contact_click', { source: 'booking_panel', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-lg shadow-violet-500/20">
                                Изпрати запитване за час
                            </a>
                            @if($user->phone)
                                <a href="{{ route('businesses.track.phone', $user) }}" onclick="window.trackBonEvent('phone_click', { source: 'booking_panel', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 text-sm font-black text-white hover:bg-white/10">
                                    Обади се
                                </a>
                            @endif
                        </div>
                    </section>
                @endif

                @if($user->isPubliclyVisible())
                <section id="send-request" class="rounded-[1.5rem] border border-orange-300/20 bg-gradient-to-br from-orange-500/10 via-amber-400/10 to-orange-600/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Заявка към бизнеса</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Изпрати заявка</h2>
                            <p class="mt-3 text-sm leading-6 text-white/65">
                                Опишете накратко от каква услуга имате нужда. Заявката отива директно към {{ $businessName }} и бизнесът ще може да я управлява от своя панел.
                            </p>

                            @if(session('service_request_success'))
                                <div class="mt-5 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-sm font-bold leading-6 text-emerald-100">
                                    {{ session('service_request_success') }}
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('businesses.service-requests.store', $user) }}" method="POST" class="grid gap-4 rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            @csrf

                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="block">
                                    <span class="mb-2 block text-sm font-semibold text-white/75">Име</span>
                                    <input name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('customer_name') ? 'border-rose-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Вашето име">
                                    @error('customer_name')<span class="mt-2 block text-sm text-rose-200">{{ $message }}</span>@enderror
                                </label>

                                <label class="block">
                                    <span class="mb-2 block text-sm font-semibold text-white/75">Телефон</span>
                                    <input name="customer_phone" value="{{ old('customer_phone') }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('customer_phone') ? 'border-rose-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="+359 ...">
                                    @error('customer_phone')<span class="mt-2 block text-sm text-rose-200">{{ $message }}</span>@enderror
                                </label>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="block">
                                    <span class="mb-2 block text-sm font-semibold text-white/75">Имейл <span class="text-white/40">(по желание)</span></span>
                                    <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('customer_email') ? 'border-rose-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="email@example.com">
                                    @error('customer_email')<span class="mt-2 block text-sm text-rose-200">{{ $message }}</span>@enderror
                                </label>

                                <label class="block">
                                    <span class="mb-2 block text-sm font-semibold text-white/75">Град</span>
                                    <input name="city" value="{{ old('city', $user->city) }}" class="min-h-12 w-full rounded-2xl border {{ $errors->has('city') ? 'border-rose-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Плевен, София...">
                                    @error('city')<span class="mt-2 block text-sm text-rose-200">{{ $message }}</span>@enderror
                                </label>
                            </div>

                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold text-white/75">Описание на проблема</span>
                                <textarea name="message" rows="5" class="w-full rounded-2xl border {{ $errors->has('message') ? 'border-rose-300/60' : 'border-white/10' }} bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/35 focus:border-orange-300/50" placeholder="Опишете каква услуга търсите, кога ви е удобно и как бизнесът да ви помогне.">{{ old('message') }}</textarea>
                                @error('message')<span class="mt-2 block text-sm text-rose-200">{{ $message }}</span>@enderror
                            </label>

                            <button type="submit" data-track="cta_send_inquiry" class="min-h-12 rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25">
                                Изпрати заявка
                            </button>
                        </form>
                    </div>
                </section>
                @endif

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Описание</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">За {{ $businessName }}</h2>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_filter([$user->is_verified ? 'Потвърден профил' : null, $user->isPremium() ? 'Препоръчан бизнес' : null, $emergencyServices ? 'Спешни услуги' : null]) as $chip)
                                <span class="rounded-full border border-white/10 bg-slate-950/45 px-3 py-1 text-xs font-black text-white/75">{{ $chip }}</span>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-5 max-w-4xl text-base leading-8 text-white/70">{{ $longDescription }}</p>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm text-white/45">Категория</p>
                            <p class="mt-2 font-black">{{ $category }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm text-white/45">Обслужвани градове</p>
                            <p class="mt-2 font-black">{{ $cityLabel }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm text-white/45">Опит</p>
                            <p class="mt-2 font-black">{{ $yearsExperience ?: 'Не е посочен' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Галерия</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Актуални снимки</h2>
                        </div>
                        <p class="text-sm font-bold text-white/50">{{ $businessPhotos->count() }} / {{ $user->photoLimit() }} снимки</p>
                    </div>

                    @if($businessPhotos->isNotEmpty())
                        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($businessPhotos as $photo)
                                <a href="{{ asset('storage/' . $photo->path) }}" class="group block overflow-hidden rounded-3xl border border-white/10 bg-slate-950/45" target="_blank" rel="noopener">
                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->alt_text ?: $businessName }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div data-empty-state class="mt-6 rounded-3xl border border-dashed border-white/15 bg-slate-950/45 p-6 text-center">
                            <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-xl font-black">B</p>
                            <p class="mt-4 text-lg font-black">Все още няма качени снимки</p>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-white/55">Когато бизнесът добави снимки в своята галерия, те ще се показват тук.</p>
                        </div>
                    @endif
                </section>

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Услуги</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Какво предлага бизнесът</h2>
                        </div>
                        <a href="{{ route('businesses.track.inquiry', $user) }}" data-track="cta_send_inquiry" onclick="window.trackBonEvent('contact_click', { source: 'profile_page_services', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="hidden rounded-2xl border border-orange-300/20 bg-orange-300/10 px-5 py-3 text-sm font-black text-orange-100 hover:bg-orange-400/15 sm:inline-flex">Заяви оферта</a>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @forelse($user->services as $service)
                            <a href="{{ route('services.show', $service) }}" class="rounded-3xl border border-white/10 bg-slate-950/45 p-5 transition hover:border-orange-300/30 hover:bg-white/10">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-black">{{ $service->title }}</p>
                                        <p class="mt-2 text-sm text-white/55">{{ \App\Support\CategoryCatalog::displayName($service->category) }}</p>
                                    </div>
                                    @if($service->price)
                                        <span class="rounded-2xl bg-orange-300/10 px-3 py-2 text-sm font-black text-orange-100">от {{ number_format($service->price, 2, ',', ' ') }} €</span>
                                    @endif
                                </div>
                                <p class="mt-4 line-clamp-2 text-sm leading-6 text-white/60">{{ $service->description }}</p>
                            </a>
                        @empty
                            <div data-empty-state class="md:col-span-2 rounded-3xl border border-white/10 bg-slate-950/45 p-6 text-center" data-testid="business-profile-services-empty">
                                <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-xl font-black">B</p>
                                <p class="mt-4 text-lg font-black">Все още няма добавени услуги с цени</p>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-white/55">Можете да изпратите запитване, за да получите индивидуална оферта от този бизнес.</p>
                                <a href="{{ route('businesses.track.inquiry', $user) }}" data-track="cta_send_inquiry" onclick="window.trackBonEvent('contact_click', { source: 'profile_page_services_empty', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-5 py-3 text-sm font-black text-white">Изпрати запитване</a>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-blue-200/80">Обяви</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Публикувани обяви и отворени позиции</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-6 text-white/60">
                                Тук бизнесът може да показва активни обяви към фрийлансъри и специалисти, без профилът да изглежда като freelancer портфолио.
                            </p>
                        </div>
                        @auth
                            @if(auth()->id() === $user->id || auth()->user()?->role === 'admin')
                                <a href="{{ route('business.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-blue-300/20 bg-blue-400/10 px-5 py-3 text-sm font-black text-blue-100 hover:bg-blue-400/15">
                                    Управлявай обявите
                                </a>
                            @endif
                        @endauth
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @forelse($businessJobs as $job)
                            <article class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-100/70">{{ $job->category ? \App\Support\CategoryCatalog::displayName($job->category) : 'Обява' }}</p>
                                        <h3 class="mt-2 text-lg font-black">{{ $job->title }}</h3>
                                        <p class="mt-2 text-sm text-white/50">
                                            {{ $job->status === 'open' ? 'Отворена позиция' : 'Затворена позиция' }}
                                            @if($job->deadline) · срок {{ $job->deadline->format('d.m.Y') }} @endif
                                            @if($job->location) · {{ $job->location }} @endif
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $job->applications_count ?? 0 }} кандидатури</span>
                                </div>
                                <p class="mt-4 line-clamp-3 text-sm leading-6 text-white/60">{{ $job->description }}</p>
                            </article>
                        @empty
                            <div data-empty-state class="md:col-span-2 rounded-3xl border border-dashed border-white/15 bg-slate-950/45 p-6 text-center">
                                <p class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 text-xl font-black">B</p>
                                <p class="mt-4 text-lg font-black">Няма публикувани обяви</p>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-white/55">Когато бизнесът публикува обява или работна позиция, тя ще се показва тук.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Доверие</p>
                    <h2 class="mt-3 text-2xl font-black sm:text-3xl">Защо клиентите избират този бизнес</h2>
                    <div class="mt-6 grid gap-3 md:grid-cols-3">
                        <div class="rounded-3xl border border-blue-300/20 bg-blue-400/10 p-5">
                            <p class="text-sm font-black uppercase tracking-[0.18em] text-blue-100/70">Trust Score</p>
                            <p class="mt-2 text-3xl font-black text-blue-100 sm:text-4xl">{{ $trustSummary['trust_score'] }}/100</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm font-black uppercase tracking-[0.18em] text-white/40">Изпълнени заявки</p>
                            <p class="mt-2 text-3xl font-black sm:text-4xl">{{ $trustSummary['completed_projects_count'] }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-sm font-black uppercase tracking-[0.18em] text-white/40">Успешно приключени</p>
                            <p class="mt-2 text-3xl font-black sm:text-4xl">{{ $trustSummary['success_rate'] }}%</p>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-3">
                        @foreach($trustReasons as $reason)
                            <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4 text-sm leading-6 text-white/70">
                                {{ $reason }}
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @if($user->is_verified)
                            <div class="rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-5">
                                <p class="text-lg font-black text-emerald-100">Потвърден бизнес</p>
                                <p class="mt-3 text-sm leading-6 text-white/70">BON показва този badge само за профили, маркирани като проверени от администратор.</p>
                            </div>
                        @endif
                        @if($user->isPremium())
                            <div class="rounded-3xl border border-orange-300/20 bg-orange-400/10 p-5">
                                <p class="text-lg font-black text-orange-100">Препоръчан бизнес</p>
                                <p class="mt-3 text-sm leading-6 text-white/70">Premium профилите получават по-високо показване и по-силно визуално представяне в публичните резултати.</p>
                            </div>
                        @endif
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-lg font-black">Ясен публичен профил</p>
                            <p class="mt-3 text-sm leading-6 text-white/60">Клиентите виждат град, категория, услуги, работно време и директни действия за контакт.</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                            <p class="text-lg font-black">Бърз контакт</p>
                            <p class="mt-3 text-sm leading-6 text-white/60">Бутоните за оферта, телефон и запитване помагат на клиентите да се свържат с бизнеса по най-бързия начин.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Мнения</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Рейтинг и отзиви</h2>
                        </div>
                        <div class="rounded-3xl border border-amber-300/20 bg-amber-300/10 px-5 py-4 text-right">
                            @if($reviewsCount > 0)
                                <p class="text-2xl font-black sm:text-3xl">{{ number_format($averageRating, 1, '.', '') }}/5</p>
                                <p class="mt-1 text-sm font-bold text-amber-100">{{ $ratingStars($averageRating) }} · {{ $reviewsCount }} {{ $reviewsCount === 1 ? 'одобрен отзив' : 'одобрени отзива' }}</p>
                            @else
                                <p class="text-2xl font-black">Няма оценки</p>
                                <p class="mt-1 text-sm text-white/55">Бъдете първият клиент с мнение.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @forelse($approvedReviews as $review)
                            <article class="rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-black">{{ $review->reviewer_name }}</p>
                                        <p class="mt-1 text-sm text-amber-200">{{ $ratingStars($review->rating) }} <span class="text-white/45">· {{ $review->rating }}/5</span></p>
                                    </div>
                                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-black text-emerald-100">Одобрен</span>
                                </div>
                                <p class="mt-4 text-sm leading-6 text-white/70">{{ $review->comment ?: 'Оценка без допълнителен коментар.' }}</p>
                                <p class="mt-4 text-xs text-white/40">{{ $review->approved_at?->format('d.m.Y') ?: $review->created_at?->format('d.m.Y') }}</p>
                            </article>
                        @empty
                            <div data-empty-state class="md:col-span-2 rounded-3xl border border-white/10 bg-slate-950/45 p-6 text-center">
                                <p class="font-black">Все още няма одобрени отзиви</p>
                                <p class="mt-2 text-sm text-white/55">Новите отзиви се показват публично след одобрение от администратор.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 rounded-3xl border border-white/10 bg-slate-950/45 p-5">
                        <h3 class="text-xl font-black">Остави отзив</h3>

                        @if(session('review_success'))
                            <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">{{ session('review_success') }}</div>
                        @endif

                        @if($user->isPubliclyVisible())
                            <form action="{{ route('businesses.reviews.store', $user) }}" method="POST" class="mt-5 grid gap-4">
                                @csrf
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-white/75">Име</label>
                                        <input type="text" name="reviewer_name" value="{{ old('reviewer_name', auth()->user()?->name) }}" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50" placeholder="Вашето име">
                                        @error('reviewer_name')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-white/75">Имейл, по желание</label>
                                        <input type="email" name="reviewer_email" value="{{ old('reviewer_email', auth()->user()?->email) }}" class="min-h-12 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50" placeholder="email@example.com">
                                        @error('reviewer_email')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-white/75">Оценка</label>
                                    <div class="grid grid-cols-5 gap-2">
                                        @for($rating = 1; $rating <= 5; $rating++)
                                            <label class="flex min-h-12 cursor-pointer items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-center text-sm font-black text-amber-100 hover:bg-white/10 has-[:checked]:border-amber-300/50 has-[:checked]:bg-amber-400/15">
                                                <input type="radio" name="rating" value="{{ $rating }}" {{ (string) old('rating') === (string) $rating ? 'checked' : '' }} class="sr-only">
                                                {{ $rating }} ★
                                            </label>
                                        @endfor
                                    </div>
                                    @error('rating')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-white/75">Коментар <span class="text-white/40">(по желание)</span></label>
                                    <textarea name="comment" rows="4" maxlength="1500" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50" placeholder="Разкажете накратко за опита си с този бизнес.">{{ old('comment') }}</textarea>
                                    @error('comment')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                                </div>

                                <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25">Изпрати отзив за одобрение</button>
                            </form>
                        @else
                            <div class="mt-4 rounded-2xl border border-rose-300/20 bg-rose-400/10 p-4 text-sm leading-6 text-rose-100">
                                Този профил не е публично активен и в момента не приема нови отзиви.
                            </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-8">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-orange-200/80">Подобни</p>
                            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Подобни бизнеси</h2>
                        </div>
                        <a href="{{ route('businesses.index') }}" class="hidden text-sm font-black text-orange-300 hover:text-white sm:block">Виж всички</a>
                    </div>

                    <div data-testid="similar-businesses" class="mt-6 grid gap-4 md:grid-cols-3">
                        @forelse($similarBusinesses as $similarBusiness)
                            @include('partials.business-card', ['business' => $similarBusiness])
                        @empty
                            <div data-empty-state class="md:col-span-3 rounded-3xl border border-white/10 bg-slate-950/45 p-6 text-center">
                                <p class="font-black">Все още няма подобни активни бизнеси</p>
                                <p class="mt-2 text-sm text-white/55">Когато има други active или trial профили от тази категория или град, те ще се появят тук.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside id="contact" class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 shadow-xl shadow-black/20 backdrop-blur-xl sm:rounded-[32px] sm:p-6">
                    <h2 class="text-2xl font-black">Контакт</h2>
                    <p class="mt-2 text-sm leading-6 text-white/60">Изберете удобен начин за връзка с бизнеса.</p>

                    <div class="mt-6 grid gap-3">
                        <a href="{{ route('businesses.track.inquiry', $user) }}" data-track="cta_send_inquiry" onclick="window.trackBonEvent('contact_click', { source: 'profile_page_sidebar', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-5 py-4 text-center font-black text-white shadow-lg shadow-orange-600/25">Заяви оферта</a>
                        @if($user->phone)
                            <a href="{{ route('businesses.track.phone', $user) }}" onclick="window.trackBonEvent('phone_click', { source: 'profile_page_sidebar', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-center font-black text-white hover:bg-white/10">Обади се</a>
                        @endif
                        <a href="{{ route('businesses.track.inquiry', $user) }}" data-track="cta_send_inquiry" onclick="window.trackBonEvent('contact_click', { source: 'profile_page_sidebar_secondary', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="rounded-2xl border border-orange-300/20 bg-orange-300/10 px-5 py-4 text-center font-black text-orange-100 hover:bg-orange-400/15">Изпрати запитване</a>
                        @if($whatsappUrl)
                            <a href="{{ route('businesses.track.social', [$user, 'whatsapp']) }}" target="_blank" rel="noopener" class="rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-center font-black text-emerald-100">WhatsApp</a>
                        @endif
                        @if($viberUrl)
                            <a href="{{ route('businesses.track.social', [$user, 'viber']) }}" target="_blank" rel="noopener" class="rounded-2xl border border-orange-300/20 bg-orange-300/10 px-5 py-4 text-center font-black text-orange-100">Viber</a>
                        @endif
                    </div>

                    <div class="mt-6 grid gap-3">
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-sm text-white/45">Телефон</p>
                            <p class="mt-1 font-black">{{ $user->phone ?: 'Няма добавен телефон' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-sm text-white/45">Адрес / район</p>
                            <p class="mt-1 font-black">{{ $user->address ?: ($servedAreas ?: 'Няма добавен адрес') }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-sm text-white/45">Работно време</p>
                            <p class="mt-1 font-black">{{ $user->working_hours ?: 'Няма добавено работно време' }}</p>
                        </div>
                        @if($paymentMethods)
                            <div class="rounded-2xl bg-slate-950/45 p-4">
                                <p class="text-sm text-white/45">Плащане</p>
                                <p class="mt-1 font-black">{{ $paymentMethods }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @if($user->website)
                            <a href="{{ route('businesses.track.website', $user) }}" target="_blank" rel="noopener" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Website</a>
                        @endif
                        @if($facebookUrl)
                            <a href="{{ route('businesses.track.social', [$user, 'facebook']) }}" target="_blank" rel="noopener" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Facebook</a>
                        @endif
                        @if($instagramUrl)
                            <a href="{{ route('businesses.track.social', [$user, 'instagram']) }}" target="_blank" rel="noopener" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Instagram</a>
                        @endif
                    </div>
                </div>
            </aside>
        </section>
    </main>

    @include('partials.public-footer')

    <div class="fixed inset-x-0 bottom-[calc(4.65rem+env(safe-area-inset-bottom))] z-50 border-t border-white/10 bg-slate-950/92 p-2.5 backdrop-blur-2xl md:bottom-0 lg:hidden">
        <div class="grid grid-cols-2 gap-2.5">
            @if($user->phone)
                <a href="{{ route('businesses.track.phone', $user) }}" onclick="window.trackBonEvent('phone_click', { source: 'profile_page_mobile_bar', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-center text-sm font-black text-white">Обади се</a>
            @else
                <a href="{{ route('request.service') }}" data-track="cta_request" onclick="window.trackBonEvent('service_request_start', { source: 'profile_page_mobile_bar' })" class="flex min-h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-center text-sm font-black text-white">Заяви оферта</a>
            @endif
            <a href="{{ route('businesses.track.inquiry', $user) }}" data-track="cta_send_inquiry" onclick="window.trackBonEvent('contact_click', { source: 'profile_page_mobile_bar', profile_id: '{{ $user->id }}', profile_type: 'business' })" class="flex min-h-11 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 via-amber-400 to-orange-600 px-4 py-3 text-center text-sm font-black text-white">Запитване</a>
        </div>
    </div>
    @include('partials.mobile-bottom-nav')
</body>
</html>

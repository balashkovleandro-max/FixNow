@php
    $profile = $profile ?? auth()->user();
    $variant = $variant ?? 'light';
    $context = $context ?? 'dashboard';
    $isDark = $variant === 'dark';
    $modalId = 'bon-paid-services-modal-' . $context . '-' . ($profile->id ?? 'guest');
    $profileName = $profile
        ? ($profile->business_name ?: $profile->name)
        : '';
    $profileEmail = $profile?->email ?? '';
    $profilePhone = $profile?->phone ?? '';
    $profileSocial = $profile?->instagram ?: ($profile?->facebook ?: ($profile?->website ?? ''));

    $services = [
        [
            'key' => 'profile_setup',
            'name' => 'BON Profile Setup',
            'price' => '99 € еднократно',
            'price_value' => 99,
            'description' => 'Създаваме и подреждаме вашия BON профил вместо вас, така че бизнесът ви да изглежда по-професионално и да бъде по-лесен за откриване.',
            'modal_message' => 'Заявете BON Profile Setup и екипът на BON ще се свърже с вас за събиране на снимки, услуги и информация за профила.',
            'cta' => 'Заяви Setup',
            'color' => 'from-blue-600 via-violet-600 to-fuchsia-500',
            'features' => [
                'Създаване и подреждане на профила',
                'Качване на снимки/лого',
                'Добавяне на услуги, градове и контакти',
                'Добавяне на работно време',
                'Кратко професионално описание',
                'Подредена галерия',
                'Оптимизация за търсачката в BON',
                '30 дни Premium beta достъп',
                'Значка Early BON Partner',
                'Приоритетно позициониране в beta периода',
                'Готов линк към профила за Instagram/Facebook/Google',
                'Кратък checklist с препоръки',
                'Помощ при първоначална настройка',
            ],
        ],
        [
            'key' => 'growth_review',
            'name' => 'BON Growth Review',
            'price' => '199 € еднократно',
            'price_value' => 199,
            'description' => 'Анализираме вашия профил, оферта и онлайн присъствие и ви даваме конкретен план как да изглеждате по-добре и да получавате повече възможности за запитвания.',
            'modal_message' => 'Заявете BON Growth Review и екипът на BON ще се свърже с вас, за да подготви анализ и план за развитие на профила и онлайн присъствието ви.',
            'cta' => 'Заяви Growth Review',
            'color' => 'from-fuchsia-500 via-violet-600 to-blue-600',
            'features' => [
                'Всичко от BON Profile Setup',
                'Преглед на BON профила',
                'Преглед на Instagram/Facebook/Google присъствие',
                'Анализ на офертата и позиционирането',
                'Препоръки за снимки, описание, услуги и комуникация',
                '30-дневен Growth план',
                '30 или 60 минути консултация',
                'Подобрение на BON профила след анализа',
                'Checklist с конкретни действия',
                'Препоръки за повече възможности за запитвания',
                'Приоритетна поддръжка в beta периода',
                'Ранен достъп до booking, статистика и бизнес анализ',
            ],
        ],
    ];
@endphp

<section id="bon-extra-help" class="rounded-[1.5rem] border {{ $isDark ? 'border-white/10 bg-white/10 text-white shadow-black/20' : 'border-white/70 bg-white/80 text-[#070B1F] shadow-blue-900/10' }} p-5 shadow-2xl backdrop-blur-2xl sm:rounded-[32px] sm:p-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] {{ $isDark ? 'text-fuchsia-100/80' : 'text-fuchsia-600' }}">Допълнителна помощ от BON</p>
            <h2 class="mt-3 text-2xl font-black sm:text-4xl">Професионална настройка и Growth преглед.</h2>
            <p class="mt-3 max-w-4xl text-sm leading-6 {{ $isDark ? 'text-white/60' : 'text-slate-600' }} sm:text-base sm:leading-7">
                Не искате да губите време с настройки? Екипът на BON може да ви помогне да създадете по-професионален профил, да подобрите представянето си и да получите конкретни препоръки за повече клиенти.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach(['Еднократно плащане', 'Подходящо за beta бизнеси', 'Без нужда сами да настройвате всичко', 'Приоритетна помощ от BON'] as $badge)
                <span class="rounded-full {{ $isDark ? 'bg-white/10 text-white/75 ring-white/10' : 'bg-white/80 text-slate-600 ring-slate-200/70' }} px-3 py-1 text-xs font-black ring-1">{{ $badge }}</span>
            @endforeach
        </div>
    </div>

    <div class="mt-7 grid gap-5 lg:grid-cols-2">
        @foreach($services as $service)
            <article class="relative flex h-full flex-col overflow-hidden rounded-[1.5rem] border {{ $isDark ? 'border-white/10 bg-slate-950/35' : 'border-slate-100 bg-white/85' }} p-5 shadow-xl {{ $isDark ? 'shadow-black/10' : 'shadow-blue-900/5' }} sm:rounded-[2rem] sm:p-7">
                <div class="pointer-events-none absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-violet-400/70 to-transparent"></div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.18em] {{ $isDark ? 'text-blue-100/70' : 'text-blue-600' }}">Еднократен пакет</p>
                        <h3 class="mt-2 text-2xl font-black">{{ $service['name'] }}</h3>
                        <p class="mt-3 text-3xl font-black">{{ $service['price'] }}</p>
                    </div>
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $service['color'] }} text-2xl font-black text-white shadow-xl shadow-violet-500/25">B</span>
                </div>

                <p class="mt-5 text-sm leading-7 {{ $isDark ? 'text-white/60' : 'text-slate-600' }}">{{ $service['description'] }}</p>

                <ul class="mt-5 grid gap-2.5 text-sm leading-6 {{ $isDark ? 'text-white/66' : 'text-slate-600' }}">
                    @foreach($service['features'] as $feature)
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-gradient-to-br {{ $service['color'] }}"></span>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                <button
                    type="button"
                    data-bon-service-open="{{ $modalId }}"
                    data-service-key="{{ $service['key'] }}"
                    data-service-name="{{ $service['name'] }}"
                    data-service-price="{{ $service['price_value'] }}"
                    data-service-message="{{ $service['modal_message'] }}"
                    class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-gradient-to-r {{ $service['color'] }} px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25 transition hover:-translate-y-0.5"
                >
                    {{ $service['cta'] }}
                </button>
            </article>
        @endforeach
    </div>

    <div class="mt-6 rounded-[1.25rem] border {{ $isDark ? 'border-white/10 bg-white/5' : 'border-blue-100 bg-blue-50/70' }} p-5 sm:rounded-[1.75rem] sm:p-6">
        <h3 class="text-xl font-black">Кога има смисъл да използвате тези услуги?</h3>
        <p class="mt-3 text-sm leading-7 {{ $isDark ? 'text-white/60' : 'text-slate-600' }}">
            Използвайте BON Profile Setup, ако искате бързо и професионално да създадем профила ви вместо вас.
            Използвайте BON Growth Review, ако искате не само профил, а конкретни препоръки как да подобрите представянето си и да привличате повече възможности за запитвания.
        </p>
    </div>
</section>

<div id="{{ $modalId }}" class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-950/55 p-4 backdrop-blur-md" data-bon-service-modal>
    <div class="absolute inset-0" data-bon-service-close></div>

    <section class="relative max-h-[90dvh] w-full max-w-xl overflow-y-auto rounded-[2rem] border border-white/70 bg-white/95 p-6 text-[#070B1F] shadow-2xl shadow-blue-950/20 backdrop-blur-2xl sm:p-7">
        <button type="button" data-bon-service-close class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 hover:text-blue-700" aria-label="Затвори">×</button>

        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
        <p class="mt-5 text-sm font-black uppercase tracking-[0.2em] text-blue-600">Заявка към BON</p>
        <h3 class="mt-2 pr-10 text-2xl font-black" data-bon-service-title>BON услуга</h3>
        <p class="mt-3 text-sm leading-6 text-slate-600" data-bon-service-message></p>

        <form class="mt-6 grid gap-4" data-bon-service-form>
            <input type="hidden" name="package" data-bon-service-package>

            <label class="grid gap-2 text-sm font-black text-slate-700">
                Име на бизнес / профил
                <input name="profile_name" value="{{ $profileName }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white px-4 font-semibold outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Телефон
                    <input name="phone" value="{{ $profilePhone }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white px-4 font-semibold outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Имейл
                    <input name="email" type="email" value="{{ $profileEmail }}" class="min-h-12 rounded-2xl border border-slate-200 bg-white px-4 font-semibold outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
                </label>
            </div>

            <label class="grid gap-2 text-sm font-black text-slate-700" data-bon-service-social>
                Instagram / Facebook / Google / сайт
                <input name="social_link" value="{{ $profileSocial }}" placeholder="https://..." class="min-h-12 rounded-2xl border border-slate-200 bg-white px-4 font-semibold outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100">
            </label>

            <label class="grid gap-2 text-sm font-black text-slate-700">
                Кратко съобщение
                <textarea name="message" rows="4" placeholder="Напишете какво искате да подобрим или каква информация вече имате готова." class="rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold leading-6 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100"></textarea>
            </label>

            <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-6 text-sm font-black text-white shadow-xl shadow-violet-500/25">
                Изпрати заявка
            </button>
        </form>
    </section>
</div>

<script>
    (() => {
        const modalId = @json($modalId);
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const title = modal.querySelector('[data-bon-service-title]');
        const message = modal.querySelector('[data-bon-service-message]');
        const form = modal.querySelector('[data-bon-service-form]');
        const packageInput = modal.querySelector('[data-bon-service-package]');
        const socialField = modal.querySelector('[data-bon-service-social]');
        const openers = document.querySelectorAll('[data-bon-service-open="' + modalId + '"]');
        const closers = modal.querySelectorAll('[data-bon-service-close]');

        let activePackage = 'profile_setup';
        let activePackageName = 'BON Profile Setup';
        let activePackagePrice = 99;

        const track = (eventName, params = {}) => {
            if (typeof window.trackBonEvent === 'function') {
                window.trackBonEvent(eventName, params);
            }
        };

        const open = (button) => {
            activePackage = button.dataset.serviceKey || 'profile_setup';
            activePackageName = button.dataset.serviceName || 'BON Profile Setup';
            activePackagePrice = Number(button.dataset.servicePrice || 99);

            if (title) title.textContent = activePackageName;
            if (message) message.textContent = button.dataset.serviceMessage || '';
            if (packageInput) packageInput.value = activePackageName;
            if (socialField) socialField.classList.toggle('hidden', activePackage !== 'growth_review');

            track(activePackage === 'growth_review' ? 'growth_review_interest' : 'profile_setup_interest', {
                package: activePackage,
                price: activePackagePrice
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.documentElement.classList.add('overflow-hidden');
        };

        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.documentElement.classList.remove('overflow-hidden');
        };

        openers.forEach((button) => button.addEventListener('click', () => open(button)));
        closers.forEach((button) => button.addEventListener('click', close));

        form?.addEventListener('submit', (event) => {
            event.preventDefault();
            const data = new FormData(form);
            const body = [
                'Пакет: ' + activePackageName,
                'Цена: ' + activePackagePrice + ' €',
                'Профил: ' + (data.get('profile_name') || ''),
                'Телефон: ' + (data.get('phone') || ''),
                'Имейл: ' + (data.get('email') || ''),
                'Линк: ' + (data.get('social_link') || ''),
                'Съобщение: ' + (data.get('message') || '')
            ].join('\n');

            window.location.href = 'mailto:hello@bon.bg?subject=' + encodeURIComponent(activePackageName + ' заявка') + '&body=' + encodeURIComponent(body);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) close();
        });
    })();
</script>

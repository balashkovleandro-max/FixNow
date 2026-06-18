@php
    $profile = $profile ?? auth()->user();
    $variant = $variant ?? 'light';
    $isDark = $variant === 'dark';
    $isPremium = $profile && method_exists($profile, 'isPremium') ? $profile->isPremium() : false;
    $bookingEnabled = (bool) data_get($profile, 'booking_enabled', false);
    $modalId = 'growth-tools-modal-' . ($profile->id ?? 'guest') . '-' . $variant;

    $statusLabels = [
        'active' => 'Активно',
        'locked' => 'Заключено',
        'coming_soon' => 'Скоро',
        'premium_only' => 'Premium',
        'addon' => 'Add-on',
    ];

    $tools = [
        [
            'title' => 'Онлайн записване на часове',
            'description' => 'Позволете на клиентите да запазват час директно през BON.',
            'status' => $bookingEnabled ? 'active' : 'addon',
            'price' => '4.99 € / месец или включено в Premium',
            'button' => $bookingEnabled ? 'Активно' : 'Отключи',
            'icon' => '⌚',
            'color' => 'from-blue-600 to-cyan-400',
        ],
        [
            'title' => 'Бизнес анализ',
            'description' => 'Получете анализ на профила, представянето и препоръки как да привличате повече клиенти.',
            'status' => $isPremium ? 'active' : 'premium_only',
            'price' => 'Включено в Premium',
            'button' => $isPremium ? 'Отвори' : 'Включено в Premium',
            'icon' => '⌁',
            'color' => 'from-violet-600 to-blue-600',
        ],
        [
            'title' => 'Growth стратегия',
            'description' => 'Кратък план с конкретни стъпки за подобрение на офертата, профила, снимките и комуникацията с клиенти.',
            'status' => $isPremium ? 'active' : 'premium_only',
            'price' => 'Premium или платена допълнителна услуга',
            'button' => $isPremium ? 'Заяви план' : 'Включено в Premium',
            'icon' => '↗',
            'color' => 'from-fuchsia-500 to-pink-500',
        ],
        [
            'title' => 'Консултация по телефона',
            'description' => 'Разговор с екипа на BON за подобрение на профила, позиционирането и намирането на повече клиенти.',
            'status' => 'addon',
            'price' => '9.99 € за 30 минути',
            'button' => 'Заяви консултация',
            'icon' => '☎',
            'color' => 'from-emerald-400 to-cyan-400',
        ],
        [
            'title' => 'Статистика за профила',
            'description' => 'Вижте колко хора са разгледали профила ви, натиснали телефон или изпратили заявка.',
            'status' => $isPremium ? 'active' : 'premium_only',
            'price' => 'Premium',
            'button' => $isPremium ? 'Виж статистика' : 'Включено в Premium',
            'icon' => '▥',
            'color' => 'from-blue-500 to-violet-500',
        ],
        [
            'title' => 'QR код за отзиви',
            'description' => 'Получете QR код, чрез който клиентите могат да оставят отзив за вашия бизнес.',
            'status' => 'addon',
            'price' => 'Premium или Add-on',
            'button' => 'Отключи',
            'icon' => '▦',
            'color' => 'from-purple-500 to-fuchsia-500',
        ],
        [
            'title' => 'Разширена галерия',
            'description' => 'Добавете повече снимки и проекти към профила си.',
            'status' => $isPremium ? 'active' : 'premium_only',
            'price' => 'Premium или Add-on',
            'button' => $isPremium ? 'Управлявай' : 'Включено в Premium',
            'icon' => '▧',
            'color' => 'from-pink-500 to-rose-500',
        ],
        [
            'title' => 'Препоръчано позициониране',
            'description' => 'Показване по-нагоре в резултатите и в секция Препоръчани.',
            'status' => $isPremium ? 'active' : 'premium_only',
            'price' => 'Premium',
            'button' => $isPremium ? 'Активно' : 'Включено в Premium',
            'icon' => '★',
            'color' => 'from-amber-400 to-fuchsia-500',
        ],
    ];
@endphp

<section id="growth-tools" class="rounded-[1.5rem] border {{ $isDark ? 'border-white/10 bg-white/10 text-white shadow-black/20' : 'border-white/70 bg-white/80 text-[#070B1F] shadow-blue-900/10' }} p-5 shadow-2xl backdrop-blur-2xl sm:rounded-[32px] sm:p-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] {{ $isDark ? 'text-blue-100/80' : 'text-blue-600' }}">Инструменти за растеж</p>
            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Отключете повече стойност от BON профила.</h2>
            <p class="mt-3 max-w-3xl text-sm leading-6 {{ $isDark ? 'text-white/60' : 'text-slate-600' }}">
                BON не е просто профил. Получавате видимост, заявки и инструменти, които помагат на бизнеса ви да се развива.
            </p>
            <p class="mt-2 max-w-3xl text-sm leading-6 {{ $isDark ? 'text-white/50' : 'text-slate-500' }}">
                Отключете допълнителни инструменти според нуждите на вашия бизнес.
            </p>
        </div>
        <a href="{{ route('plans') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl {{ $isDark ? 'border border-white/10 bg-white/5 text-white hover:bg-white/10' : 'border border-slate-200 bg-white/80 text-slate-700 hover:text-blue-700' }} px-5 text-sm font-black">
            Виж плановете
        </a>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($tools as $tool)
            <article class="flex h-full flex-col rounded-[1.35rem] border {{ $isDark ? 'border-white/10 bg-slate-950/35' : 'border-slate-100 bg-white/85' }} p-4 shadow-lg {{ $isDark ? 'shadow-black/10' : 'shadow-blue-900/5' }} sm:rounded-[1.75rem] sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $tool['color'] }} text-xl font-black text-white shadow-lg shadow-violet-500/20">{{ $tool['icon'] }}</span>
                    <span class="rounded-full {{ $tool['status'] === 'active' ? 'bg-emerald-500/15 text-emerald-500' : ($tool['status'] === 'coming_soon' ? 'bg-slate-500/15 text-slate-500' : 'bg-violet-500/15 text-violet-500') }} px-3 py-1 text-xs font-black">
                        {{ $statusLabels[$tool['status']] }}
                    </span>
                </div>

                <h3 class="mt-4 text-lg font-black">{{ $tool['title'] }}</h3>
                <p class="mt-2 flex-1 text-sm leading-6 {{ $isDark ? 'text-white/58' : 'text-slate-600' }}">{{ $tool['description'] }}</p>
                <p class="mt-4 text-xs font-black uppercase tracking-[0.14em] {{ $isDark ? 'text-white/40' : 'text-slate-400' }}">{{ $tool['price'] }}</p>

                <button
                    type="button"
                    data-growth-open="{{ $modalId }}"
                    data-growth-title="{{ $tool['title'] }}"
                    data-growth-status="{{ $statusLabels[$tool['status']] }}"
                    data-growth-description="{{ $tool['description'] }}"
                    class="mt-4 inline-flex min-h-11 items-center justify-center rounded-2xl {{ $tool['status'] === 'active' ? 'border border-emerald-200 bg-emerald-50 text-emerald-700' : 'bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 text-white shadow-lg shadow-violet-500/20' }} px-4 text-sm font-black transition hover:-translate-y-0.5"
                >
                    {{ $tool['button'] }}
                </button>
            </article>
        @endforeach
    </div>
</section>

<div id="{{ $modalId }}" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/55 p-4 backdrop-blur-md" data-growth-modal>
    <div class="absolute inset-0" data-growth-close></div>
    <section class="relative max-h-[90dvh] w-full max-w-lg overflow-y-auto rounded-[2rem] border border-white/70 bg-white/95 p-6 text-[#070B1F] shadow-2xl shadow-blue-950/20 backdrop-blur-2xl sm:p-7">
        <button type="button" data-growth-close class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 hover:text-blue-700" aria-label="Затвори">×</button>
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 text-2xl font-black text-white shadow-xl shadow-violet-500/25">B</div>
        <p class="mt-5 text-sm font-black uppercase tracking-[0.2em] text-blue-600" data-growth-modal-status>Add-on</p>
        <h3 class="mt-2 pr-10 text-2xl font-black" data-growth-modal-title>Инструмент</h3>
        <p class="mt-3 text-sm leading-6 text-slate-600" data-growth-modal-description></p>
        <div class="mt-5 rounded-3xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-900/75">
            Тази функция е в процес на активиране. Свържете се с екипа на BON за ранен достъп или я отключете чрез подходящ план/add-on, когато стане налична.
        </div>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="mailto:hello@bon.bg?subject=BON%20Growth%20Tool" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 via-violet-600 to-fuchsia-500 px-5 text-sm font-black text-white shadow-lg shadow-violet-500/20">
                Свържи се с BON
            </a>
            <button type="button" data-growth-close class="inline-flex min-h-12 flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-black text-slate-700">
                Затвори
            </button>
        </div>
    </section>
</div>

<script>
    (() => {
        const modalId = @json($modalId);
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const title = modal.querySelector('[data-growth-modal-title]');
        const status = modal.querySelector('[data-growth-modal-status]');
        const description = modal.querySelector('[data-growth-modal-description]');
        const openers = document.querySelectorAll('[data-growth-open="' + modalId + '"]');
        const closers = modal.querySelectorAll('[data-growth-close]');

        const open = (button) => {
            if (title) title.textContent = button.dataset.growthTitle || 'Инструмент';
            if (status) status.textContent = button.dataset.growthStatus || 'Add-on';
            if (description) description.textContent = button.dataset.growthDescription || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');
        };

        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.documentElement.classList.remove('overflow-hidden');
            document.body.classList.remove('overflow-hidden');
        };

        openers.forEach((button) => button.addEventListener('click', () => open(button)));
        closers.forEach((button) => button.addEventListener('click', close));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) close();
        });

        window.addEventListener('pagehide', close);
        window.addEventListener('pageshow', () => {
            if (modal.classList.contains('hidden')) {
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    })();
</script>

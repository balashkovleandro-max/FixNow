@once
    <style>
        .fn-public-cta {
            transition: transform 220ms ease, box-shadow 220ms ease, filter 220ms ease;
        }

        .fn-public-cta:hover {
            box-shadow: 0 0 0 1px rgba(125, 211, 252, .22), 0 18px 48px rgba(59, 130, 246, .32);
            filter: saturate(1.08);
            transform: translateY(-1px);
        }

        .fn-public-hover {
            transition: transform 240ms ease, border-color 240ms ease, box-shadow 240ms ease, background-color 240ms ease;
            will-change: transform;
        }

        .fn-public-hover:hover {
            border-color: rgba(125, 211, 252, .34);
            box-shadow: 0 22px 58px rgba(2, 8, 18, .42), 0 0 0 1px rgba(59, 130, 246, .10);
            transform: translateY(-4px);
        }

        .fn-mega-panel {
            display: none;
            left: 50%;
            top: calc(100% + .75rem);
            background-color: rgba(2, 6, 23, .98);
            box-shadow: 0 26px 80px rgba(0, 0, 0, .58), 0 0 0 1px rgba(125, 211, 252, .08);
            transform: translate3d(-50%, 8px, 0);
        }

        .fn-mega-trigger.is-active > .fn-mega-panel {
            display: block;
            transform: translate3d(-50%, 0, 0);
        }

        .fn-mega-panel-wide {
            width: min(720px, calc(100vw - 2rem));
        }

        .fn-mega-panel-compact {
            width: min(560px, calc(100vw - 2rem));
        }

        @media (max-width: 1023px) {
            .fn-mega-panel {
                display: none !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .fn-public-cta,
            .fn-public-hover,
            .fn-mega-panel {
                transition: none !important;
            }

            .fn-public-cta:hover,
            .fn-public-hover:hover {
                transform: none !important;
            }
        }
    </style>
@endonce

@php
    $publicServiceMenu = [
        ['title' => 'Ремонти и майстори', 'description' => 'Ремонт, довършителни работи и домашни задачи.', 'url' => route('services.index', ['category' => 'Ремонти и строителство']), 'icon' => '01'],
        ['title' => 'ВиК услуги', 'description' => 'Течове, канали, санитария и аварийни ремонти.', 'url' => route('services.index', ['category' => 'ВиК услуги']), 'icon' => '02'],
        ['title' => 'Електроуслуги', 'description' => 'Електротехници, табла, контакти и осветление.', 'url' => route('services.index', ['category' => 'Електроуслуги']), 'icon' => '03'],
        ['title' => 'Почистване', 'description' => 'Домове, офиси, след ремонт и абонаментна поддръжка.', 'url' => route('services.index', ['category' => 'Почистване']), 'icon' => '04'],
        ['title' => 'Автосервизи', 'description' => 'Сервизи, диагностика, гуми и авто услуги.', 'url' => route('services.index', ['category' => 'Автосервизи']), 'icon' => '05'],
        ['title' => 'Виж всички услуги', 'description' => 'Разгледайте целия каталог с локални категории.', 'url' => route('services.index'), 'icon' => '→'],
    ];

    $publicClientMenu = [
        ['title' => 'Пусни заявка', 'description' => 'Опишете задачата и получете оферти от подходящи изпълнители.', 'url' => route('request.service'), 'icon' => '01'],
        ['title' => 'Как работи', 'description' => 'Вижте процеса от заявка до избран изпълнител.', 'url' => url('/how-it-works'), 'icon' => '02'],
        ['title' => 'Намери изпълнител', 'description' => 'Търсете директно по услуга, град, рейтинг и доверие.', 'url' => route('businesses.index'), 'icon' => '03'],
        ['title' => 'Топ изпълнители', 'description' => 'Профили с рейтинг, препоръки и активна видимост.', 'url' => route('top.businesses'), 'icon' => '04'],
    ];

    $publicExecutorMenu = [
        ['title' => 'Добави профил', 'description' => 'Създайте профил на изпълнител и започнете да се показвате.', 'url' => route('business.landing'), 'icon' => '01'],
        ['title' => 'Получавай заявки', 'description' => 'Виждайте релевантни заявки по град и категория.', 'url' => route('business.landing'), 'icon' => '02'],
        ['title' => 'Изпращай оферти', 'description' => 'Използвайте точки, за да изпращате цена и срок.', 'url' => route('business.landing'), 'icon' => '03'],
        ['title' => 'Планове', 'description' => 'Standard и Premium с ясни лимити и видимост.', 'url' => route('plans'), 'icon' => '04'],
    ];
@endphp

<header id="fn-main-header" class="sticky top-0 z-50 border-b border-white/10 bg-[#030712]/82 backdrop-blur-2xl">
    <div class="mx-auto flex h-[76px] max-w-[1500px] items-center justify-between px-4 sm:px-6 lg:px-12">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black shadow-[0_0_26px_rgba(59,130,246,0.42)]">F</div>
            <span class="text-xl font-black tracking-tight">FixNow.bg</span>
        </a>

        <nav id="fn-desktop-mega-nav" class="hidden items-center gap-2 lg:flex" aria-label="Основна навигация">
            <div class="fn-mega-trigger relative" data-mega-trigger>
                <button type="button" aria-expanded="false" class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-white/75 transition hover:bg-white/10 hover:text-cyan-100 focus:bg-white/10 focus:text-cyan-100 focus:outline-none">
                    Услуги
                    <svg class="h-4 w-4 transition" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                </button>
                <div class="fn-mega-panel fn-mega-panel-wide absolute z-[90] rounded-[30px] border border-white/15 p-4 shadow-2xl backdrop-blur-2xl transition duration-200" data-mega-panel>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($publicServiceMenu as $item)
                            <a href="{{ $item['url'] }}" class="fn-public-hover group/card rounded-3xl border border-white/10 bg-white/5 p-4 hover:bg-cyan-300/10">
                                <div class="flex gap-3">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-cyan-300/10 text-sm font-black text-cyan-100 ring-1 ring-cyan-300/15">{{ $item['icon'] }}</span>
                                    <span>
                                        <span class="block font-black text-white group-hover/card:text-cyan-100">{{ $item['title'] }}</span>
                                        <span class="mt-1 block text-sm leading-5 text-white/55">{{ $item['description'] }}</span>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="fn-mega-trigger relative" data-mega-trigger>
                <button type="button" aria-expanded="false" class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-white/75 transition hover:bg-white/10 hover:text-cyan-100 focus:bg-white/10 focus:text-cyan-100 focus:outline-none">
                    За клиенти
                    <svg class="h-4 w-4 transition" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                </button>
                <div class="fn-mega-panel fn-mega-panel-compact absolute z-[90] rounded-[30px] border border-white/15 p-4 shadow-2xl backdrop-blur-2xl transition duration-200" data-mega-panel>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($publicClientMenu as $item)
                            <a href="{{ $item['url'] }}" class="fn-public-hover rounded-3xl border border-white/10 bg-white/5 p-4 hover:bg-blue-300/10">
                                <span class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-300/10 text-sm font-black text-blue-100 ring-1 ring-blue-300/15">{{ $item['icon'] }}</span>
                                <span class="block font-black text-white">{{ $item['title'] }}</span>
                                <span class="mt-1 block text-sm leading-5 text-white/55">{{ $item['description'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="fn-mega-trigger relative" data-mega-trigger>
                <button type="button" aria-expanded="false" class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-white/75 transition hover:bg-white/10 hover:text-cyan-100 focus:bg-white/10 focus:text-cyan-100 focus:outline-none">
                    За изпълнители
                    <svg class="h-4 w-4 transition" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                </button>
                <div class="fn-mega-panel fn-mega-panel-compact absolute z-[90] rounded-[30px] border border-white/15 p-4 shadow-2xl backdrop-blur-2xl transition duration-200" data-mega-panel>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($publicExecutorMenu as $item)
                            <a href="{{ $item['url'] }}" class="fn-public-hover rounded-3xl border border-white/10 bg-white/5 p-4 hover:bg-violet-300/10">
                                <span class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-300/10 text-sm font-black text-violet-100 ring-1 ring-violet-300/15">{{ $item['icon'] }}</span>
                                <span class="block font-black text-white">{{ $item['title'] }}</span>
                                <span class="mt-1 block text-sm leading-5 text-white/55">{{ $item['description'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a href="{{ route('plans') }}" class="rounded-2xl px-4 py-3 text-sm font-black text-white/75 transition hover:bg-white/10 hover:text-cyan-100">Планове</a>
        </nav>

        <div class="hidden items-center gap-4 md:flex">
            @guest
                <a href="{{ route('login') }}" class="rounded-2xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">Вход</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-2xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">Табло</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="rounded-2xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">Изход</button>
                </form>
            @endauth
            <a href="{{ route('request.service') }}" class="fn-public-cta rounded-2xl bg-gradient-to-r from-blue-500 to-fuchsia-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/25">Пусни заявка</a>
        </div>

        <a href="{{ route('request.service') }}" class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/15 bg-white/10 text-white md:hidden" aria-label="Пусни заявка">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/><path d="M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z" stroke-linejoin="round"/></svg>
        </a>
    </div>
</header>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const header = document.getElementById('fn-main-header');
            const triggers = Array.from(document.querySelectorAll('[data-mega-trigger]'));

            if (!header || !triggers.length) {
                return;
            }

            let closeTimer = null;
            const desktopQuery = window.matchMedia('(min-width: 1024px)');

            const setExpanded = (trigger, expanded) => {
                const button = trigger.querySelector('button[aria-expanded]');
                if (button) {
                    button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                }
            };

            const closeMenus = () => {
                if (closeTimer) {
                    window.clearTimeout(closeTimer);
                    closeTimer = null;
                }

                triggers.forEach((trigger) => {
                    trigger.classList.remove('is-active');
                    setExpanded(trigger, false);
                });
            };

            const openMenu = (activeTrigger) => {
                if (!desktopQuery.matches) {
                    closeMenus();
                    return;
                }

                if (closeTimer) {
                    window.clearTimeout(closeTimer);
                    closeTimer = null;
                }

                triggers.forEach((trigger) => {
                    const isActive = trigger === activeTrigger;
                    trigger.classList.toggle('is-active', isActive);
                    setExpanded(trigger, isActive);
                });
            };

            const scheduleClose = () => {
                if (closeTimer) {
                    window.clearTimeout(closeTimer);
                }

                closeTimer = window.setTimeout(closeMenus, 120);
            };

            triggers.forEach((trigger) => {
                trigger.addEventListener('mouseenter', () => openMenu(trigger));
                trigger.addEventListener('focusin', () => openMenu(trigger));

                const panel = trigger.querySelector('[data-mega-panel]');
                if (panel) {
                    panel.addEventListener('mouseenter', () => openMenu(trigger));
                }
            });

            header.addEventListener('mouseleave', scheduleClose);
            header.addEventListener('mouseenter', () => {
                if (closeTimer) {
                    window.clearTimeout(closeTimer);
                    closeTimer = null;
                }
            });

            document.addEventListener('pointerdown', (event) => {
                if (!header.contains(event.target)) {
                    closeMenus();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenus();
                }
            });

            document.addEventListener('focusin', (event) => {
                if (!header.contains(event.target)) {
                    closeMenus();
                }
            });

            if (desktopQuery.addEventListener) {
                desktopQuery.addEventListener('change', closeMenus);
            } else {
                desktopQuery.addListener(closeMenus);
            }
        });
    </script>
@endonce

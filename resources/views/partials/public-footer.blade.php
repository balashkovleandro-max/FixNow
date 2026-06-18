<footer data-testid="public-footer" class="border-t border-white/10 bg-slate-950/88 shadow-[0_-18px_60px_rgba(0,0,0,0.22)] backdrop-blur-2xl">
    <div class="mx-auto grid max-w-[1500px] gap-6 px-4 py-7 sm:grid-cols-2 sm:gap-8 sm:px-6 sm:py-10 md:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-12">
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 font-black text-white shadow-lg shadow-violet-500/25">B</span>
                <span class="text-xl font-black text-white">BON</span>
            </a>
            <p class="mt-4 max-w-md text-sm leading-7 text-slate-300">
                Business operating platform за профили, инструменти, задачи, анализ, доверие и растеж. BON помага на бизнесите да изглеждат по-професионално и да работят по-структурирано.
            </p>
            <p class="mt-4 text-sm text-slate-400">Въпроси: <a href="mailto:hello@bon.bg" onclick="window.trackBonEvent('contact_click', { source: 'footer' })" class="font-bold text-blue-300 hover:text-violet-200">hello@bon.bg</a></p>
        </div>

        <nav class="grid gap-2.5 text-sm text-slate-400 sm:gap-3" aria-label="Основна навигация">
            <p class="font-black text-white">Платформа</p>
            <a href="{{ url('/') }}" class="hover:text-blue-200">Начало</a>
            <a href="{{ route('search') }}" class="hover:text-blue-200">Търсене</a>
            <a href="{{ route('business.landing') }}" class="hover:text-blue-200">За бизнеси</a>
            <a href="{{ route('bon.freelancers') }}" class="hover:text-blue-200">Фрилансъри</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-200">Инструменти</a>
            <a href="{{ route('plans') }}" class="hover:text-blue-200">Планове</a>
            <a href="{{ route('contact') }}" class="hover:text-blue-200">Контакт</a>
        </nav>

        <nav class="grid gap-2.5 text-sm text-slate-400 sm:gap-3" aria-label="За бизнеси">
            <p class="font-black text-white">За бизнеси</p>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-blue-200">Добави бизнес</a>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-blue-200">Бизнес присъствие</a>
            <a href="{{ route('bon.freelancers') }}" class="hover:text-blue-200">Профил за фрилансър</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-200">Финансов анализ</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-200">Business Health Score</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-200">Premium видимост</a>
        </nav>

        <nav class="grid gap-2.5 text-sm text-slate-400 sm:gap-3" aria-label="Правна информация">
            <p class="font-black text-white">Доверие</p>
            <a href="{{ route('terms') }}" data-testid="footer-terms-link" class="hover:text-blue-200">Общи условия</a>
            <a href="{{ route('privacy') }}" data-testid="footer-privacy-link" class="hover:text-blue-200">Политика за поверителност</a>
            <a href="{{ route('cookies') }}" data-testid="footer-cookies-link" class="hover:text-blue-200">Политика за бисквитки</a>
            @guest
                <a href="{{ route('login') }}" onclick="window.trackBonEvent('login_start', { source: 'footer' })" class="hover:text-blue-200">Вход</a>
                <a href="{{ route('register') }}" onclick="window.trackBonEvent('sign_up_start', { source: 'footer' })" class="hover:text-blue-200">Регистрация</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-blue-200">Моето табло</a>
            @endauth
        </nav>
    </div>

    <div class="border-t border-white/10 px-4 pb-24 pt-5 text-center text-xs text-slate-500 md:pb-6">
        © {{ date('Y') }} BON. Всички права запазени.
    </div>
</footer>

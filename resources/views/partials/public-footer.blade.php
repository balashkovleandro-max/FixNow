<footer data-testid="public-footer" class="border-t border-white/10 bg-[#020617]/94">
    <div class="mx-auto grid max-w-[1500px] gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-12">
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 via-amber-400 to-orange-600 font-black text-white shadow-lg shadow-orange-950/30">F</span>
                <span class="text-xl font-black text-white">Fix<span class="text-orange-400">Now</span>.bg</span>
            </a>
            <p class="mt-4 max-w-md text-sm leading-7 text-white/60">
                Платформа за локални услуги и изпълнители, която помага на клиентите да намират проверени профили, а на професионалистите да получават повече директни запитвания.
            </p>
            <p class="mt-4 text-sm text-white/45">Въпроси: <a href="mailto:hello@fixnow.bg" class="font-bold text-orange-300 hover:text-white">hello@fixnow.bg</a></p>
        </div>

        <nav class="grid gap-3 text-sm text-white/60" aria-label="Основна навигация">
            <p class="font-black text-white">Платформа</p>
            <a href="{{ url('/') }}" class="hover:text-orange-300">Начало</a>
            <a href="{{ route('services.index') }}" class="hover:text-orange-300">Услуги</a>
            <a href="{{ route('businesses.index') }}" class="hover:text-orange-300">Изпълнители</a>
            <a href="{{ route('top.businesses') }}" class="hover:text-orange-300">Топ изпълнители</a>
            <a href="{{ url('/how-it-works') }}" class="hover:text-orange-300">Как работи</a>
        </nav>

        <nav class="grid gap-3 text-sm text-white/60" aria-label="За изпълнители">
            <p class="font-black text-white">За изпълнители</p>
            <a href="{{ route('plans') }}" class="hover:text-orange-300">Планове</a>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-orange-300">Стани изпълнител</a>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-orange-300">За изпълнители</a>
            <a href="{{ route('request.service') }}" data-track="cta_request" class="hover:text-orange-300">Заяви оферта</a>
            <a href="{{ route('contact') }}" class="hover:text-orange-300">Контакт</a>
        </nav>

        <nav class="grid gap-3 text-sm text-white/60" aria-label="Правна информация">
            <p class="font-black text-white">Доверие</p>
            <a href="{{ route('terms') }}" data-testid="footer-terms-link" class="hover:text-orange-300">Общи условия</a>
            <a href="{{ route('privacy') }}" data-testid="footer-privacy-link" class="hover:text-orange-300">Политика за поверителност</a>
            <a href="{{ route('cookies') }}" data-testid="footer-cookies-link" class="hover:text-orange-300">Политика за бисквитки</a>
            @guest
                <a href="{{ route('login') }}" class="hover:text-orange-300">Вход</a>
                <a href="{{ route('register') }}" class="hover:text-orange-300">Регистрация</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-orange-300">Моето табло</a>
            @endauth
        </nav>
    </div>

    <div class="border-t border-white/10 px-4 pb-24 pt-5 text-center text-xs text-white/40 md:pb-6">
        © {{ date('Y') }} FixNow.bg. Всички права запазени.
    </div>
</footer>

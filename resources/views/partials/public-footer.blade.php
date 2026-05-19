<footer data-testid="public-footer" class="border-t border-white/10 bg-[#030712]/90">
    <div class="mx-auto grid max-w-[1500px] gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-12">
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-300 via-blue-500 to-violet-600 font-black text-white">F</span>
                <span class="text-xl font-black text-white">FixNow.bg</span>
            </a>
            <p class="mt-4 max-w-md text-sm leading-7 text-white/60">
                Платформа за локални услуги и изпълнители, която помага на клиентите да намират проверени профили, а на професионалистите да получават повече директни запитвания.
            </p>
            <p class="mt-4 text-sm text-white/45">Въпроси: <a href="mailto:hello@fixnow.bg" class="font-bold text-cyan-200 hover:text-white">hello@fixnow.bg</a></p>
        </div>

        <nav class="grid gap-3 text-sm text-white/60" aria-label="Основна навигация">
            <p class="font-black text-white">Платформа</p>
            <a href="{{ url('/') }}" class="hover:text-cyan-200">Начало</a>
            <a href="{{ route('services.index') }}" class="hover:text-cyan-200">Услуги</a>
            <a href="{{ route('businesses.index') }}" class="hover:text-cyan-200">Изпълнители</a>
            <a href="{{ route('top.businesses') }}" class="hover:text-cyan-200">Топ изпълнители</a>
            <a href="{{ url('/how-it-works') }}" class="hover:text-cyan-200">Как работи</a>
        </nav>

        <nav class="grid gap-3 text-sm text-white/60" aria-label="За изпълнители">
            <p class="font-black text-white">За изпълнители</p>
            <a href="{{ route('plans') }}" class="hover:text-cyan-200">Планове</a>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-cyan-200">Стани изпълнител</a>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-cyan-200">За изпълнители</a>
            <a href="{{ route('request.service') }}" data-track="cta_request" class="hover:text-cyan-200">Заяви оферта</a>
            <a href="{{ route('contact') }}" class="hover:text-cyan-200">Контакт</a>
        </nav>

        <nav class="grid gap-3 text-sm text-white/60" aria-label="Правна информация">
            <p class="font-black text-white">Доверие</p>
            <a href="{{ route('terms') }}" data-testid="footer-terms-link" class="hover:text-cyan-200">Общи условия</a>
            <a href="{{ route('privacy') }}" data-testid="footer-privacy-link" class="hover:text-cyan-200">Политика за поверителност</a>
            <a href="{{ route('cookies') }}" data-testid="footer-cookies-link" class="hover:text-cyan-200">Политика за бисквитки</a>
            @guest
                <a href="{{ route('login') }}" class="hover:text-cyan-200">Вход</a>
                <a href="{{ route('register') }}" class="hover:text-cyan-200">Регистрация</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-200">Моето табло</a>
            @endauth
        </nav>
    </div>

    <div class="border-t border-white/10 px-4 pb-24 pt-5 text-center text-xs text-white/40 md:pb-6">
        © {{ date('Y') }} FixNow.bg. Всички права запазени.
    </div>
</footer>

<footer data-testid="public-footer" class="border-t border-white/70 bg-white/82 shadow-[0_-18px_60px_rgba(30,64,175,0.08)] backdrop-blur-2xl">
    <div class="mx-auto grid max-w-[1500px] gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-12">
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 via-violet-600 to-fuchsia-500 font-black text-white shadow-lg shadow-violet-500/25">B</span>
                <span class="text-xl font-black text-[#070B1F]">BON</span>
            </a>
            <p class="mt-4 max-w-md text-sm leading-7 text-slate-600">
                Платформа за бизнес присъствие, видимост и доверие. BON помага на бизнесите да изглеждат по-професионално онлайн и да бъдат по-лесно откривани.
            </p>
            <p class="mt-4 text-sm text-slate-500">Въпроси: <a href="mailto:hello@bon.bg" class="font-bold text-blue-600 hover:text-violet-600">hello@bon.bg</a></p>
        </div>

        <nav class="grid gap-3 text-sm text-slate-600" aria-label="Основна навигация">
            <p class="font-black text-[#070B1F]">Платформа</p>
            <a href="{{ url('/') }}" class="hover:text-blue-600">Начало</a>
            <a href="{{ route('business.landing') }}" class="hover:text-blue-600">За бизнеси</a>
            <a href="{{ route('bon.freelancers') }}" class="hover:text-blue-600">Фрилансъри</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-600">Инструменти</a>
            <a href="{{ route('plans') }}" class="hover:text-blue-600">Планове</a>
            <a href="{{ route('contact') }}" class="hover:text-blue-600">Контакт</a>
        </nav>

        <nav class="grid gap-3 text-sm text-slate-600" aria-label="За бизнеси">
            <p class="font-black text-[#070B1F]">За бизнеси</p>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-blue-600">Добави бизнес</a>
            <a href="{{ route('business.landing') }}" data-track="cta_business_signup" class="hover:text-blue-600">Бизнес присъствие</a>
            <a href="{{ route('bon.freelancers') }}" class="hover:text-blue-600">Профил за фрилансър</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-600">Финансов анализ</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-600">Business Health Score</a>
            <a href="{{ route('bon.tools') }}" class="hover:text-blue-600">Premium видимост</a>
        </nav>

        <nav class="grid gap-3 text-sm text-slate-600" aria-label="Правна информация">
            <p class="font-black text-[#070B1F]">Доверие</p>
            <a href="{{ route('terms') }}" data-testid="footer-terms-link" class="hover:text-blue-600">Общи условия</a>
            <a href="{{ route('privacy') }}" data-testid="footer-privacy-link" class="hover:text-blue-600">Политика за поверителност</a>
            <a href="{{ route('cookies') }}" data-testid="footer-cookies-link" class="hover:text-blue-600">Политика за бисквитки</a>
            @guest
                <a href="{{ route('login') }}" class="hover:text-blue-600">Вход</a>
                <a href="{{ route('register') }}" class="hover:text-blue-600">Регистрация</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Моето табло</a>
            @endauth
        </nav>
    </div>

    <div class="border-t border-slate-200/70 px-4 pb-24 pt-5 text-center text-xs text-slate-500 md:pb-6">
        © {{ date('Y') }} BON. Всички права запазени.
    </div>
</footer>

## FixNow.bg local setup

FixNow.bg е Laravel marketplace за локални услуги и бизнес профили с mobile-first публичен UX, Standard/Premium планове, Stripe Checkout foundation, business dashboard, admin dashboard, reviews, analytics и lead заявки.

### Стартиране локално

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan test
npm run build
npm run dev
php artisan serve
```

Ако използвате Laravel Herd, стартирайте проекта през Herd и изпълнявайте artisan командите от Herd terminal/CMD с наличен PHP.

Минимални `.env` настройки за локална проверка:

```env
APP_URL=http://fixnow.test
MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@fixnow.bg
MAIL_FROM_NAME="FixNow.bg"
```

### Demo съдържание

За чиста локална база с realistic demo content:

```bash
php artisan migrate:fresh --seed
```

Seeder-ът създава demo admin, client, Standard и Premium бизнеси, услуги, отзиви, препоръки, analytics events и няколко заявки за оферта. Не се използват реални Stripe IDs или реални лични данни.

### Demo login данни

Всички demo акаунти използват парола:

```text
password
```

- `admin@example.com` - admin dashboard
- `business@example.com` - почти празен Standard бизнес за onboarding/completeness тест
- `premium@example.com` - пълен Premium бизнес профил
- `client@example.com` - клиентски профил

### Страници за ръчна проверка

- `/` - homepage с live секции
- `/businesses` - публични бизнес cards
- `/services` - услуги с demo business данни
- `/businesses/{id}` - business detail profile
- `/dashboard` - role-based dashboard
- `/plans` - Standard/Premium pricing
- `/business/billing` - business billing page
- `/zayavi-oferta` - lead/request form
- `/top-biznesi` - класации
- `/terms`, `/privacy`, `/cookies`, `/contact`, `/how-it-works` - trust/legal foundation

### Stripe test mode

Попълнете Stripe test keys в `.env`, преди да тествате checkout:

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_STANDARD_PRICE_ID=price_1TYmByRqvGMkwX9rN7HTUunp
STRIPE_PREMIUM_PRICE_ID=price_1TYmCcRqvGMkwX9rE8ichDo4
```

За local Stripe test mode използвайте test keys и горните test Price IDs. `STRIPE_WEBHOOK_SECRET` може да остане празен, докато не стартирате webhook testing; след като Stripe CLI/Dashboard върне `whsec_...`, добавете стойността в `.env`, защото `/stripe/webhook` отхвърля неподписани webhook заявки.

Плановете Standard/Premium не се активират от UI бутон. Активирането става само след валиден Stripe webhook.

Преди soft launch проверете още:

```bash
php artisan route:list
php artisan migrate
php artisan test
npm run build
```

В production `.env` задайте реални `APP_URL`, mail настройки, Stripe live/test ключове според средата и потвърдете, че Stripe Customer Portal е активиран в Stripe dashboard.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

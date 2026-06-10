# BON Deployment Guide

Този документ описва минималните стъпки за deploy на BON в staging или production среда. Не съхранявайте реални secret стойности в Git. Всички production стойности трябва да идват от environment variables.

## Изисквания

- PHP 8.3 или по-нова версия според `composer.json`.
- Composer 2.x.
- Node.js и npm, съвместими с текущия `package-lock.json`.
- Database: PostgreSQL или MySQL за production.
- HTTPS домейн преди реални потребители и Stripe live плащания.

## Build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

`npm run build` трябва да генерира:

```text
public/build/manifest.json
```

Ако production страницата изглежда без стилове, проверете дали `public/hot` не присъства и дали Blade view-овете използват `@vite(['resources/css/app.css', 'resources/js/app.js'])`.

## Environment

Примерни production стойности:

```env
APP_NAME="BON"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bon.bg

DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@bon.bg
MAIL_FROM_NAME="${APP_NAME}"
BON_ADMIN_EMAIL=admin@bon.bg

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_STANDARD_PRICE_ID=
STRIPE_PREMIUM_PRICE_ID=

GA_MEASUREMENT_ID=
META_PIXEL_ID=
CLARITY_PROJECT_ID=
```

Важно:

- `APP_DEBUG=false` за production.
- `APP_URL` трябва да е реалният HTTPS домейн.
- Stripe keys, webhook secret и Price IDs трябва да са от един и същ Stripe режим.
- Не използвайте SQLite за production.

## Stripe

Webhook endpoint:

```text
https://bon.bg/stripe/webhook
```

Stripe Customer Portal трябва да е enabled в Stripe Dashboard. Платен план се активира само чрез Stripe webhook, не чрез success URL.

## Database и storage

```bash
php artisan migrate --force
php artisan storage:link
```

Не изпълнявайте `php artisan migrate:fresh --seed` в production, защото изтрива реални данни.

## Admin и soft launch данни

Създаване на admin:

```bash
php artisan bon:create-admin --name="BON Admin" --email="admin@bon.bg"
```

За non-interactive staging setup:

```bash
php artisan bon:create-admin --name="BON Admin" --email="admin@bon.bg" --password="temporary-secure-password"
```

Контролиран soft-launch dataset за Плевен:

```bash
php artisan db:seed --class=SoftLaunchPlevenSeeder
```

Seeder-ът създава контролирани бизнес профили за проверка на публични cards, категории, Premium/Verified badges и visibility logic. Не го използвайте за production, освен ако това е умишлен staging тест.

## Cache при deploy

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ако `route:cache` върне грешка за Closure routes, пропуснете само `route:cache` за този deploy и оставете останалите cache команди.

## Проверки преди deploy

```bash
php artisan test
npm run build
php artisan route:list
```

Ръчни проверки:

- `/health` връща `{"status":"ok","app":"BON"}`.
- `/`, `/categories`, `/businesses`, `/services`, `/plans`, `/za-biznesi`.
- `/login` и `/register` показват BON.
- `/business/billing` зарежда и не активира план без Stripe webhook.
- `/stripe/webhook` валидира Stripe signature чрез `STRIPE_WEBHOOK_SECRET`.
- `/admin/service-requests` е достъпен само за admin.
- Emails използват `APP_URL`/route URL-и и BON copy.

## Rollback

1. Намерете последния стабилен commit:

```bash
git log --oneline -10
```

2. Върнете release директорията към стабилния commit:

```bash
git checkout <stable-commit>
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

Не изпълнявайте destructive database команди без backup.

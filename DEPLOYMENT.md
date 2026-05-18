# FixNow.bg Deployment Guide

Този документ описва минималните стъпки за качване на FixNow.bg на test/staging или production сървър. Не съхранявайте реални secret стойности в Git.

## Изисквания

- PHP 8.3 или по-нова версия, според `composer.json`.
- Composer 2.x.
- Node.js и npm, съвместими с текущия `package-lock.json`.
- Web server с document root към `public/`.
- Database: SQLite за малък test deployment или MySQL/PostgreSQL за production.
- HTTPS сертификат преди реално приемане на потребители и плащания.

## Подготовка на кода

```bash
git clone <repo-url> fixnow
cd fixnow
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

За staging, където ще се пускат тестове, може да използвате:

```bash
composer install
npm ci
npm run build
php artisan test
```

## `.env` настройка

```bash
cp .env.example .env
php artisan key:generate
```

Задължителни production стойности:

```env
APP_NAME="FixNow.bg"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://fixnow.bg
```

Настройте database секцията според сървъра. За production не използвайте локални demo credentials.

Mail настройките трябва да сочат към реален SMTP/mail provider:

```env
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@fixnow.bg
MAIL_FROM_NAME="${APP_NAME}"
FIXNOW_ADMIN_EMAIL=admin@fixnow.bg
```

Stripe настройките трябва да са от правилния test или live режим:

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_STANDARD_PRICE_ID=
STRIPE_PREMIUM_PRICE_ID=
```

Webhook endpoint в Stripe:

```text
https://fixnow.bg/stripe/webhook
```

Stripe Customer Portal трябва да е enabled в Stripe Dashboard.

## Database и storage

```bash
php artisan migrate --force
php artisan storage:link
```

Не пускайте `php artisan migrate:fresh --seed` на production. Тази команда е само за local/demo среда, защото изтрива всички данни.

За local/staging demo preview:

```bash
php artisan migrate:fresh --seed
```

## Permissions

Linux пример:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache
```

Потребителят на web server-а трябва да може да пише в `storage/` и `bootstrap/cache/`.

## Queue и cron

Проектът използва database queue driver в `.env.example`. Ако production използва queue за mail/job обработка, стартирайте worker чрез Supervisor/systemd:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Laravel scheduler cron, ако добавите scheduled tasks:

```cron
* * * * * cd /path/to/fixnow && php artisan schedule:run >> /dev/null 2>&1
```

## Cache при deploy

Преди финално включване:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ако `route:cache` върне грешка за Closure routes, пропуснете само тази команда и планирайте отделна задача за преместване на closure routes към controllers.

При проблем след deploy:

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## Проверки преди deploy

```bash
php artisan test
npm run build
php artisan route:list
```

Проверете ръчно:

- `/health` връща `{"status":"ok","app":"FixNow.bg"}`.
- `/robots.txt` и `/sitemap.xml` се зареждат.
- `/`, `/businesses`, `/services`, `/plans`, `/zayavi-oferta`.
- `/business/billing` като изпълнител.
- `/admin/service-requests` като admin.
- Stripe checkout в test mode.
- Stripe webhook activation.
- Email уведомления за заявки и оферти.

## Rollback чрез Git

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

3. Ако rollback-ът изисква database промени, не изпълнявайте destructive команди без backup. Възстановете database backup, ако миграциите са променили структурата необратимо.

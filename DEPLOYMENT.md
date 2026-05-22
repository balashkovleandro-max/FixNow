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

## Scalingo + Vite build

FixNow uses Laravel Vite through `@vite(...)` in Blade views. In production this requires
`public/build/manifest.json`. The repository includes a `.buildpacks` file so Scalingo runs
the Node.js buildpack before the PHP buildpack:

```text
https://github.com/Scalingo/nodejs-buildpack
https://github.com/Scalingo/php-buildpack
```

The `package.json` build script must stay present:

```json
"build": "vite build"
```

Recommended Scalingo environment setting when using the dedicated Node.js buildpack:

```bash
scalingo --app <app-name> env-set PHP_BUILDPACK_NO_NODE=true
```

Deploy:

```bash
git add .buildpacks package.json package-lock.json
git commit -m "Configure Scalingo Vite build"
git push scalingo main
```

Verify after deploy:

```bash
scalingo --app <app-name> run 'ls -la public/build/manifest.json'
```

If the file is missing, inspect the deploy logs for the Node.js buildpack step and confirm
that `npm ci` and `npm run build` ran before the PHP/Laravel app started.

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

## Soft launch data setup

Създайте admin акаунт без да записвате парола в Git или в публична документация:

```bash
php artisan fixnow:create-admin --name="FixNow Admin" --email="admin@fixnow.bg"
```

Ако не подадете `--password`, command-ът ще я поиска интерактивно и няма да я показва в output. За non-interactive staging setup може да подадете:

```bash
php artisan fixnow:create-admin --name="FixNow Admin" --email="admin@fixnow.bg" --password="temporary-secure-password"
```

По желание за test/soft launch в Плевен може да заредите контролирани примерни изпълнители:

```bash
php artisan db:seed --class=SoftLaunchPlevenSeeder
```

Този seeder създава 5+ профила за Плевен в категории ремонти, ВиК, електроуслуги, почистване и автосервизи. Използва placeholder телефони и `@fixnow.test` email-и, за да не изглежда като реални фирмени данни. Не seed-ва fake reviews или fake analytics, за да започнат тези метрики от реална активност.

Default `DatabaseSeeder` е само за local/demo preview и е защитен да не се изпълнява в `APP_ENV=production`. Не пускайте `migrate:fresh --seed` на production, защото изтрива реални данни.

След soft-launch seed проверете:

- Login като admin работи.
- `/businesses?city=Плевен` показва seeded изпълнители.
- `/grad/pleven` и `/grad/pleven/vik-uslugi` зареждат.
- Поне един Premium и един verified профил се виждат публично.
- Expired/cancelled профили не се показват публично.

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

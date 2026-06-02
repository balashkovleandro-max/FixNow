# FixNow.bg Laravel Cloud Deploy Checklist

Този документ е production checklist за Laravel Cloud deploy и Stripe live плащания. Не записвайте реални secret стойности в Git. Всички production стойности трябва да се задават през Laravel Cloud environment variables.

## 1. Laravel Cloud Environment

Задайте тези променливи в Laravel Cloud dashboard:

```env
APP_NAME="FixNow.bg"
APP_ENV=production
APP_KEY=<generated-by-laravel-cloud-or-artisan-key-generate>
APP_DEBUG=false
APP_URL=https://fixnow.bg

APP_LOCALE=bg
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=bg_BG

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=<laravel-cloud-database-host>
DB_PORT=5432
DB_DATABASE=<laravel-cloud-database-name>
DB_USERNAME=<laravel-cloud-database-user>
DB_PASSWORD=<laravel-cloud-database-password>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=<mail-provider-host>
MAIL_PORT=587
MAIL_USERNAME=<mail-provider-user>
MAIL_PASSWORD=<mail-provider-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@fixnow.bg
MAIL_FROM_NAME="${APP_NAME}"
FIXNOW_ADMIN_EMAIL=admin@fixnow.bg

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_STANDARD_PRICE_ID=price_...
STRIPE_PREMIUM_PRICE_ID=price_...

GA_MEASUREMENT_ID=
META_PIXEL_ID=
CLARITY_PROJECT_ID=
```

Важно:

- `APP_DEBUG` трябва да е `false`.
- `APP_URL` трябва да е реалният HTTPS домейн.
- `STRIPE_*` стойностите трябва да са от същия Stripe режим. Не смесвайте test keys с live Price IDs.
- `STRIPE_WEBHOOK_SECRET` се взима от Stripe Dashboard след създаване на endpoint-а.
- Не използвайте SQLite за production.

## 2. Build Settings

Laravel Cloud трябва да изпълни PHP/Composer и Node build стъпките.

Очаквани build команди:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

`npm run build` трябва да генерира:

```text
public/build/manifest.json
```

Blade view-овете използват `@vite(['resources/css/app.css', 'resources/js/app.js'])`, така че не hardcode-вайте hashed asset имена.

Ако production страницата изглежда като bare HTML:

1. Проверете дали `public/build/manifest.json` съществува.
2. Проверете дали `public/hot` не съществува в production.
3. Проверете дали deploy log-ът съдържа `npm run build`.

## 3. Post-deploy Artisan Commands

След първия deploy:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ако `route:cache` върне грешка за Closure routes, не пускайте destructive промени. Пропуснете само `route:cache` за този deploy и планирайте отделна малка задача за изнасяне на closure routes към controllers.

Текущо има closure routes за:

- `/health`
- `/robots.txt`
- `/sitemap.xml`
- `/`
- `/categories`
- `/dashboard`

Ако вашата Laravel версия/Cloud build не кешира closure routes, това е единствената очаквана причина `route:cache` да бъде пропуснат временно.

При проблем след deploy:

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## 4. Stripe Dashboard Setup

В Stripe Dashboard:

1. Създайте live products/prices:
   - Standard: 18.99 EUR / monthly
   - Premium: 24.99 EUR / monthly
2. Копирайте live Price IDs в:
   - `STRIPE_STANDARD_PRICE_ID`
   - `STRIPE_PREMIUM_PRICE_ID`
3. Включете Stripe Customer Portal.
4. Създайте webhook endpoint:

```text
https://fixnow.bg/stripe/webhook
```

5. Добавете поне тези events:
   - `checkout.session.completed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_failed`
6. Копирайте webhook signing secret в `STRIPE_WEBHOOK_SECRET`.

Очаквано поведение:

- Checkout Session е `mode=subscription`.
- Standard използва `STRIPE_STANDARD_PRICE_ID`.
- Premium използва `STRIPE_PREMIUM_PRICE_ID`.
- Планът не се активира от success URL.
- Активиране става само при валиден Stripe webhook.
- `invoice.payment_failed` спира Premium benefits чрез non-active subscription status.
- `customer.subscription.deleted` маркира subscription като canceled и спира paid benefits.

## 5. Mail / Queue

FixNow изпраща email уведомления синхронно чрез `Mail::to(...)->send(...)`, така че работи и с `QUEUE_CONNECTION=sync`, и с `QUEUE_CONNECTION=database`.

В проекта има queue migrations за:

- `jobs`
- `job_batches`
- `failed_jobs`

Ако по-късно mail-ите се queue-нат, стартирайте worker през Laravel Cloud process/worker настройка:

```bash
php artisan queue:work --tries=3 --timeout=90
```

За production mail:

- използвайте реален SMTP provider;
- задайте `MAIL_FROM_ADDRESS`;
- задайте `MAIL_FROM_NAME`;
- направете тестова заявка и проверете дали клиентът/изпълнителят/admin получават email.

## 6. Storage / Uploads

Качванията се записват в public disk (`storage/app/public`) и се показват през `/storage/...`.

След deploy:

```bash
php artisan storage:link
```

Проверете:

- business gallery upload;
- service request photos;
- public business profile without photos;
- fallback UI при липсваща снимка;
- `public/images/fixnow-hero-city.svg` е commit-нат и наличен.

Ако Laravel Cloud използва persistent storage/add-on за uploads, уверете се, че `storage/app/public` е persistent между deploy-и.

## 7. Manual Smoke Tests

Проверете публичните страници:

- `/`
- `/businesses`
- `/services`
- `/plans`
- `/zayavi-oferta`
- `/za-biznesi`
- `/contact`
- `/how-it-works`
- `/terms`
- `/privacy`
- `/cookies`
- `/login`
- `/register`

Проверете core flow:

1. Регистрация на customer.
2. Регистрация на изпълнител.
3. Login/logout.
4. Public business listing.
5. Business profile page.
6. Search/filter по град и категория.
7. Пускане на заявка през `/zayavi-oferta`.
8. Запитване от public business profile.
9. Business dashboard.
10. `/business/service-requests`.
11. Изпращане на оферта.
12. Customer offer link `/zayavka/{public_token}/offers`.
13. Приемане на оферта.
14. Admin dashboard.
15. `/admin/service-requests`.

Проверете billing:

1. `/business/billing` зарежда за business user.
2. Guest/client нямат достъп до business billing.
3. Standard checkout тръгва към Stripe.
4. Premium checkout тръгва към Stripe.
5. Cancelled checkout не активира план.
6. Success URL сам по себе си не активира план.
7. Валиден webhook активира правилния план.
8. Customer Portal се отваря при активен/trialing Stripe subscription.
9. Duplicate active subscriptions не се създават от checkout.

## 8. Domain / DNS

В Laravel Cloud:

1. Добавете custom domain `fixnow.bg`.
2. Добавете `www.fixnow.bg`, ако ще се използва.
3. Следвайте DNS инструкциите на Laravel Cloud.
4. Изчакайте SSL provisioning.
5. Настройте canonical redirect според избрания домейн, ако Laravel Cloud не го прави автоматично.
6. Обновете Stripe webhook URL към live HTTPS домейна.
7. Обновете `APP_URL`.

## 9. Pre-live Checklist

- [ ] `php artisan test` минава.
- [ ] `npm run build` минава.
- [ ] `public/build/manifest.json` съществува.
- [ ] `APP_DEBUG=false`.
- [ ] Real mail provider е настроен.
- [ ] Stripe live keys и live Price IDs са настроени.
- [ ] Stripe webhook подписът е настроен.
- [ ] Customer Portal е enabled.
- [ ] Admin user е създаден.
- [ ] Поне 3-5 реални изпълнители са добавени.
- [ ] Upload/fallback images работят.
- [ ] Mobile smoke check е минал.
- [ ] Legal pages са прегледани.
- [ ] Database backup/restore стратегия е ясна.

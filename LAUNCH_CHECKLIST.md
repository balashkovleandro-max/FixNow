# FixNow.bg Soft Launch Checklist

## Pre-deploy

- [ ] `php artisan test` минава без failing tests.
- [ ] `npm run build` минава успешно.
- [ ] `git status` е прегледан и няма неочаквани промени.
- [ ] `.env` е прегледан за production/test средата.
- [ ] `APP_ENV=production` за production.
- [ ] `APP_DEBUG=false` за production.
- [ ] `APP_URL` сочи към реалния домейн.
- [ ] Database credentials са production/test, не local/demo.
- [ ] Real mail provider е настроен.
- [ ] `MAIL_FROM_ADDRESS` и `MAIL_FROM_NAME` са коректни.
- [ ] Stripe test/live mode е избран съзнателно.
- [ ] `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` са настроени.
- [ ] `STRIPE_STANDARD_PRICE_ID` и `STRIPE_PREMIUM_PRICE_ID` са настроени.
- [ ] Local test Price IDs: Standard `price_1TYmByRqvGMkwX9rN7HTUunp`, Premium `price_1TYmCcRqvGMkwX9rE8ichDo4`.
- [ ] `STRIPE_WEBHOOK_SECRET` е попълнен след Stripe webhook testing (`whsec_...`); докато е празен, `/stripe/webhook` правилно отхвърля webhook заявки.
- [ ] Stripe Customer Portal е enabled.
- [ ] Stripe webhook endpoint е настроен към `/stripe/webhook`.
- [ ] Admin user е създаден чрез `php artisan fixnow:create-admin`.
- [ ] Ако е нужен контролиран Pleven test dataset, изпълнен е `php artisan db:seed --class=SoftLaunchPlevenSeeder`.
- [ ] Demo seeders не са пускани на production, освен ако това е умишлен staging тест.
- [ ] Проверено е, че seeded изпълнителите се виждат в `/businesses?city=Плевен`.
- [ ] `php artisan storage:link` е изпълнен.
- [ ] Permissions за `storage/` и `bootstrap/cache/` са проверени.
- [ ] Backup стратегия за database е избрана.
- [ ] `public/hot` не присъства на production server.
- [ ] `robots.txt` и `sitemap.xml` са проверени.
- [ ] `/health` връща 200.

## Deploy commands

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ако `route:cache` върне грешка за Closure routes, пропуснете само `route:cache` за този deploy и оставете останалите cache команди.

## Post-deploy

- [ ] Homepage `/` зарежда.
- [ ] `/services` зарежда.
- [ ] `/businesses` зарежда.
- [ ] `/top-biznesi` зарежда.
- [ ] `/plans` зарежда.
- [ ] `/zayavi-oferta` зарежда.
- [ ] `/how-it-works`, `/contact`, `/terms`, `/privacy`, `/cookies` зареждат.
- [ ] Login/register работят.
- [ ] Business/executor dashboard зарежда.
- [ ] `/business/billing` зарежда и не активира план без Stripe webhook.
- [ ] Admin dashboard зарежда.
- [ ] `/admin/service-requests` зарежда само за admin.
- [ ] Request form работи.
- [ ] Offer flow работи: заявка, оферта, избор на изпълнител.
- [ ] Customer offer link `/zayavka/{public_token}/offers` работи.
- [ ] Email към клиент при нова оферта се получава.
- [ ] Email към избран изпълнител се получава.
- [ ] Stripe checkout работи в избрания test/live режим.
- [ ] Stripe webhook активира правилния план.
- [ ] Mobile check passed за homepage, listings, request form, offer page и dashboards.
- [ ] Няма хоризонтален scroll на основните mobile страници.

## Soft launch

- [ ] Добавени са 5-10 реални изпълнители.
- [ ] Профилите имат реални описания, градове, категории и снимки.
- [ ] Поне един изпълнител е маркиран като verified от admin.
- [ ] Поне един Standard и един Premium профил са тествани.
- [ ] Тествана е една реална клиентска заявка.
- [ ] Тествана е поне една реална оферта от изпълнител.
- [ ] Клиентът е избрал изпълнител през offer link.
- [ ] Събрана е обратна връзка от първите клиенти/изпълнители.
- [ ] Error logs се наблюдават ежедневно.
- [ ] Stripe dashboard се наблюдава при първите checkout тестове.
- [ ] Mail delivery logs се наблюдават при първите заявки.

## Final manual QA

- [ ] Client request flow: homepage → `Пусни заявка` → `/zayavi-oferta` → success message → `Виж получените оферти`.
- [ ] Customer offer page: `/zayavka/{public_token}/offers` показва empty state без оферти и cards при получени оферти.
- [ ] Customer accept flow: клиентът избира една оферта, вижда success state и не може да избере втора оферта.
- [ ] Business offer flow: изпълнителят вижда релевантни заявки, изпраща оферта, точките се намаляват и вижда success message.
- [ ] Accepted executor view: избраният изпълнител вижда `Активна поръчка` и инструкция да се свърже с клиента.
- [ ] Not selected executor view: неизбраният изпълнител вижда, че клиентът е избрал друг изпълнител.
- [ ] Admin overview: `/admin/service-requests` и detail page показват заявки, оферти, selected executor и public offer link.
- [ ] Email delivery: customer confirmation, new offer, accepted offer и not selected emails съдържат правилните links.
- [ ] Mobile check: homepage, listings, business detail, request form, customer offer page, business service requests и admin service requests.
- [ ] Analytics enabled: `GA_MEASUREMENT_ID`, `META_PIXEL_ID` и `CLARITY_PROJECT_ID` са попълнени само когато реално ще се използват.
- [ ] Test request completed: една реална заявка е пусната, има оферта, избран изпълнител и admin вижда статуса.

## Production security checklist

- [ ] `APP_DEBUG=false` е настроено за production.
- [ ] `.env` не е committed и съдържа реалните secrets само на server-а.
- [ ] Login, register, public request forms, review/recommend forms и billing checkout имат rate limiting.
- [ ] Admin routes са достъпни само за authenticated admin потребител.
- [ ] Business dashboard/billing/profile routes са достъпни само за role `business`.
- [ ] Public uploads приемат само JPG, PNG и WEBP с реален image MIME type и лимит на размера.
- [ ] Изпълними файлове, SVG/script payloads и non-image uploads се отхвърлят.
- [ ] User-generated content се показва с escaped Blade output `{{ }}`, без raw `{!! !!}` за клиентски текстове.
- [ ] Website/social redirects приемат само очаквани safe URL схеми.
- [ ] Security headers са налични: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`.
- [ ] Stripe webhook endpoint `/stripe/webhook` има валиден `STRIPE_WEBHOOK_SECRET` и отхвърля неподписани заявки.
- [ ] Contact page е статична; ако бъде добавена POST contact форма, route-ът трябва да получи validation и throttle middleware.
- [ ] Password reset route не е активен; ако бъде добавен, трябва да използва Laravel signed tokens и throttle middleware.

## Legal and trust

- [ ] `/terms` е прегледана.
- [ ] `/privacy` е прегледана.
- [ ] `/cookies` е прегледана.
- [ ] `/contact` съдържа коректен email.
- [ ] Footer линковете към legal страниците работят.
- [ ] Текстовете са прегледани от юрист преди официален launch.

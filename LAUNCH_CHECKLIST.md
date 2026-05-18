# FixNow.bg Launch Checklist

## Local/dev preparation

- [ ] Run `composer install`
- [ ] Run `npm install`
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_URL` to the real local/staging/production URL
- [ ] Run `php artisan key:generate`
- [ ] Configure database connection in `.env`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan migrate:fresh --seed` for local/demo preview
- [ ] Run `php artisan test`
- [ ] Run `npm run build`
- [ ] Run `php artisan route:list`

## Stripe test mode

- [ ] Set `STRIPE_KEY`
- [ ] Set `STRIPE_SECRET`
- [ ] Set `STRIPE_WEBHOOK_SECRET`
- [ ] Set `STRIPE_STANDARD_PRICE_ID`
- [ ] Set `STRIPE_PREMIUM_PRICE_ID`
- [ ] Enable Stripe Customer Portal in Stripe dashboard
- [ ] Configure webhook endpoint: `/stripe/webhook`
- [ ] Test Standard checkout in Stripe test mode
- [ ] Test Premium checkout in Stripe test mode
- [ ] Confirm Standard/Premium activate only after valid webhook
- [ ] Confirm subscription updates/deletes/payment failures sync correctly

## Public trust and legal

- [ ] Review `/terms`
- [ ] Review `/privacy`
- [ ] Review `/cookies`
- [ ] Review `/contact`
- [ ] Review `/how-it-works`
- [ ] Confirm footer links work on mobile and desktop
- [ ] Replace or confirm contact email: `hello@fixnow.bg`
- [ ] Confirm `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- [ ] Have Terms/Privacy/Cookies reviewed by a lawyer before official launch

## Business readiness

- [ ] Add first real business profiles
- [ ] Verify at least one real business through admin dashboard
- [ ] Confirm Standard business limits
- [ ] Confirm Premium badge and ranking boost
- [ ] Confirm expired/cancelled businesses are hidden publicly
- [ ] Confirm business onboarding checklist is clear
- [ ] Confirm business billing page shows current plan and limits
- [ ] Confirm `/business/service-requests` shows only the current business requests
- [ ] Confirm `/business/billing` does not activate plans without Stripe webhook

## User flows

- [ ] Test homepage from a phone
- [ ] Test `/businesses` from a phone
- [ ] Test `/services` from a phone
- [ ] Test `/top-biznesi` from a phone
- [ ] Test `/plans` from a phone
- [ ] Test `/zayavi-oferta` from a phone
- [ ] Test business detail sticky CTA from a phone
- [ ] Submit a request via `/zayavi-oferta`
- [ ] Confirm admin sees the request
- [ ] Confirm auto-assigned business sees only its assigned requests
- [ ] Submit a direct request from a public business profile
- [ ] Confirm the target business sees the direct request in `/business/service-requests`
- [ ] Confirm admin sees all direct requests in `/admin/service-requests`
- [ ] Submit a review and approve it from admin dashboard
- [ ] Confirm analytics/click events are visible in business dashboard
- [ ] Confirm phone click tracking increments business analytics

## Soft launch QA

- [ ] Run `php artisan migrate`
- [ ] Run `php artisan migrate:fresh --seed` in local/demo environment
- [ ] Run `php artisan test`
- [ ] Run `npm run build`
- [ ] Test Stripe checkout in test mode for Standard and Premium
- [ ] Test Stripe webhook activation with Stripe CLI or dashboard test event
- [ ] Test Stripe Customer Portal access from `/business/billing`
- [ ] Create first real businesses
- [ ] Verify at least one real business from admin dashboard
- [ ] Test public request from `/zayavi-oferta`
- [ ] Test direct request from business profile
- [ ] Test business dashboard on mobile
- [ ] Test admin dashboard on mobile
- [ ] Check legal pages on mobile and desktop
- [ ] Check no horizontal scroll on main public pages

## Production notes

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production mail sender
- [ ] Configure queue/session/cache drivers as needed
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Confirm `storage` is linked if using uploads: `php artisan storage:link`
- [ ] Confirm SSL/HTTPS is active

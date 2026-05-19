<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class BillingController extends Controller
{
    public function plans(): View
    {
        return view('plans', [
            'plans' => $this->planDefinitions(),
        ]);
    }

    public function show(Request $request): View
    {
        $business = $request->user();

        if (!$business || $business->role !== 'business') {
            abort(403);
        }

        $business->initializeTrialIfMissing();
        $business->ensureOfferPointsInitialized();
        $business->loadMissing('services');

        $subscriptionStatus = $business->effectiveSubscriptionStatus();
        $endDate = $subscriptionStatus === 'trial'
            ? $business->trial_ends_at
            : $business->subscription_ends_at;
        $hasActiveStripeSubscription = $this->hasActiveStripeSubscription($business);
        $mustManageExistingStripeSubscription = $this->mustManageExistingStripeSubscription($business);

        return view('business.billing', [
            'business' => $business,
            'plans' => $this->planDefinitions(),
            'subscriptionStatus' => $subscriptionStatus,
            'endDate' => $endDate,
            'hasActiveStripeSubscription' => $hasActiveStripeSubscription,
            'mustManageExistingStripeSubscription' => $mustManageExistingStripeSubscription,
            'canStartCheckout' => !($hasActiveStripeSubscription || $mustManageExistingStripeSubscription),
            'canOpenBillingPortal' => filled($business->stripe_customer_id)
                && ($hasActiveStripeSubscription || $mustManageExistingStripeSubscription || $business->hasPaymentIssue()),
            'usage' => [
                'cities' => $business->serviceCityCount(),
                'city_limit' => $business->cityLimit(),
                'included_city_limit' => $business->includedCityLimit(),
                'categories' => $business->serviceCategoryCount(),
                'category_limit' => $business->categoryLimit(),
                'photos' => $business->photoCount(),
                'photo_limit' => $business->photoLimit(),
                'extra_cities' => 0,
                'extra_cities_amount' => 0,
                'offer_points' => $business->offerPointsBalance(),
                'remaining_offers' => $business->remainingOfferCount(),
                'included_offer_points' => $business->includedMonthlyOfferPoints(),
            ],
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $business = $request->user();

        if (!$business || $business->role !== 'business') {
            abort(403);
        }

        $validated = $request->validate([
            'plan' => ['required', 'in:standard,premium'],
        ]);

        if ($this->hasActiveStripeSubscription($business)) {
            return redirect()
                ->route('business.billing')
                ->with('success', 'Вече имате активен абонамент. Можете да го управлявате от Customer Portal.');
        }

        if ($this->mustManageExistingStripeSubscription($business)) {
            return redirect()
                ->route('business.billing')
                ->withErrors([
                    'stripe' => 'Имате съществуващ Stripe абонамент. Моля, управлявайте плащането или промяната на плана през Customer Portal.',
                ]);
        }

        $plan = $validated['plan'];
        $secret = config('services.stripe.secret');
        $priceId = $this->stripePriceIdFor($plan);

        if (!$secret || !$priceId) {
            return redirect()
                ->back()
                ->withErrors([
                    'stripe' => 'Stripe не е конфигуриран. Добавете STRIPE_SECRET и price id за избрания план.',
                ]);
        }

        try {
            $payload = [
                'mode' => 'subscription',
                'success_url' => route('business.billing', [], true).'?stripe=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('business.billing', [], true).'?stripe=cancelled',
                'client_reference_id' => (string) $business->id,
                'line_items[0][price]' => $priceId,
                'line_items[0][quantity]' => 1,
                'metadata[user_id]' => (string) $business->id,
                'metadata[plan]' => $plan,
                'subscription_data[metadata][user_id]' => (string) $business->id,
                'subscription_data[metadata][plan]' => $plan,
            ];

            if ($business->stripe_customer_id) {
                $payload['customer'] = $business->stripe_customer_id;
            } else {
                $payload['customer_email'] = $business->email;
            }

            $session = Http::asForm()
                ->withToken($secret)
                ->post('https://api.stripe.com/v1/checkout/sessions', $payload)
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withErrors([
                    'stripe' => 'Не успяхме да стартираме Stripe Checkout. Опитайте отново след малко.',
                ]);
        }

        if (!is_array($session) || empty($session['url'])) {
            return redirect()
                ->back()
                ->withErrors([
                    'stripe' => 'Stripe не върна валиден checkout URL. Проверете price id настройките.',
                ]);
        }

        return redirect()->away($session['url']);
    }

    public function portal(Request $request): RedirectResponse
    {
        $business = $request->user();

        if (!$business || $business->role !== 'business') {
            abort(403);
        }

        if (!$business->stripe_customer_id) {
            return redirect()
                ->route('business.billing')
                ->withErrors([
                    'stripe' => 'Все още няма Stripe customer за този бизнес. Стартирайте абонамент през Stripe Checkout.',
                ]);
        }

        $secret = config('services.stripe.secret');

        if (!$secret) {
            return redirect()
                ->route('business.billing')
                ->withErrors([
                    'stripe' => 'Stripe не е конфигуриран. Добавете STRIPE_SECRET, за да отворите customer portal.',
                ]);
        }

        try {
            $session = Http::asForm()
                ->withToken($secret)
                ->post('https://api.stripe.com/v1/billing_portal/sessions', [
                    'customer' => $business->stripe_customer_id,
                    'return_url' => route('business.billing', [], true),
                ])
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('business.billing')
                ->withErrors([
                    'stripe' => 'Не успяхме да отворим Stripe Customer Portal. Опитайте отново след малко.',
                ]);
        }

        if (!is_array($session) || empty($session['url'])) {
            return redirect()
                ->route('business.billing')
                ->withErrors([
                    'stripe' => 'Stripe не върна валиден customer portal URL.',
                ]);
        }

        return redirect()->away($session['url']);
    }

    public function upgradePremium(Request $request): RedirectResponse
    {
        $business = $request->user();

        if (!$business || $business->role !== 'business') {
            abort(403);
        }

        return redirect()
            ->route('business.billing')
            ->with('success', 'Premium не е активиран. Използвайте Stripe Checkout бутона, за да стартирате реално плащане.');
    }

    private function stripePriceIdFor(string $plan): ?string
    {
        return match ($plan) {
            'standard' => config('services.stripe.standard_price_id'),
            'premium' => config('services.stripe.premium_price_id'),
            default => null,
        };
    }

    private function hasActiveStripeSubscription(User $business): bool
    {
        return filled($business->stripe_subscription_id)
            && in_array($business->subscription_status, ['active', 'trialing'], true)
            && (
                $business->subscription_ends_at === null
                || $business->subscription_ends_at->greaterThanOrEqualTo(now())
            );
    }

    private function mustManageExistingStripeSubscription(User $business): bool
    {
        return filled($business->stripe_subscription_id)
            && in_array($business->subscription_status, ['past_due', 'unpaid', 'incomplete'], true);
    }

    private function planDefinitions(): array
    {
        return [
            'standard' => [
                'label' => 'Standard',
                'price' => 18.99,
                'city_limit' => 2,
                'category_limit' => 2,
                'photo_limit' => 5,
                'offer_points' => 30,
                'features' => [
                    'Публичен бизнес профил',
                    'До 2 града',
                    'До 2 категории/услуги',
                    'До 5 снимки',
                    'Показване в търсене',
                    'Ревюта',
                    'Директен контакт',
                    '30 точки за оферти месечно',
                ],
            ],
            'premium' => [
                'label' => 'Premium',
                'price' => 24.99,
                'city_limit' => 5,
                'category_limit' => 5,
                'photo_limit' => 15,
                'offer_points' => 90,
                'features' => [
                    'Всичко от Standard',
                    'До 5 града',
                    'До 5 категории/услуги',
                    'До 15 снимки',
                    'Premium/Препоръчан badge',
                    'По-високо позициониране',
                    'Показване в “Препоръчани бизнеси”',
                    'Разширена видимост',
                    '90 точки за оферти месечно',
                ],
            ],
        ];
    }
}

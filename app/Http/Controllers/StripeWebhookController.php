<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        if (!$this->hasValidSignature($request)) {
            return response('Invalid Stripe signature.', 400);
        }

        $event = json_decode($request->getContent(), true);

        if (!is_array($event)) {
            return response('Invalid Stripe payload.', 400);
        }

        $object = Arr::get($event, 'data.object', []);
        $object = is_array($object) ? $object : [];

        match (Arr::get($event, 'type')) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($object),
            'customer.subscription.updated' => $this->syncSubscription($object),
            'customer.subscription.deleted' => $this->syncSubscription($object, 'canceled'),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($object),
            default => null,
        };

        return response('Webhook handled.', 200);
    }

    private function syncSubscription(array $subscription, ?string $forcedStatus = null): void
    {
        $subscriptionId = Arr::get($subscription, 'id');
        $customerId = Arr::get($subscription, 'customer');
        $customerId = is_array($customerId) ? Arr::get($customerId, 'id') : $customerId;
        $business = $this->businessFromStripeIdentifiers($subscriptionId, $customerId);

        if (!$business && ($businessId = Arr::get($subscription, 'metadata.user_id'))) {
            $business = User::query()
                ->whereKey($businessId)
                ->where('role', 'business')
                ->first();
        }

        if (!$business) {
            return;
        }

        $status = $forcedStatus ?: $this->normalizeStripeStatus(Arr::get($subscription, 'status'));
        $plan = $this->planFromStripeObject($subscription);

        $updates = [
            'subscription_status' => $status,
            'stripe_customer_id' => $customerId ?: $business->stripe_customer_id,
            'stripe_subscription_id' => $subscriptionId ?: $business->stripe_subscription_id,
            'subscription_ends_at' => $this->periodEndFromStripeObject($subscription) ?: $business->subscription_ends_at,
        ];

        if ($plan) {
            $updates['subscription_plan'] = $plan;
        }

        if (in_array($status, ['active', 'trialing'], true)) {
            $updates['subscription_started_at'] = $business->subscription_started_at ?: now();
            $updates['cancelled_at'] = null;

            if ($plan && Schema::hasColumn('users', 'offer_points_balance')) {
                $updates['offer_points_balance'] = $plan === 'premium' ? 90 : 30;
                if (Schema::hasColumn('users', 'offer_points_initialized_at')) {
                    $updates['offer_points_initialized_at'] = now();
                }
            }
        }

        if (in_array($status, ['canceled', 'cancelled'], true)) {
            $updates['cancelled_at'] = $business->cancelled_at ?: now();
        }

        $business->forceFill($updates)->save();
    }

    private function handleInvoicePaymentFailed(array $invoice): void
    {
        $business = $this->businessFromStripeIdentifiers(
            Arr::get($invoice, 'subscription'),
            Arr::get($invoice, 'customer')
        );

        if (!$business && ($businessId = Arr::get($invoice, 'subscription_details.metadata.user_id'))) {
            $business = User::query()
                ->whereKey($businessId)
                ->where('role', 'business')
                ->first();
        }

        if (!$business) {
            return;
        }

        $updates = [
            'subscription_status' => 'payment_failed',
            'subscription_ends_at' => $this->periodEndFromStripeObject($invoice) ?: $business->subscription_ends_at,
        ];

        $business->forceFill($updates)->save();
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.stripe.webhook_secret');
        $signatureHeader = (string) $request->header('Stripe-Signature', '');

        if (!$secret || $signatureHeader === '') {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($key === 't') {
                $timestamp = $value;
            }

            if ($key === 'v1' && $value) {
                $signatures[] = $value;
            }
        }

        if (!$timestamp || $signatures === []) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$request->getContent(), $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }

    private function handleCheckoutSessionCompleted(array $session): void
    {
        $plan = Arr::get($session, 'metadata.plan');
        $businessId = Arr::get($session, 'metadata.user_id') ?: Arr::get($session, 'client_reference_id');
        $paymentStatus = Arr::get($session, 'payment_status');

        if (!in_array($plan, ['standard', 'premium'], true) || !$businessId) {
            return;
        }

        if ($paymentStatus !== 'paid') {
            return;
        }

        $business = User::query()
            ->whereKey($businessId)
            ->where('role', 'business')
            ->first();

        if (!$business) {
            return;
        }

        $updates = [
            'subscription_plan' => $plan,
            'subscription_status' => 'active',
            'stripe_customer_id' => Arr::get($session, 'customer') ?: $business->stripe_customer_id,
            'stripe_subscription_id' => Arr::get($session, 'subscription') ?: $business->stripe_subscription_id,
            'subscription_started_at' => now(),
            'subscription_ends_at' => $this->periodEndFromStripeObject($session),
            'cancelled_at' => null,
        ];

        if (Schema::hasColumn('users', 'offer_points_balance')) {
            $updates['offer_points_balance'] = $plan === 'premium' ? 90 : 30;
            if (Schema::hasColumn('users', 'offer_points_initialized_at')) {
                $updates['offer_points_initialized_at'] = now();
            }
        }

        $business->forceFill($updates)->save();
    }

    private function businessFromStripeIdentifiers($subscriptionId, $customerId): ?User
    {
        $subscriptionId = is_array($subscriptionId) ? Arr::get($subscriptionId, 'id') : $subscriptionId;
        $customerId = is_array($customerId) ? Arr::get($customerId, 'id') : $customerId;
        $subscriptionId = is_string($subscriptionId) ? $subscriptionId : null;
        $customerId = is_string($customerId) ? $customerId : null;

        if (!$subscriptionId && !$customerId) {
            return null;
        }

        return User::query()
            ->where('role', 'business')
            ->where(function ($query) use ($subscriptionId, $customerId) {
                if ($subscriptionId) {
                    $query->orWhere('stripe_subscription_id', $subscriptionId);
                }

                if ($customerId) {
                    $query->orWhere('stripe_customer_id', $customerId);
                }
            })
            ->first();
    }

    private function normalizeStripeStatus(?string $status): string
    {
        return match ($status) {
            'active' => 'active',
            'trialing' => 'trialing',
            'past_due' => 'past_due',
            'unpaid' => 'unpaid',
            'canceled', 'cancelled' => 'canceled',
            'incomplete' => 'incomplete',
            'incomplete_expired' => 'incomplete_expired',
            default => $status ?: 'incomplete',
        };
    }

    private function planFromStripeObject(array $object): ?string
    {
        $metadataPlan = Arr::get($object, 'metadata.plan')
            ?: Arr::get($object, 'subscription_details.metadata.plan');

        if (in_array($metadataPlan, ['standard', 'premium'], true)) {
            return $metadataPlan;
        }

        $priceId = Arr::get($object, 'items.data.0.price.id')
            ?: Arr::get($object, 'plan.id')
            ?: Arr::get($object, 'lines.data.0.price.id')
            ?: Arr::get($object, 'lines.data.0.plan.id');

        $standardPriceId = config('services.stripe.standard_price_id');
        $premiumPriceId = config('services.stripe.premium_price_id');

        if ($standardPriceId && $priceId === $standardPriceId) {
            return 'standard';
        }

        if ($premiumPriceId && $priceId === $premiumPriceId) {
            return 'premium';
        }

        return null;
    }

    private function periodEndFromStripeObject(array $object): ?Carbon
    {
        $timestamp = Arr::get($object, 'subscription.current_period_end')
            ?: Arr::get($object, 'current_period_end')
            ?: Arr::get($object, 'items.data.0.current_period_end')
            ?: Arr::get($object, 'lines.data.0.period.end');

        if (!$timestamp || !is_numeric($timestamp)) {
            return null;
        }

        return Carbon::createFromTimestamp((int) $timestamp);
    }
}

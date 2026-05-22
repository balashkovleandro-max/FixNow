<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BillingPlansTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'services.stripe.secret' => 'sk_test_fixnow',
            'services.stripe.webhook_secret' => 'whsec_fixnow',
            'services.stripe.standard_price_id' => 'price_1TYmByRqvGMkwX9rN7HTUunp',
            'services.stripe.premium_price_id' => 'price_1TYmCcRqvGMkwX9rE8ichDo4',
        ]);
    }

    public function test_public_plans_page_loads(): void
    {
        $this->get(route('plans'))
            ->assertOk()
            ->assertSee('Standard')
            ->assertSee('Premium')
            ->assertSee('18,99')
            ->assertSee('24,99');
    }

    public function test_pricing_redirects_to_plans(): void
    {
        $this->get('/pricing')
            ->assertRedirect('/plans');
    }

    public function test_business_user_can_see_billing_page(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->get(route('business.billing'))
            ->assertOk()
            ->assertSee('Standard')
            ->assertSee('Управление на плана');
    }

    public function test_client_cannot_see_business_billing_page(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
        ]);

        $this->actingAs($client)
            ->get(route('business.billing'))
            ->assertForbidden();
    }

    public function test_business_dashboard_shows_billing_card(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dashboard-billing-card', false)
            ->assertSee('Управление на плана');
    }

    public function test_standard_business_sees_upgrade_cta(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->get(route('business.billing'))
            ->assertOk()
            ->assertSee('Ъпгрейд към Premium')
            ->assertSee('Вземи Premium');
    }

    public function test_premium_business_sees_premium_benefits(): void
    {
        $business = $this->business([
            'subscription_plan' => 'premium',
        ]);

        $this->actingAs($business)
            ->get(route('business.billing'))
            ->assertOk()
            ->assertSee('Вашият бизнес има Premium предимство')
            ->assertSee('Приоритет при matching на заявки');
    }

    public function test_upgrade_placeholder_does_not_activate_premium_without_payment(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.upgrade-premium'))
            ->assertRedirect(route('business.billing'))
            ->assertSessionHas('success');

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
    }

    public function test_business_can_start_checkout_for_standard(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'url' => 'https://checkout.stripe.test/standard',
            ]),
        ]);

        $business = $this->business([
            'subscription_plan' => 'premium',
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'standard'])
            ->assertRedirect('https://checkout.stripe.test/standard');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
            && str_contains($request->body(), 'price_1TYmByRqvGMkwX9rN7HTUunp')
            && str_contains($request->body(), 'metadata%5Bplan%5D=standard'));
    }

    public function test_business_can_start_checkout_for_premium(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'url' => 'https://checkout.stripe.test/premium',
            ]),
        ]);

        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertRedirect('https://checkout.stripe.test/premium');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
            && str_contains($request->body(), 'price_1TYmCcRqvGMkwX9rE8ichDo4')
            && str_contains($request->body(), 'metadata%5Bplan%5D=premium'));
    }

    public function test_active_subscribed_business_cannot_start_another_checkout(): void
    {
        Http::fake();

        $business = $this->business([
            'subscription_plan' => 'premium',
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_active_fixnow',
            'stripe_subscription_id' => 'sub_active_fixnow',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'standard'])
            ->assertRedirect(route('business.billing'))
            ->assertSessionHas('success', 'Вече имате активен абонамент. Можете да го управлявате от Customer Portal.');

        Http::assertNothingSent();
    }

    public function test_trialing_subscribed_business_cannot_start_another_checkout(): void
    {
        Http::fake();

        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'trialing',
            'stripe_customer_id' => 'cus_trialing_fixnow',
            'stripe_subscription_id' => 'sub_trialing_fixnow',
            'subscription_ends_at' => now()->addWeeks(2),
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertRedirect(route('business.billing'))
            ->assertSessionHas('success', 'Вече имате активен абонамент. Можете да го управлявате от Customer Portal.');

        Http::assertNothingSent();
    }

    public function test_cancelled_business_can_start_checkout_again(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'url' => 'https://checkout.stripe.test/restart-cancelled',
            ]),
        ]);

        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'canceled',
            'stripe_customer_id' => 'cus_cancelled_fixnow',
            'stripe_subscription_id' => 'sub_cancelled_fixnow',
            'subscription_ends_at' => now()->subDay(),
            'cancelled_at' => now()->subDay(),
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertRedirect('https://checkout.stripe.test/restart-cancelled');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
            && str_contains($request->body(), 'price_1TYmCcRqvGMkwX9rE8ichDo4'));
    }

    public function test_expired_business_can_start_checkout_again(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'url' => 'https://checkout.stripe.test/restart-expired',
            ]),
        ]);

        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'expired',
            'stripe_customer_id' => 'cus_expired_fixnow',
            'stripe_subscription_id' => 'sub_expired_fixnow',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertRedirect('https://checkout.stripe.test/restart-expired');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
            && str_contains($request->body(), 'price_1TYmCcRqvGMkwX9rE8ichDo4'));
    }

    public function test_payment_failed_business_can_start_checkout_again(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'url' => 'https://checkout.stripe.test/restart-payment-failed',
            ]),
        ]);

        $business = $this->business([
            'subscription_plan' => 'premium',
            'subscription_status' => 'payment_failed',
            'stripe_customer_id' => 'cus_payment_failed_fixnow',
            'stripe_subscription_id' => 'sub_payment_failed_fixnow',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'standard'])
            ->assertRedirect('https://checkout.stripe.test/restart-payment-failed');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
            && str_contains($request->body(), 'price_1TYmByRqvGMkwX9rN7HTUunp'));
    }

    public function test_past_due_business_must_use_customer_portal_instead_of_new_checkout(): void
    {
        Http::fake();

        $business = $this->business([
            'subscription_plan' => 'premium',
            'subscription_status' => 'past_due',
            'stripe_customer_id' => 'cus_past_due_checkout_fixnow',
            'stripe_subscription_id' => 'sub_past_due_checkout_fixnow',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'standard'])
            ->assertRedirect(route('business.billing'))
            ->assertSessionHasErrors('stripe');

        Http::assertNothingSent();
    }

    public function test_customer_portal_button_is_available_for_active_stripe_subscription(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_active_portal_fixnow',
            'stripe_subscription_id' => 'sub_active_portal_fixnow',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($business)
            ->get(route('business.billing'))
            ->assertOk()
            ->assertSee('active-stripe-subscription-notice', false)
            ->assertSee('billing-portal-button', false)
            ->assertSee('Управлявай абонамента')
            ->assertDontSee('upgrade-premium-button', false)
            ->assertDontSee('checkout-standard-button', false);
    }

    public function test_checkout_rejects_invalid_plan(): void
    {
        Http::fake();

        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->from(route('business.billing'))
            ->post(route('business.billing.checkout'), ['plan' => 'enterprise'])
            ->assertRedirect(route('business.billing'))
            ->assertSessionHasErrors('plan');

        Http::assertNothingSent();
    }

    public function test_client_cannot_start_business_checkout(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
        ]);

        $this->actingAs($client)
            ->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_for_checkout(): void
    {
        $this->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertRedirect(route('login'));
    }

    public function test_business_user_can_open_customer_portal_with_stripe_customer_id(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/billing_portal/sessions' => Http::response([
                'url' => 'https://billing.stripe.test/session',
            ]),
        ]);

        $business = $this->business([
            'stripe_customer_id' => 'cus_portal_fixnow',
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.portal'))
            ->assertRedirect('https://billing.stripe.test/session');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/billing_portal/sessions'
            && str_contains($request->body(), 'customer=cus_portal_fixnow')
            && str_contains($request->body(), 'return_url='));
    }

    public function test_guest_is_redirected_to_login_for_customer_portal(): void
    {
        $this->post(route('business.billing.portal'))
            ->assertRedirect(route('login'));
    }

    public function test_client_cannot_open_customer_portal(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
        ]);

        $this->actingAs($client)
            ->post(route('business.billing.portal'))
            ->assertForbidden();
    }

    public function test_admin_cannot_open_customer_portal(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('business.billing.portal'))
            ->assertForbidden();
    }

    public function test_customer_portal_requires_stripe_customer_id(): void
    {
        $business = $this->business([
            'stripe_customer_id' => null,
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.portal'))
            ->assertRedirect(route('business.billing'))
            ->assertSessionHasErrors('stripe');
    }

    public function test_webhook_activates_correct_plan(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'stripe_customer_id' => null,
            'stripe_subscription_id' => null,
        ]);

        $periodEnd = now()->addMonth()->timestamp;
        $payload = $this->checkoutCompletedPayload($business, 'premium', $periodEnd);

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('premium', $business->subscription_plan);
        $this->assertSame('active', $business->subscription_status);
        $this->assertSame('cus_fixnow', $business->stripe_customer_id);
        $this->assertSame('sub_fixnow', $business->stripe_subscription_id);
        $this->assertNotNull($business->subscription_ends_at);
        $this->assertNull($business->cancelled_at);
    }

    public function test_subscription_updated_syncs_active_premium(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'stripe_customer_id' => 'cus_lifecycle_fixnow',
            'stripe_subscription_id' => 'sub_lifecycle_fixnow',
        ]);

        $periodEnd = now()->addMonth()->timestamp;
        $payload = $this->subscriptionUpdatedPayload($business, 'active', 'price_1TYmCcRqvGMkwX9rE8ichDo4', $periodEnd);

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('premium', $business->subscription_plan);
        $this->assertSame('active', $business->subscription_status);
        $this->assertTrue($business->isPremium());
        $this->assertNotNull($business->subscription_ends_at);
        $this->assertNull($business->cancelled_at);
    }

    public function test_subscription_updated_syncs_stripe_identifiers_from_metadata(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'trial',
            'stripe_customer_id' => null,
            'stripe_subscription_id' => null,
        ]);

        $periodEnd = now()->addMonth()->timestamp;
        $payload = $this->subscriptionUpdatedPayload(
            $business,
            'active',
            'price_1TYmByRqvGMkwX9rN7HTUunp',
            $periodEnd,
            'sub_synced_fixnow',
            'cus_synced_fixnow'
        );

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
        $this->assertSame('active', $business->subscription_status);
        $this->assertSame('cus_synced_fixnow', $business->stripe_customer_id);
        $this->assertSame('sub_synced_fixnow', $business->stripe_subscription_id);
        $this->assertNotNull($business->subscription_ends_at);
    }

    public function test_subscription_updated_with_past_due_removes_premium_benefits(): void
    {
        $business = $this->business([
            'subscription_plan' => 'premium',
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_past_due_fixnow',
            'stripe_subscription_id' => 'sub_past_due_fixnow',
        ]);

        $payload = $this->subscriptionUpdatedPayload($business, 'past_due', 'price_1TYmCcRqvGMkwX9rE8ichDo4');

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('premium', $business->subscription_plan);
        $this->assertSame('past_due', $business->subscription_status);
        $this->assertFalse($business->isPremium());
        $this->assertTrue($business->hasPaymentIssue());
    }

    public function test_subscription_deleted_removes_premium_benefits(): void
    {
        $business = $this->business([
            'subscription_plan' => 'premium',
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_deleted_fixnow',
            'stripe_subscription_id' => 'sub_deleted_fixnow',
        ]);

        $payload = $this->subscriptionDeletedPayload($business);

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('canceled', $business->subscription_status);
        $this->assertFalse($business->isPremium());
        $this->assertFalse($business->isPubliclyVisible());
        $this->assertNotNull($business->cancelled_at);
    }

    public function test_invoice_payment_failed_removes_premium_benefits(): void
    {
        $business = $this->business([
            'subscription_plan' => 'premium',
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_failed_fixnow',
            'stripe_subscription_id' => 'sub_failed_fixnow',
        ]);

        $payload = $this->invoicePaymentFailedPayload($business);

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('payment_failed', $business->subscription_status);
        $this->assertFalse($business->isPremium());
        $this->assertFalse($business->isPubliclyVisible());
        $this->assertTrue($business->hasPaymentIssue());
    }

    public function test_invoice_payment_failed_does_not_upgrade_to_failed_paid_plan(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_failed_upgrade_fixnow',
            'stripe_subscription_id' => 'sub_failed_upgrade_fixnow',
        ]);

        $payload = $this->invoicePaymentFailedPayload($business);

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
        $this->assertSame('payment_failed', $business->subscription_status);
        $this->assertFalse($business->isPremium());
        $this->assertFalse($business->isPubliclyVisible());
        $this->assertTrue($business->hasPaymentIssue());
    }

    public function test_checkout_completed_with_unpaid_payment_status_does_not_activate_plan(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'stripe_customer_id' => null,
            'stripe_subscription_id' => null,
        ]);

        $payload = $this->checkoutCompletedPayload($business, 'premium', now()->addMonth()->timestamp, 'unpaid');

        $this->postStripeWebhook($payload)
            ->assertOk();

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
        $this->assertSame('trial', $business->subscription_status);
        $this->assertNull($business->stripe_customer_id);
        $this->assertNull($business->stripe_subscription_id);
        $this->assertFalse($business->isPremium());
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $payload = $this->checkoutCompletedPayload($business, 'premium');

        $this->call('POST', '/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => 't='.time().',v1=invalid',
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertStatus(400);

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
    }

    public function test_checkout_does_not_activate_premium_without_webhook(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'url' => 'https://checkout.stripe.test/premium',
            ]),
        ]);

        $business = $this->business([
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->post(route('business.billing.checkout'), ['plan' => 'premium'])
            ->assertRedirect('https://checkout.stripe.test/premium');

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
    }

    public function test_success_return_url_does_not_activate_plan_directly(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'active',
            'stripe_customer_id' => null,
            'stripe_subscription_id' => null,
        ]);

        $this->actingAs($business)
            ->get(route('business.billing', ['stripe' => 'success', 'session_id' => 'cs_test_fixnow']))
            ->assertOk()
            ->assertSee('stripe-return-success', false);

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
        $this->assertSame('active', $business->subscription_status);
        $this->assertNull($business->stripe_customer_id);
        $this->assertNull($business->stripe_subscription_id);
    }

    public function test_cancel_return_url_shows_feedback_without_changing_plan(): void
    {
        $business = $this->business([
            'subscription_plan' => 'standard',
            'subscription_status' => 'active',
        ]);

        $this->actingAs($business)
            ->get(route('business.billing', ['stripe' => 'cancelled']))
            ->assertOk()
            ->assertSee('stripe-return-cancelled', false)
            ->assertSee('Плащането не беше завършено. Абонаментът не е активиран.');

        $business->refresh();

        $this->assertSame('standard', $business->subscription_plan);
        $this->assertSame('active', $business->subscription_status);
    }

    private function checkoutCompletedPayload(
        User $business,
        string $plan,
        ?int $periodEnd = null,
        string $paymentStatus = 'paid'
    ): string
    {
        return json_encode([
            'id' => 'evt_fixnow',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_fixnow',
                    'client_reference_id' => (string) $business->id,
                    'customer' => 'cus_fixnow',
                    'subscription' => 'sub_fixnow',
                    'payment_status' => $paymentStatus,
                    'metadata' => [
                        'user_id' => (string) $business->id,
                        'plan' => $plan,
                    ],
                    'current_period_end' => $periodEnd,
                ],
            ],
        ]);
    }

    private function subscriptionUpdatedPayload(
        User $business,
        string $status,
        string $priceId,
        ?int $periodEnd = null,
        ?string $subscriptionId = null,
        ?string $customerId = null
    ): string
    {
        return json_encode([
            'id' => 'evt_subscription_updated_fixnow',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => $subscriptionId ?: $business->stripe_subscription_id,
                    'customer' => $customerId ?: $business->stripe_customer_id,
                    'status' => $status,
                    'metadata' => [
                        'user_id' => (string) $business->id,
                    ],
                    'current_period_end' => $periodEnd ?: now()->addMonth()->timestamp,
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => $priceId,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function subscriptionDeletedPayload(User $business, ?int $periodEnd = null): string
    {
        return json_encode([
            'id' => 'evt_subscription_deleted_fixnow',
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => $business->stripe_subscription_id,
                    'customer' => $business->stripe_customer_id,
                    'status' => 'canceled',
                    'current_period_end' => $periodEnd ?: now()->addDay()->timestamp,
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => 'price_1TYmCcRqvGMkwX9rE8ichDo4',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function invoicePaymentFailedPayload(User $business, ?int $periodEnd = null): string
    {
        return json_encode([
            'id' => 'evt_invoice_failed_fixnow',
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'in_failed_fixnow',
                    'customer' => $business->stripe_customer_id,
                    'subscription' => $business->stripe_subscription_id,
                    'lines' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => 'price_1TYmCcRqvGMkwX9rE8ichDo4',
                                ],
                                'period' => [
                                    'end' => $periodEnd ?: now()->addMonth()->timestamp,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function postStripeWebhook(string $payload)
    {
        return $this->call('POST', '/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => $this->stripeSignature($payload),
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }

    private function stripeSignature(string $payload): string
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_fixnow');

        return 't='.$timestamp.',v1='.$signature;
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow Billing Test',
            'business_category' => 'Автосервиз',
            'city' => 'София',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'cancelled_at' => null,
            'is_verified' => false,
            'verified_at' => null,
            'service_cities' => ['София'],
            'service_categories' => ['Автосервиз'],
        ], $overrides));
    }
}

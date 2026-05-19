<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\CategoryCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_public_pages_include_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
    }

    public function test_login_route_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
                ->post(route('login.post'), [
                    'email' => 'missing@example.com',
                    'password' => 'wrong-password',
                ])
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
            ->post(route('login.post'), [
                'email' => 'missing@example.com',
                'password' => 'wrong-password',
            ])
            ->assertStatus(429);
    }

    public function test_public_service_request_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.20'])
                ->post(route('request.service.store'), [])
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.20'])
            ->post(route('request.service.store'), [])
            ->assertStatus(429);
    }

    public function test_billing_checkout_route_is_rate_limited(): void
    {
        $business = $this->business();

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($business)
                ->from(route('business.billing'))
                ->post(route('business.billing.checkout'), ['plan' => 'enterprise'])
                ->assertRedirect(route('business.billing'));
        }

        $this->actingAs($business)
            ->from(route('business.billing'))
            ->post(route('business.billing.checkout'), ['plan' => 'enterprise'])
            ->assertStatus(429);
    }

    public function test_business_gallery_rejects_executable_uploads(): void
    {
        Storage::fake('public');

        $business = $this->business();

        $this->actingAs($business)
            ->post(route('business.profile.photos.store'), [
                'photos' => [
                    UploadedFile::fake()->create('shell.php', 4, 'application/x-php'),
                ],
            ])
            ->assertSessionHasErrors('photos.0');

        $this->assertSame(0, $business->businessPhotos()->count());
    }

    public function test_public_service_request_rejects_script_file_uploads(): void
    {
        Storage::fake('public');

        $category = CategoryCatalog::requestBased()->first()['name'] ?? 'Ремонти и строителство';

        $this->post(route('request.service.store'), [
            'name' => 'Security Tester',
            'phone' => '0888123456',
            'email' => 'tester@example.com',
            'city' => 'Плевен',
            'category' => $category,
            'description' => 'Имам нужда от помощ с ремонт и искам оферта.',
            'photos' => [
                UploadedFile::fake()->create('payload.svg', 4, 'image/svg+xml'),
            ],
        ])
            ->assertSessionHasErrors('photos.0');

        Storage::disk('public')->assertMissing('service-requests/payload.svg');
    }

    public function test_admin_routes_require_admin_role(): void
    {
        $business = $this->business();

        $this->get(route('admin.service-requests.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($business)
            ->get(route('admin.service-requests.index'))
            ->assertForbidden();
    }

    public function test_business_only_routes_reject_customer_users(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get(route('business.billing'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('business.profile.edit'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('business.service-requests.index'))
            ->assertForbidden();
    }

    public function test_stripe_webhook_requires_valid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_security_test']);

        $this->postJson(route('stripe.webhook'), [
            'type' => 'checkout.session.completed',
            'data' => ['object' => []],
        ])
            ->assertStatus(400);
    }

    public function test_checkout_does_not_activate_paid_plan_without_webhook(): void
    {
        config([
            'services.stripe.secret' => 'sk_test_security',
            'services.stripe.premium_price_id' => 'price_1TYmCcRqvGMkwX9rE8ichDo4',
        ]);

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

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'Security Test Executor',
            'business_category' => 'Ремонти',
            'city' => 'Плевен',
            'phone' => '0888123456',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'service_cities' => ['Плевен'],
            'service_categories' => ['Ремонти'],
        ], $overrides));
    }
}

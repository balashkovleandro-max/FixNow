<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanBasedLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_active_premium_business_has_premium_badge(): void
    {
        $business = $this->business([
            'business_name' => 'Active Premium Badge Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
        ]);

        $response = $this->get(route('businesses.show', $business));

        $response->assertOk();

        $this->assertStringContainsString('Premium', $this->mainBusinessBadges($response->getContent()));
    }

    public function test_standard_business_does_not_have_premium_badge(): void
    {
        $business = $this->business([
            'business_name' => 'Standard No Premium Badge Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
        ]);

        $response = $this->get(route('businesses.show', $business));

        $response->assertOk();

        $badges = $this->mainBusinessBadges($response->getContent());

        $this->assertStringNotContainsString('Premium', $badges);
        $this->assertStringNotContainsString('Препоръчан', $badges);
    }

    public function test_active_premium_business_is_ranked_before_active_standard_business(): void
    {
        $standard = $this->business([
            'business_name' => 'Standard Plan Ranking Test',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $premium = $this->business([
            'business_name' => 'Premium Plan Ranking Test',
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSeeInOrder([
                $premium->business_name,
                $standard->business_name,
            ]);
    }

    public function test_inactive_premium_plan_does_not_receive_premium_benefits(): void
    {
        $business = $this->business([
            'subscription_status' => 'expired',
            'subscription_plan' => 'premium',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->assertSame('premium', $business->planKey());
        $this->assertFalse($business->hasActiveSubscription());
        $this->assertFalse($business->isPremium());
        $this->assertTrue($business->isStandard());
        $this->assertSame(2, $business->cityLimit());
        $this->assertSame(2, $business->categoryLimit());
        $this->assertSame(5, $business->photoLimit());
        $this->assertNotContains('Препоръчан', $business->publicBadges());
    }

    public function test_trial_premium_plan_does_not_show_premium_badge_before_paid_activation(): void
    {
        $business = $this->business([
            'business_name' => 'Trial Premium Without Payment Business',
            'subscription_status' => 'trial',
            'subscription_plan' => 'premium',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'subscription_started_at' => null,
            'subscription_ends_at' => null,
        ]);

        $response = $this->get(route('businesses.show', $business));

        $response->assertOk();

        $badges = $this->mainBusinessBadges($response->getContent());

        $this->assertStringContainsString('Trial', $badges);
        $this->assertStringNotContainsString('Premium', $badges);
    }

    public function test_active_premium_plan_has_higher_limits(): void
    {
        $business = $this->business([
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
        ]);

        $this->assertTrue($business->hasActiveSubscription());
        $this->assertTrue($business->isPremium());
        $this->assertSame(5, $business->cityLimit());
        $this->assertSame(5, $business->categoryLimit());
        $this->assertSame(15, $business->photoLimit());
    }

    public function test_admin_activation_does_not_change_standard_plan_to_premium(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $business = $this->business([
            'subscription_status' => 'expired',
            'subscription_plan' => 'standard',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.businesses.activate', $business))
            ->assertRedirect();

        $business->refresh();

        $this->assertSame('active', $business->subscription_status);
        $this->assertSame('standard', $business->subscription_plan);
        $this->assertFalse($business->isPremium());
    }

    public function test_standard_business_cannot_save_more_than_two_cities(): void
    {
        $business = $this->business([
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->put(route('business.profile.update'), [
                'business_name' => 'Standard Limited Cities',
                'service_cities' => ['София', 'Пловдив', 'Варна'],
            ])
            ->assertSessionHasErrors('service_cities');
    }

    public function test_standard_business_cannot_publish_service_in_third_city(): void
    {
        $business = $this->business([
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'service_cities' => ['София', 'Пловдив'],
        ]);

        $this->actingAs($business)
            ->post(route('services.store'), [
                'title' => 'Услуга във Варна',
                'category' => 'Автосервиз',
                'city' => 'Варна',
                'description' => 'Тестово описание за услуга извън лимита.',
                'phone' => '0888123456',
            ])
            ->assertSessionHasErrors('city');
    }

    public function test_active_premium_business_can_save_up_to_five_cities(): void
    {
        $business = $this->business([
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
        ]);

        $cities = ['София', 'Пловдив', 'Варна', 'Бургас', 'Плевен'];

        $this->actingAs($business)
            ->put(route('business.profile.update'), [
                'business_name' => 'Premium Five Cities',
                'service_cities' => $cities,
            ])
            ->assertRedirect(route('business.profile.edit'))
            ->assertSessionHasNoErrors();

        $business->refresh();

        $this->assertSame($cities, $business->service_cities);
        $this->assertSame(5, $business->serviceCityCount());
    }

    private function mainBusinessBadges(string $html): string
    {
        if (!preg_match('/<div[^>]*data-testid="main-business-badges"[^>]*>(.*?)<\/div>/s', $html, $matches)) {
            $this->fail('Main business badges section was not found.');
        }

        return $matches[1];
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'BON Plan Test Business',
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

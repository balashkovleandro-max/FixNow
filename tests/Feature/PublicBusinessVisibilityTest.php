<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBusinessVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_active_business_is_visible_publicly(): void
    {
        $business = $this->business([
            'business_name' => 'Active Visible Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
        ]);

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSee('Active Visible Business');

        $this->get('/')
            ->assertOk()
            ->assertSee('Active Visible Business');

        $this->get(route('businesses.show', $business))
            ->assertOk()
            ->assertSee('Active Visible Business');
    }

    public function test_trial_business_is_visible_publicly(): void
    {
        $business = $this->business([
            'business_name' => 'Trial Visible Business',
            'subscription_status' => 'trial',
            'subscription_plan' => 'standard',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
        ]);

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSee('Trial Visible Business');

        $this->get(route('businesses.show', $business))
            ->assertOk()
            ->assertSee('Trial Visible Business');
    }

    public function test_expired_business_is_not_publicly_visible(): void
    {
        $business = $this->business([
            'business_name' => 'Expired Hidden Business',
            'subscription_status' => 'expired',
            'subscription_plan' => 'premium',
            'trial_started_at' => now()->subDays(40),
            'trial_ends_at' => now()->subDays(10),
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->serviceFor($business, 'Expired Hidden Service');

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertDontSee('Expired Hidden Business');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Expired Hidden Business');

        $this->get(route('services.index'))
            ->assertOk()
            ->assertDontSee('Expired Hidden Service');

        $this->get(route('businesses.show', $business))
            ->assertNotFound();
    }

    public function test_cancelled_business_is_not_publicly_visible(): void
    {
        $business = $this->business([
            'business_name' => 'Cancelled Hidden Business',
            'subscription_status' => 'cancelled',
            'subscription_plan' => 'premium',
            'cancelled_at' => now(),
        ]);

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertDontSee('Cancelled Hidden Business');

        $this->get(route('businesses.show', $business))
            ->assertNotFound();
    }

    public function test_premium_business_is_ranked_before_standard_business(): void
    {
        $standard = $this->business([
            'business_name' => 'Standard Ranking Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $premium = $this->business([
            'business_name' => 'Premium Ranking Business',
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

    public function test_verified_business_gets_ranking_advantage_within_same_plan(): void
    {
        $unverified = $this->business([
            'business_name' => 'Unverified Standard Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verified = $this->business([
            'business_name' => 'Verified Standard Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'is_verified' => true,
            'verified_at' => now(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSeeInOrder([
                $verified->business_name,
                $unverified->business_name,
            ]);
    }

    public function test_verified_badge_is_separate_from_premium_badge(): void
    {
        $verifiedStandard = $this->business([
            'business_name' => 'Verified Standard Badge Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $premiumUnverified = $this->business([
            'business_name' => 'Premium Unverified Badge Business',
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
            'is_verified' => false,
            'verified_at' => null,
        ]);

        $verifiedResponse = $this->get(route('businesses.show', $verifiedStandard));
        $verifiedResponse->assertOk();

        $verifiedBadges = $this->mainBusinessBadges($verifiedResponse->getContent());

        $this->assertStringContainsString('Потвърден', $verifiedBadges);
        $this->assertStringNotContainsString('Premium', $verifiedBadges);

        $premiumResponse = $this->get(route('businesses.show', $premiumUnverified));
        $premiumResponse->assertOk();

        $premiumBadges = $this->mainBusinessBadges($premiumResponse->getContent());

        $this->assertStringContainsString('Premium', $premiumBadges);
        $this->assertStringNotContainsString('Потвърден', $premiumBadges);
    }

    public function test_business_detail_similar_businesses_only_show_publicly_visible_profiles(): void
    {
        $current = $this->business([
            'business_name' => 'Current Detail Business',
            'business_category' => 'ВиК услуги',
            'city' => 'София',
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
        ]);

        $this->business([
            'business_name' => 'Similar Active Business',
            'business_category' => 'ВиК услуги',
            'city' => 'София',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
        ]);

        $this->business([
            'business_name' => 'Similar Trial Business',
            'business_category' => 'ВиК услуги',
            'city' => 'София',
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
        ]);

        $this->business([
            'business_name' => 'Similar Expired Business',
            'business_category' => 'ВиК услуги',
            'city' => 'София',
            'subscription_status' => 'expired',
            'subscription_plan' => 'premium',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->business([
            'business_name' => 'Similar Cancelled Business',
            'business_category' => 'ВиК услуги',
            'city' => 'София',
            'subscription_status' => 'cancelled',
            'subscription_plan' => 'premium',
            'cancelled_at' => now(),
        ]);

        $this->get(route('businesses.show', $current))
            ->assertOk()
            ->assertSee('Similar Active Business')
            ->assertSee('Similar Trial Business')
            ->assertDontSee('Similar Expired Business')
            ->assertDontSee('Similar Cancelled Business');
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
            'business_name' => 'BON Test Business',
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
        ], $overrides));
    }

    private function serviceFor(User $business, string $title): Service
    {
        return Service::create([
            'user_id' => $business->id,
            'title' => $title,
            'category' => $business->business_category ?: 'Автосервиз',
            'city' => $business->city ?: 'София',
            'description' => 'Тестова услуга за публична видимост.',
            'phone' => '0888123456',
        ]);
    }
}

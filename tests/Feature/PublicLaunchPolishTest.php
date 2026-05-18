<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLaunchPolishTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_homepage_loads_launch_polish_sections(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('homepage-how-it-works', false)
            ->assertSee('homepage-client-business-benefits', false)
            ->assertSee('homepage-business-acquisition', false)
            ->assertSee('homepage-popular-categories', false);
    }

    public function test_businesses_page_loads_with_empty_state(): void
    {
        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSee('public-businesses-empty-state', false)
            ->assertSee(route('request.service'), false)
            ->assertSee(route('business.landing'), false);
    }

    public function test_services_page_loads_with_empty_state(): void
    {
        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('public-services-empty-state', false)
            ->assertSee(route('request.service'), false)
            ->assertSee(route('business.landing'), false);
    }

    public function test_plans_page_loads(): void
    {
        $this->get(route('plans'))
            ->assertOk()
            ->assertSee('Standard')
            ->assertSee('Premium')
            ->assertSee('18,99')
            ->assertSee('24,99');
    }

    public function test_business_profile_shows_trust_badges_when_applicable(): void
    {
        $business = User::factory()->create([
            'role' => 'business',
            'business_name' => 'Launch Trust Business',
            'business_category' => 'Автосервиз',
            'city' => 'София',
            'phone' => '0888123456',
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
            'is_verified' => true,
            'verified_at' => now(),
            'service_cities' => ['София'],
            'service_categories' => ['Автосервиз'],
        ]);

        $response = $this->get(route('businesses.show', $business));

        $response
            ->assertOk()
            ->assertSee('main-business-badges', false)
            ->assertSee('business-profile-trust-strip', false)
            ->assertSee('Premium');
    }
}

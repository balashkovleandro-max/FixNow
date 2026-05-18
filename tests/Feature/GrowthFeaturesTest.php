<?php

namespace Tests\Feature;

use App\Models\BusinessRecommendation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrowthFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_top_businesses_page_loads_successfully(): void
    {
        $this->get(route('top.businesses'))
            ->assertOk()
            ->assertSee('Топ изпълнители');

        $this->get('/top-businesses')
            ->assertRedirect('/top-biznesi');
    }

    public function test_expired_and_cancelled_businesses_are_hidden_from_top_rankings(): void
    {
        $this->business(['business_name' => 'Visible Growth Business']);
        $expired = $this->business([
            'business_name' => 'Expired Growth Hidden',
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);
        $cancelled = $this->business([
            'business_name' => 'Cancelled Growth Hidden',
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->get(route('top.businesses'))
            ->assertOk()
            ->assertSee('Visible Growth Business')
            ->assertDontSee($expired->business_name)
            ->assertDontSee($cancelled->business_name);
    }

    public function test_premium_verified_rating_and_recommendations_influence_top_order(): void
    {
        $basic = $this->business([
            'business_name' => 'Basic Growth Business',
            'subscription_plan' => 'standard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rated = $this->business([
            'business_name' => 'Rated Recommended Growth Business',
            'subscription_plan' => 'standard',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Review::create([
            'business_id' => $rated->id,
            'reviewer_name' => 'Happy Client',
            'rating' => 5,
            'comment' => 'Excellent work.',
            'status' => Review::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        BusinessRecommendation::create([
            'business_id' => $rated->id,
            'ip_hash' => hash('sha256', 'rated-growth'),
        ]);

        $premiumVerified = $this->business([
            'business_name' => 'Premium Verified Growth Business',
            'subscription_plan' => 'premium',
            'is_verified' => true,
            'verified_at' => now(),
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('top.businesses'))->assertOk();
        $section = $this->sectionByTestId($response->getContent(), 'top-recommended');

        $this->assertStringContainsString($premiumVerified->business_name, $section);
        $this->assertStringContainsString($rated->business_name, $section);
        $this->assertStringContainsString($basic->business_name, $section);
        $this->assertStringContainsString('Потвърден', $section);
        $this->assertStringContainsString('Препоръчан', $section);
        $this->assertStringContainsString('1 препоръки', $section);
        $this->assertStringContainsString('5.0', $section);
        $this->assertOrder($section, [
            $premiumVerified->business_name,
            $rated->business_name,
            $basic->business_name,
        ]);
    }

    public function test_business_owner_sees_share_section_in_dashboard(): void
    {
        $business = $this->business([
            'business_name' => 'Share Dashboard Business',
            'subscription_plan' => 'premium',
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Сподели профила си')
            ->assertSee('business-share-section', false)
            ->assertSee('Ние сме във FixNow.bg');
    }

    public function test_recommend_button_route_creates_recommendation(): void
    {
        $business = $this->business([
            'business_name' => 'Recommendable Growth Business',
        ]);

        $this->post(route('businesses.recommendations.store', $business))
            ->assertRedirect(route('businesses.show', $business));

        $this->assertDatabaseHas('business_recommendations', [
            'business_id' => $business->id,
            'user_id' => null,
        ]);
    }

    public function test_logged_in_user_cannot_recommend_same_business_twice(): void
    {
        $business = $this->business([
            'business_name' => 'One Vote Growth Business',
        ]);

        $client = User::factory()->create([
            'role' => 'client',
        ]);

        $this->actingAs($client)->post(route('businesses.recommendations.store', $business));
        $this->actingAs($client)->post(route('businesses.recommendations.store', $business));

        $this->assertSame(1, BusinessRecommendation::query()
            ->where('business_id', $business->id)
            ->where('user_id', $client->id)
            ->count());
    }

    public function test_homepage_loads_new_live_sections_with_empty_database(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Топ изпълнители')
            ->assertSee('Най-препоръчвани')
            ->assertSee('Проверени изпълнители')
            ->assertSee('Най-нови изпълнители')
            ->assertSee('Последни отзиви')
            ->assertSee('Първите 50 изпълнители получават стартово предимство');
    }

    public function test_business_filters_do_not_break_public_business_index(): void
    {
        $this->business([
            'business_name' => 'Filtered Growth Business',
            'subscription_plan' => 'premium',
            'is_verified' => true,
            'verified_at' => now(),
            'emergency_services' => true,
            'works_24_7' => true,
        ]);

        $this->get(route('businesses.index', [
            'premium' => 1,
            'verified' => 1,
            'emergency' => 1,
            'works_24_7' => 1,
            'rating' => '4plus',
        ]))->assertOk();
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow Growth Test Business',
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

    private function sectionByTestId(string $html, string $testId): string
    {
        if (!preg_match('/<section[^>]*data-testid="' . preg_quote($testId, '/') . '"[^>]*>(.*?)<\/section>/s', $html, $matches)) {
            $this->fail("Section {$testId} was not found.");
        }

        return $matches[1];
    }

    private function assertOrder(string $haystack, array $needles): void
    {
        $lastPosition = -1;

        foreach ($needles as $needle) {
            $position = strpos($haystack, $needle);

            $this->assertNotFalse($position, "Failed asserting that {$needle} exists.");
            $this->assertGreaterThan($lastPosition, $position, "Failed asserting that {$needle} appears in order.");

            $lastPosition = $position;
        }
    }
}

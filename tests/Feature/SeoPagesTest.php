<?php

namespace Tests\Feature;

use App\Models\BusinessRecommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_city_seo_page_loads_successfully(): void
    {
        $this->business([
            'business_name' => 'Pleven City SEO Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
        ]);

        $this->get('/grad/pleven')
            ->assertOk()
            ->assertSee('Изпълнители и услуги в Плевен')
            ->assertSee('Pleven City SEO Business');
    }

    public function test_city_category_seo_page_loads_successfully(): void
    {
        $this->business([
            'business_name' => 'Pleven Auto SEO Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
        ]);

        $this->get('/grad/pleven/avtoservizi')
            ->assertOk()
            ->assertSee('Автосервизи в Плевен')
            ->assertSee('Pleven Auto SEO Business');
    }

    public function test_service_city_seo_page_loads_successfully(): void
    {
        $this->business([
            'business_name' => 'Pleven Electric SEO Business',
            'business_category' => 'Електроуслуги',
            'service_categories' => ['Електроуслуги'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
        ]);

        $this->get('/uslugi/elektrouslugi/pleven')
            ->assertOk()
            ->assertSee('Електроуслуги в Плевен')
            ->assertSee('Pleven Electric SEO Business');
    }

    public function test_expired_and_cancelled_businesses_are_hidden_from_seo_pages(): void
    {
        $this->business([
            'business_name' => 'Visible Trial SEO Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'subscription_started_at' => null,
            'subscription_ends_at' => null,
        ]);

        $this->business([
            'business_name' => 'Expired SEO Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->business([
            'business_name' => 'Cancelled SEO Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->get('/grad/pleven/avtoservizi')
            ->assertOk()
            ->assertSee('Visible Trial SEO Business')
            ->assertDontSee('Expired SEO Business')
            ->assertDontSee('Cancelled SEO Business');
    }

    public function test_premium_verified_businesses_rank_above_standard_on_seo_pages(): void
    {
        $standard = $this->business([
            'business_name' => 'Standard SEO Ranking Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'subscription_plan' => 'standard',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $premiumVerified = $this->business([
            'business_name' => 'Premium Verified SEO Ranking Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'subscription_plan' => 'premium',
            'is_verified' => true,
            'verified_at' => now(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        BusinessRecommendation::query()->create([
            'business_id' => $premiumVerified->id,
            'ip_hash' => hash('sha256', 'premium-verified-seo-ranking-business'),
        ]);

        $this->get('/grad/pleven/avtoservizi')
            ->assertOk()
            ->assertSeeInOrder([
                $premiumVerified->business_name,
                $standard->business_name,
            ])
            ->assertSee('Препоръчан')
            ->assertSee('Потвърден');
    }

    public function test_empty_state_works_when_no_businesses_match(): void
    {
        $this->get('/grad/pleven/avtoservizi')
            ->assertOk()
            ->assertSee('Все още няма активни изпълнители тук');
    }

    public function test_homepage_has_popular_seo_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Популярни градове и услуги')
            ->assertSee('Автосервизи в Плевен')
            ->assertSee('/grad/pleven/avtoservizi', false)
            ->assertSee('/grad/pleven/maistori', false)
            ->assertSee('/grad/pleven', false);
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow SEO Test Business',
            'business_category' => 'Автосервиз',
            'service_categories' => ['Автосервиз'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
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
}

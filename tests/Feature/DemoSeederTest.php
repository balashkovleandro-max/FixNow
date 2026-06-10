<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed();
    }

    public function test_demo_seeder_creates_expected_demo_users(): void
    {
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'business@example.com', 'role' => 'business']);
        $this->assertDatabaseHas('users', ['email' => 'premium@example.com', 'role' => 'business']);
        $this->assertDatabaseHas('users', ['email' => 'client@example.com', 'role' => 'client']);
    }

    public function test_demo_premium_business_has_premium_badge(): void
    {
        $premium = User::query()->where('email', 'premium@example.com')->firstOrFail();

        $this->assertTrue($premium->isPremium());

        $this->get(route('businesses.show', $premium))
            ->assertOk()
            ->assertSee('main-business-badges', false)
            ->assertSee('Premium');
    }

    public function test_demo_standard_business_does_not_get_premium_benefits(): void
    {
        $standard = User::query()->where('email', 'demo.vik@bon.test')->firstOrFail();

        $this->assertTrue($standard->isStandard());
        $this->assertFalse($standard->isPremium());

        $this->get(route('businesses.show', $standard))
            ->assertOk()
            ->assertSee('main-business-badges', false)
            ->assertDontSee('Premium профил');
    }

    public function test_homepage_and_public_listings_load_with_demo_data(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Auto Premium Service')
            ->assertSee('homepage-top-businesses', false);

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSee('Auto Premium Service')
            ->assertSee('VIK Express Pleven')
            ->assertSee('storage/demo/auto-service.svg', false);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('Компютърна диагностика')
            ->assertSee('Отстраняване на теч')
            ->assertSee('storage/demo/plumbing.svg', false);
    }

    public function test_key_public_pages_load_with_demo_content(): void
    {
        $premium = User::query()->where('email', 'premium@example.com')->firstOrFail();

        $this->get(route('top.businesses'))
            ->assertOk()
            ->assertSee('Auto Premium Service');

        $this->get(route('plans'))
            ->assertOk()
            ->assertSee('Standard')
            ->assertSee('Premium');

        $this->get(route('request.service'))
            ->assertOk()
            ->assertSee('Пусни заявка за оферта');

        $this->get(route('businesses.show', $premium))
            ->assertOk()
            ->assertSee('Auto Premium Service')
            ->assertSee('storage/demo/auto-service.svg', false);
    }

    public function test_demo_expired_and_cancelled_businesses_are_hidden_publicly(): void
    {
        $expired = User::query()->where('email', 'demo.expired@bon.test')->firstOrFail();
        $cancelled = User::query()->where('email', 'demo.cancelled@bon.test')->firstOrFail();

        $this->assertFalse($expired->isPubliclyVisible());
        $this->assertFalse($cancelled->isPubliclyVisible());

        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertDontSee('Скрит Expired Demo Бизнес')
            ->assertDontSee('Скрит Cancelled Demo Бизнес');

        $this->get(route('businesses.show', $expired))->assertNotFound();
        $this->get(route('businesses.show', $cancelled))->assertNotFound();
    }

    public function test_demo_seed_creates_services_for_major_categories(): void
    {
        foreach ([
            'Автосервизи',
            'ВиК услуги',
            'Електро услуги',
            'Почистване',
            'Красота и услуги',
            'Ремонти',
        ] as $category) {
            $this->assertTrue(
                Service::query()->where('category', $category)->exists(),
                "Missing demo service category: {$category}"
            );
        }
    }

    public function test_demo_dashboards_load_for_admin_standard_and_premium_businesses(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $standard = User::query()->where('email', 'business@example.com')->firstOrFail();
        $premium = User::query()->where('email', 'premium@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Admin Control MVP')
            ->assertSee('admin-pending-businesses', false);

        $this->actingAs($standard)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('business-onboarding-checklist', false)
            ->assertSee('profile-incomplete-status', false);

        $this->actingAs($premium)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dashboard-billing-card', false)
            ->assertSee('Premium');
    }

    public function test_demo_billing_page_loads_without_starting_checkout(): void
    {
        $premium = User::query()->where('email', 'premium@example.com')->firstOrFail();

        $this->actingAs($premium)
            ->get(route('business.billing'))
            ->assertOk()
            ->assertSee('Вашият бизнес има Premium предимство')
            ->assertSee('billing-portal-button', false);
    }
}

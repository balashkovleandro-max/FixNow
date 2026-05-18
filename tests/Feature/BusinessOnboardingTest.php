<?php

namespace Tests\Feature;

use App\Models\BusinessPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_business_dashboard_shows_onboarding_checklist(): void
    {
        $business = $this->business([
            'business_name' => null,
            'phone' => null,
            'city' => null,
            'business_category' => null,
            'description' => null,
            'short_description' => null,
            'service_cities' => null,
            'service_categories' => null,
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('business-onboarding-checklist', false)
            ->assertSee('onboarding-item-business-name', false)
            ->assertSee('onboarding-fill-profile-cta', false)
            ->assertSee('profile-incomplete-status', false);
    }

    public function test_profile_completeness_is_calculated_from_real_fields(): void
    {
        $business = $this->business([
            'business_name' => 'Complete Profile Business',
            'phone' => '0888123456',
            'city' => 'София',
            'business_category' => 'Автосервиз',
            'description' => 'Професионален сервиз с реално описание за профила.',
            'service_cities' => ['София'],
            'service_categories' => ['Автосервиз'],
        ]);

        $this->assertSame(80, $business->profileCompleteness()['percent']);

        BusinessPhoto::create([
            'business_id' => $business->id,
            'path' => 'business-photos/diagnostics.jpg',
            'original_name' => 'diagnostics.jpg',
            'alt_text' => 'Снимка към профила',
            'sort_order' => 0,
        ]);

        $business->refresh();

        $this->assertSame(100, $business->profileCompleteness()['percent']);
        $this->assertSame([], $business->profileCompleteness()['missing']);
    }

    public function test_incomplete_business_sees_profile_completion_cta(): void
    {
        $business = $this->business([
            'business_name' => 'Incomplete Onboarding Business',
            'phone' => null,
            'description' => null,
            'short_description' => null,
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('profile-incomplete-status', false)
            ->assertSee('onboarding-fill-profile-cta', false);
    }

    public function test_complete_business_sees_complete_status(): void
    {
        $business = $this->business([
            'business_name' => 'Ready Onboarding Business',
            'phone' => '0888123456',
            'city' => 'София',
            'business_category' => 'Автосервиз',
            'description' => 'Пълен бизнес профил за клиенти.',
            'service_cities' => ['София'],
            'service_categories' => ['Автосервиз'],
        ]);

        BusinessPhoto::create([
            'business_id' => $business->id,
            'path' => 'business-photos/repair.jpg',
            'original_name' => 'repair.jpg',
            'alt_text' => 'Снимка към профила',
            'sort_order' => 0,
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('profile-complete-status', false)
            ->assertSee('profile-completeness-percent', false);
    }

    public function test_admin_sees_pending_businesses_section(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $pendingBusiness = $this->business([
            'business_name' => 'Pending Verification Business',
            'is_verified' => false,
            'verified_at' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('admin-pending-businesses', false)
            ->assertSee('pending-business-card', false)
            ->assertSee($pendingBusiness->business_name);
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow Onboarding Test Business',
            'business_category' => 'Автосервиз',
            'city' => 'София',
            'phone' => '0888123456',
            'description' => 'Описание за тестов бизнес профил.',
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

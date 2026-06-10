<?php

namespace Tests\Feature;

use App\Models\BusinessFinancialReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessFinancialInsightsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_business_can_view_financial_insights_page(): void
    {
        $business = $this->business();

        $this->actingAs($business)
            ->get(route('business.insights.index'))
            ->assertOk()
            ->assertSee('Финансов анализ')
            ->assertSee('Месечни финансови данни');
    }

    public function test_customer_cannot_view_financial_insights_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('business.insights.index'))
            ->assertForbidden();
    }

    public function test_business_can_store_financial_report_with_calculations(): void
    {
        $business = $this->business();

        $this->actingAs($business)
            ->post(route('business.insights.store'), $this->payload())
            ->assertRedirect(route('business.insights.index'));

        $report = BusinessFinancialReport::query()->firstOrFail();

        $this->assertSame($business->id, $report->business_id);
        $this->assertSame(5, $report->month);
        $this->assertSame(2026, $report->year);
        $this->assertSame('10000.00', $report->revenue);
        $this->assertSame('7500.00', $report->total_costs);
        $this->assertSame('2500.00', $report->net_profit);
        $this->assertSame('25.00', $report->profit_margin);
        $this->assertGreaterThan(0, $report->health_score);
        $this->assertNotEmpty($report->recommendations);
    }

    public function test_business_report_is_updated_for_same_month_instead_of_duplicated(): void
    {
        $business = $this->business();

        $this->actingAs($business)
            ->post(route('business.insights.store'), $this->payload());

        $this->actingAs($business)
            ->post(route('business.insights.store'), array_merge($this->payload(), [
                'revenue' => 12000,
            ]));

        $this->assertSame(1, BusinessFinancialReport::query()->where('business_id', $business->id)->count());
        $this->assertSame('12000.00', BusinessFinancialReport::query()->firstOrFail()->revenue);
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'name' => 'BON Insight Business',
            'business_name' => 'BON Insight Business',
            'business_category' => 'Ресторанти и кафенета',
            'city' => 'Плевен',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ], $overrides));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'month' => 5,
            'year' => 2026,
            'revenue' => 10000,
            'orders_count' => 100,
            'average_order_value' => '',
            'rent_cost' => 1000,
            'payroll_cost' => 3000,
            'payroll_taxes_cost' => 600,
            'inventory_cost' => 1500,
            'utilities_cost' => 300,
            'marketing_cost' => 200,
            'software_cost' => 100,
            'transport_cost' => 100,
            'other_fixed_costs' => 200,
            'other_variable_costs' => 500,
            'employees_count' => 3,
            'staff_roles' => [
                ['title' => 'Manager', 'monthly_cost' => 1800, 'hours' => 160],
                ['title' => 'Service', 'monthly_cost' => 1200, 'hours' => 160],
            ],
        ];
    }
}

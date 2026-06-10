<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceRequestMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_can_view_service_request_list(): void
    {
        $admin = $this->admin();
        $serviceRequest = $this->serviceRequest([
            'service' => 'Bathroom repair admin list',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.service-requests.index'))
            ->assertOk()
            ->assertSee('Marketplace overview')
            ->assertSee('Bathroom repair admin list')
            ->assertSee('ID #'.$serviceRequest->id)
            ->assertSee('Детайли');
    }

    public function test_admin_can_filter_requests_by_status(): void
    {
        $admin = $this->admin();
        $this->serviceRequest([
            'service' => 'Open request should be hidden',
            'status' => ServiceRequest::STATUS_NEW,
        ]);
        $this->serviceRequest([
            'service' => 'Completed request should be visible',
            'status' => ServiceRequest::STATUS_COMPLETED,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.service-requests.index', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('Completed request should be visible')
            ->assertDontSee('Open request should be hidden');
    }

    public function test_admin_can_view_service_request_detail(): void
    {
        $admin = $this->admin();
        $serviceRequest = $this->serviceRequest([
            'service' => 'Detailed request view',
            'description' => 'Admin needs full request diagnostics.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.service-requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee('Marketplace request')
            ->assertSee('Detailed request view')
            ->assertSee('Admin needs full request diagnostics.')
            ->assertSee('Customer offers URL');
    }

    public function test_admin_sees_offers_and_selected_offer_on_detail_page(): void
    {
        $admin = $this->admin();
        $selectedExecutor = $this->executor([
            'business_name' => 'Selected Admin Executor',
            'email' => 'selected-admin@example.com',
        ]);
        $otherExecutor = $this->executor([
            'business_name' => 'Other Admin Executor',
            'email' => 'other-admin@example.com',
        ]);
        $serviceRequest = $this->serviceRequest([
            'service' => 'Selected offer request',
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $selectedExecutor->id,
        ]);
        $selectedOffer = $this->offer($serviceRequest, $selectedExecutor, [
            'price_estimate' => '1200 lv.',
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
        ]);
        $this->offer($serviceRequest, $otherExecutor, [
            'price_estimate' => '1500 lv.',
            'status' => ServiceRequestOffer::STATUS_NOT_SELECTED,
        ]);
        $serviceRequest->forceFill([
            'selected_offer_id' => $selectedOffer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($admin)
            ->get(route('admin.service-requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee('Selected Admin Executor')
            ->assertSee('Other Admin Executor')
            ->assertSee('1200 lv.')
            ->assertSee('1500 lv.')
            ->assertSee('Избран изпълнител')
            ->assertSee('selected_offer_id');
    }

    public function test_non_admin_cannot_access_admin_service_request_pages(): void
    {
        $serviceRequest = $this->serviceRequest();
        $business = $this->executor();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($business)
            ->get(route('admin.service-requests.index'))
            ->assertForbidden();

        $this->actingAs($business)
            ->get(route('admin.service-requests.show', $serviceRequest))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.service-requests.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.service-requests.show', $serviceRequest))
            ->assertForbidden();
    }

    public function test_admin_can_mark_request_as_completed(): void
    {
        $admin = $this->admin();
        $serviceRequest = $this->serviceRequest();

        $this->actingAs($admin)
            ->from(route('admin.service-requests.show', $serviceRequest))
            ->patch(route('admin.service-requests.completed', $serviceRequest))
            ->assertRedirect(route('admin.service-requests.show', $serviceRequest));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_COMPLETED,
        ]);
    }

    public function test_admin_can_mark_request_as_cancelled(): void
    {
        $admin = $this->admin();
        $serviceRequest = $this->serviceRequest();

        $this->actingAs($admin)
            ->from(route('admin.service-requests.show', $serviceRequest))
            ->patch(route('admin.service-requests.cancelled', $serviceRequest))
            ->assertRedirect(route('admin.service-requests.show', $serviceRequest));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_CANCELLED,
        ]);
    }

    public function test_admin_detail_shows_public_customer_offers_url(): void
    {
        $admin = $this->admin();
        $serviceRequest = $this->serviceRequest();

        $this->actingAs($admin)
            ->get(route('admin.service-requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee($serviceRequest->public_token)
            ->assertSee('/zayavka/'.$serviceRequest->public_token.'/offers');
    }

    public function test_empty_state_renders_when_no_requests_match_filter(): void
    {
        $admin = $this->admin();
        $this->serviceRequest([
            'service' => 'Only open request',
            'status' => ServiceRequest::STATUS_NEW,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.service-requests.index', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('Няма заявки по този филтър')
            ->assertDontSee('Only open request');
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email' => 'admin-'.uniqid().'@example.com',
        ]);
    }

    private function executor(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'name' => 'Executor User',
            'business_name' => 'BON Executor',
            'business_category' => 'Repairs',
            'service_categories' => ['Repairs'],
            'city' => 'Pleven',
            'service_cities' => ['Pleven'],
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'cancelled_at' => null,
            'is_verified' => false,
            'verified_at' => null,
            'offer_points_balance' => 30,
        ], $overrides));
    }

    private function serviceRequest(array $overrides = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'name' => 'Admin Request Client',
            'phone' => '0888123456',
            'email' => 'admin-client@example.com',
            'city' => 'Pleven',
            'category' => 'Repairs',
            'service' => 'Admin request service',
            'description' => 'Admin marketplace request description.',
            'urgency' => ServiceRequest::URGENCY_THIS_WEEK,
            'budget' => 'up to 2000 lv.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ], $overrides));
    }

    private function offer(ServiceRequest $serviceRequest, User $business, array $overrides = []): ServiceRequestOffer
    {
        return ServiceRequestOffer::create(array_merge([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'price_estimate' => 'from 1000 lv.',
            'timeframe' => '10 days',
            'message' => 'We can inspect the job and send a final quote.',
            'phone' => '0888999000',
            'email' => $business->email,
            'status' => ServiceRequestOffer::STATUS_SENT,
            'points_spent' => ServiceRequestOffer::POINTS_COST,
        ], $overrides));
    }
}

<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestMvpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_can_submit_service_request(): void
    {
        $this->post(route('request.service.store'), [
            'name' => 'Lead Client',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Pleven',
            'category' => 'Remonti',
            'service' => 'Boiler repair',
            'description' => 'Need a repair offer for tomorrow.',
            'urgency' => ServiceRequest::URGENCY_URGENT,
            'budget' => 'up to 200 BGN',
        ])->assertRedirect(route('request.service'));

        $this->assertDatabaseHas('service_requests', [
            'name' => 'Lead Client',
            'phone' => '0888123456',
            'city' => 'Pleven',
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_URGENT,
            'budget' => 'up to 200 BGN',
        ]);
    }

    public function test_service_request_validation_requires_core_fields(): void
    {
        $this->post(route('request.service.store'), [])
            ->assertSessionHasErrors(['name', 'phone', 'city', 'description']);
    }

    public function test_admin_sees_service_requests_in_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        ServiceRequest::create([
            'name' => 'Admin Visible Lead',
            'phone' => '0888000000',
            'city' => 'Sofia',
            'category' => 'Cleaning',
            'description' => 'Office cleaning request.',
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_NORMAL,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Admin Visible Lead')
            ->assertSee('0888000000');
    }

    public function test_admin_can_mark_service_request_contacted_and_closed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $serviceRequest = $this->serviceRequest();

        $this->actingAs($admin)
            ->patch(route('admin.service-requests.contacted', $serviceRequest))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_CONTACTED,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.service-requests.closed', $serviceRequest))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_CLOSED,
        ]);
    }

    public function test_business_owner_sees_only_assigned_service_requests(): void
    {
        $firstBusiness = $this->business(['business_name' => 'Assigned Lead Business']);
        $secondBusiness = $this->business(['business_name' => 'Other Lead Business']);

        $ownRequest = ServiceRequest::create([
            'name' => 'Own Assigned Lead',
            'phone' => '0888111111',
            'city' => 'Pleven',
            'category' => 'Electric',
            'description' => 'Assigned to first business.',
            'assigned_business_id' => $firstBusiness->id,
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_NORMAL,
        ]);

        $otherRequest = ServiceRequest::create([
            'name' => 'Other Assigned Lead',
            'phone' => '0888222222',
            'city' => 'Sofia',
            'category' => 'Cleaning',
            'description' => 'Assigned to second business.',
            'assigned_business_id' => $secondBusiness->id,
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_NORMAL,
        ]);

        ServiceRequestAssignment::create([
            'service_request_id' => $ownRequest->id,
            'business_id' => $firstBusiness->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        ServiceRequestAssignment::create([
            'service_request_id' => $otherRequest->id,
            'business_id' => $secondBusiness->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->actingAs($firstBusiness)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Own Assigned Lead')
            ->assertDontSee('Other Assigned Lead');
    }

    public function test_public_cta_pages_load_without_errors(): void
    {
        $business = $this->business([
            'business_name' => 'CTA Test Business',
        ]);

        $this->get(route('request.service'))
            ->assertOk()
            ->assertSee('Заяви оферта');

        $this->get('/request-service')
            ->assertRedirect('/zayavi-oferta');

        $this->get('/')
            ->assertOk();

        $this->get(route('top.businesses'))
            ->assertOk();

        $this->get(route('business.landing'))
            ->assertOk()
            ->assertSee('BON събира реални заявки');

        $this->get(route('businesses.show', $business))
            ->assertOk()
            ->assertSee('Заяви оферта');
    }

    private function serviceRequest(array $overrides = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'name' => 'Action Lead',
            'phone' => '0888123456',
            'city' => 'Pleven',
            'category' => 'Repairs',
            'description' => 'Action test lead.',
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_NORMAL,
        ], $overrides));
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'BON Lead Test Business',
            'business_category' => 'Auto service',
            'city' => 'Sofia',
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

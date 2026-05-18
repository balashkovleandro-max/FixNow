<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestDistributionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_submit_creates_request_and_auto_assigns_matching_businesses(): void
    {
        $active = $this->business([
            'business_name' => 'Active Matching Lead Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
        ]);

        $trial = $this->business([
            'business_name' => 'Trial Matching Lead Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'subscription_started_at' => null,
            'subscription_ends_at' => null,
        ]);

        $this->post(route('request.service.store'), $this->requestPayload())
            ->assertRedirect(route('request.service'));

        $serviceRequest = ServiceRequest::query()->where('name', 'Distribution Client')->firstOrFail();

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $active->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
        ]);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $trial->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
        ]);
    }

    public function test_expired_and_cancelled_businesses_do_not_receive_assignments(): void
    {
        $active = $this->business([
            'business_name' => 'Visible Matching Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
        ]);

        $expired = $this->business([
            'business_name' => 'Expired Matching Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $cancelled = $this->business([
            'business_name' => 'Cancelled Matching Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->post(route('request.service.store'), $this->requestPayload())
            ->assertRedirect(route('request.service'));

        $serviceRequest = ServiceRequest::query()->where('name', 'Distribution Client')->firstOrFail();

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $active->id,
        ]);

        $this->assertDatabaseMissing('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $expired->id,
        ]);

        $this->assertDatabaseMissing('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $cancelled->id,
        ]);
    }

    public function test_premium_verified_business_gets_assignment_priority(): void
    {
        $standard = $this->business([
            'business_name' => 'Standard Assignment Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
            'subscription_plan' => 'standard',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $premiumVerified = $this->business([
            'business_name' => 'Premium Verified Assignment Business',
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
            'subscription_plan' => 'premium',
            'is_verified' => true,
            'verified_at' => now(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->post(route('request.service.store'), $this->requestPayload())
            ->assertRedirect(route('request.service'));

        $serviceRequest = ServiceRequest::query()->where('name', 'Distribution Client')->firstOrFail();
        $firstAssignment = ServiceRequestAssignment::query()
            ->where('service_request_id', $serviceRequest->id)
            ->orderBy('id')
            ->firstOrFail();

        $this->assertSame($premiumVerified->id, $firstAssignment->business_id);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $standard->id,
        ]);
    }

    public function test_business_owner_sees_only_own_assignment_and_can_mark_contacted(): void
    {
        $firstBusiness = $this->business(['business_name' => 'First Assigned Business']);
        $secondBusiness = $this->business(['business_name' => 'Second Assigned Business']);
        $ownRequest = $this->serviceRequest(['name' => 'Own Distribution Lead']);
        $otherRequest = $this->serviceRequest(['name' => 'Other Distribution Lead']);

        $assignment = ServiceRequestAssignment::create([
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
            ->assertSee('Own Distribution Lead')
            ->assertDontSee('Other Distribution Lead');

        $this->actingAs($firstBusiness)
            ->patch(route('service-request-assignments.contacted', $assignment))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('service_request_assignments', [
            'id' => $assignment->id,
            'status' => ServiceRequestAssignment::STATUS_CONTACTED,
        ]);

        $this->assertDatabaseHas('service_requests', [
            'id' => $ownRequest->id,
            'status' => ServiceRequest::STATUS_CONTACTED,
        ]);
    }

    public function test_business_can_decline_assignment(): void
    {
        $business = $this->business();
        $assignment = ServiceRequestAssignment::create([
            'service_request_id' => $this->serviceRequest()->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->actingAs($business)
            ->patch(route('service-request-assignments.declined', $assignment))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('service_request_assignments', [
            'id' => $assignment->id,
            'status' => ServiceRequestAssignment::STATUS_DECLINED,
        ]);
    }

    public function test_admin_dashboard_shows_assignments_and_statuses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $business = $this->business(['business_name' => 'Admin Assignment Business']);
        $serviceRequest = $this->serviceRequest(['name' => 'Admin Assignment Lead']);

        ServiceRequestAssignment::create([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_CONTACTED,
            'sent_at' => now()->subHour(),
            'contacted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Admin Assignment Lead')
            ->assertSee('Admin Assignment Business')
            ->assertSee('contacted');
    }

    public function test_unauthorized_user_cannot_update_foreign_assignment(): void
    {
        $owner = $this->business(['business_name' => 'Owner Business']);
        $otherBusiness = $this->business(['business_name' => 'Other Business']);
        $client = User::factory()->create(['role' => 'client']);
        $assignment = ServiceRequestAssignment::create([
            'service_request_id' => $this->serviceRequest()->id,
            'business_id' => $owner->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->actingAs($otherBusiness)
            ->patch(route('service-request-assignments.contacted', $assignment))
            ->assertForbidden();

        $this->actingAs($client)
            ->patch(route('service-request-assignments.contacted', $assignment))
            ->assertForbidden();

        $this->assertDatabaseHas('service_request_assignments', [
            'id' => $assignment->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
        ]);
    }

    private function requestPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Distribution Client',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Плевен',
            'category' => 'ВиК услуги',
            'service' => 'Ремонт на теч',
            'description' => 'Търся оферта за ремонт на теч.',
            'urgency' => ServiceRequest::URGENCY_NORMAL,
            'budget' => 'до 100 лв.',
        ], $overrides);
    }

    private function serviceRequest(array $overrides = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'name' => 'Distribution Lead',
            'phone' => '0888123456',
            'city' => 'Плевен',
            'category' => 'ВиК услуги',
            'service' => 'ремонт на теч',
            'description' => 'Тестова заявка за разпределяне.',
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_NORMAL,
        ], $overrides));
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow Assignment Test Business',
            'business_category' => 'ВиК услуги',
            'service_categories' => ['ВиК услуги'],
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

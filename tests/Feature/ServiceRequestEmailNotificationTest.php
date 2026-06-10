<?php

namespace Tests\Feature;

use App\Mail\NewServiceRequestAdminMail;
use App\Mail\NewServiceRequestBusinessMail;
use App\Mail\ServiceRequestAssignmentContactedAdminMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use App\Support\ServiceRequestNotificationSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class ServiceRequestEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_receives_email_when_new_service_request_is_submitted(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->business(['business_name' => 'Assigned Email Business']);

        $this->post(route('request.service.store'), $this->requestPayload())
            ->assertRedirect(route('request.service'));

        Mail::assertSent(NewServiceRequestAdminMail::class, function (NewServiceRequestAdminMail $mail) use ($admin) {
            return $mail->hasTo($admin->email)
                && $mail->serviceRequest->name === 'Email Client';
        });
    }

    public function test_assigned_business_receives_email_for_auto_assigned_request(): void
    {
        Mail::fake();

        User::factory()->create(['role' => 'admin']);
        $assignedBusiness = $this->business([
            'business_name' => 'Assigned Email Business',
            'email' => 'assigned-business@example.com',
        ]);

        $this->post(route('request.service.store'), $this->requestPayload())
            ->assertRedirect(route('request.service'));

        Mail::assertSent(NewServiceRequestBusinessMail::class, function (NewServiceRequestBusinessMail $mail) use ($assignedBusiness) {
            return $mail->hasTo($assignedBusiness->email)
                && $mail->business->id === $assignedBusiness->id
                && $mail->serviceRequest->name === 'Email Client';
        });
    }

    public function test_unassigned_business_does_not_receive_email(): void
    {
        Mail::fake();

        User::factory()->create(['role' => 'admin']);
        $assignedBusiness = $this->business([
            'business_name' => 'Assigned Email Business',
            'email' => 'assigned-business@example.com',
        ]);
        $unassignedBusiness = $this->business([
            'business_name' => 'Unassigned Email Business',
            'email' => 'unassigned-business@example.com',
            'city' => 'Varna',
            'service_cities' => ['Varna'],
            'business_category' => 'Cleaning',
            'service_categories' => ['Cleaning'],
        ]);

        $this->post(route('request.service.store'), $this->requestPayload())
            ->assertRedirect(route('request.service'));

        Mail::assertSent(NewServiceRequestBusinessMail::class, fn (NewServiceRequestBusinessMail $mail) => $mail->hasTo($assignedBusiness->email));
        Mail::assertNotSent(NewServiceRequestBusinessMail::class, fn (NewServiceRequestBusinessMail $mail) => $mail->hasTo($unassignedBusiness->email));
    }

    public function test_email_sending_failure_does_not_break_submit_flow(): void
    {
        $this->app->bind(ServiceRequestNotificationSender::class, function () {
            return new class extends ServiceRequestNotificationSender {
                public function newServiceRequest(ServiceRequest $serviceRequest): void
                {
                    throw new RuntimeException('Mail transport failed.');
                }
            };
        });

        $this->business(['business_name' => 'Fallback Email Business']);

        $this->post(route('request.service.store'), $this->requestPayload([
            'name' => 'Mail Failure Client',
        ]))->assertRedirect(route('request.service'));

        $this->assertDatabaseHas('service_requests', [
            'name' => 'Mail Failure Client',
            'status' => ServiceRequest::STATUS_NEW,
        ]);
    }

    public function test_admin_receives_email_when_business_marks_assignment_contacted(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $business = $this->business(['business_name' => 'Contacted Email Business']);
        $serviceRequest = $this->serviceRequest();
        $assignment = ServiceRequestAssignment::create([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->actingAs($business)
            ->patch(route('service-request-assignments.contacted', $assignment))
            ->assertRedirect(route('dashboard'));

        Mail::assertSent(ServiceRequestAssignmentContactedAdminMail::class, function (ServiceRequestAssignmentContactedAdminMail $mail) use ($admin, $assignment) {
            return $mail->hasTo($admin->email)
                && $mail->assignment->id === $assignment->id
                && $mail->assignment->status === ServiceRequestAssignment::STATUS_CONTACTED;
        });
    }

    private function requestPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Email Client',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Pleven',
            'category' => 'Cleaning',
            'service' => 'Apartment cleaning',
            'description' => 'Need a service offer.',
            'urgency' => ServiceRequest::URGENCY_NORMAL,
            'budget' => 'up to 100 BGN',
        ], $overrides);
    }

    private function serviceRequest(array $overrides = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'name' => 'Contacted Email Client',
            'phone' => '0888123456',
            'city' => 'Pleven',
            'category' => 'Cleaning',
            'service' => 'Apartment cleaning',
            'description' => 'Need a service offer.',
            'status' => ServiceRequest::STATUS_NEW,
            'urgency' => ServiceRequest::URGENCY_NORMAL,
        ], $overrides));
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'BON Email Test Business',
            'business_category' => 'Cleaning',
            'service_categories' => ['Cleaning'],
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
        ], $overrides));
    }
}

<?php

namespace Tests\Feature;

use App\Mail\CustomerServiceRequestConfirmationMail;
use App\Mail\NewServiceRequestBusinessMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BusinessProfileServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_can_submit_service_request_to_active_business(): void
    {
        Mail::fake();

        $business = $this->business([
            'business_name' => 'Активен тест бизнес',
            'email' => 'active-business@example.com',
        ]);

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload([
            'customer_name' => 'Иван Клиент',
        ]))
            ->assertRedirect(route('businesses.show', $business))
            ->assertSessionHas('service_request_success');

        $serviceRequest = ServiceRequest::query()->where('name', 'Иван Клиент')->firstOrFail();

        $this->assertSame($business->id, $serviceRequest->assigned_business_id);
        $this->assertSame(ServiceRequest::STATUS_NEW, $serviceRequest->status);
        $this->assertSame(ServiceRequest::SOURCE_BUSINESS_PROFILE, $serviceRequest->source);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
        ]);
    }

    public function test_guest_can_submit_service_request_to_trial_business(): void
    {
        Mail::fake();

        $business = $this->business([
            'business_name' => 'Trial тест бизнес',
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'subscription_started_at' => null,
            'subscription_ends_at' => null,
        ]);

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload())
            ->assertRedirect(route('businesses.show', $business))
            ->assertSessionHas('service_request_success');

        $this->assertDatabaseHas('service_requests', [
            'assigned_business_id' => $business->id,
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_BUSINESS_PROFILE,
        ]);
    }

    public function test_guest_cannot_submit_service_request_to_expired_business(): void
    {
        Mail::fake();

        $business = $this->business([
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload())
            ->assertNotFound();

        $this->assertDatabaseMissing('service_requests', [
            'assigned_business_id' => $business->id,
            'source' => ServiceRequest::SOURCE_BUSINESS_PROFILE,
        ]);
    }

    public function test_guest_cannot_submit_service_request_to_cancelled_business(): void
    {
        Mail::fake();

        $business = $this->business([
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload())
            ->assertNotFound();

        $this->assertDatabaseMissing('service_requests', [
            'assigned_business_id' => $business->id,
            'source' => ServiceRequest::SOURCE_BUSINESS_PROFILE,
        ]);
    }

    public function test_business_can_see_only_own_requests(): void
    {
        $firstBusiness = $this->business(['business_name' => 'Първи бизнес']);
        $secondBusiness = $this->business(['business_name' => 'Втори бизнес']);

        $ownRequest = $this->directRequest($firstBusiness, ['name' => 'Моя клиентска заявка']);
        $otherRequest = $this->directRequest($secondBusiness, ['name' => 'Чужда клиентска заявка']);

        $this->actingAs($firstBusiness)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Моя клиентска заявка')
            ->assertDontSee('Чужда клиентска заявка');

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $ownRequest->id,
            'business_id' => $firstBusiness->id,
        ]);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $otherRequest->id,
            'business_id' => $secondBusiness->id,
        ]);
    }

    public function test_business_cannot_update_another_business_request(): void
    {
        $owner = $this->business(['business_name' => 'Собственик бизнес']);
        $otherBusiness = $this->business(['business_name' => 'Друг бизнес']);
        $serviceRequest = $this->directRequest($owner);

        $this->actingAs($otherBusiness)
            ->patch(route('business.service-requests.contacted', $serviceRequest))
            ->assertForbidden();

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_NEW,
        ]);
    }

    public function test_business_can_mark_request_as_contacted(): void
    {
        $business = $this->business();
        $serviceRequest = $this->directRequest($business);

        $this->actingAs($business)
            ->patch(route('business.service-requests.contacted', $serviceRequest))
            ->assertRedirect(route('business.service-requests.index'));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_CONTACTED,
        ]);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_CONTACTED,
        ]);
    }

    public function test_business_can_mark_request_as_completed(): void
    {
        $business = $this->business();
        $serviceRequest = $this->directRequest($business);

        $this->actingAs($business)
            ->patch(route('business.service-requests.completed', $serviceRequest))
            ->assertRedirect(route('business.service-requests.index'));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_COMPLETED,
        ]);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_COMPLETED,
        ]);
    }

    public function test_business_can_mark_request_as_cancelled(): void
    {
        $business = $this->business();
        $serviceRequest = $this->directRequest($business);

        $this->actingAs($business)
            ->patch(route('business.service-requests.cancelled', $serviceRequest))
            ->assertRedirect(route('business.service-requests.index'));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_CANCELLED,
        ]);

        $this->assertDatabaseHas('service_request_assignments', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_CANCELLED,
        ]);
    }

    public function test_admin_can_see_all_service_requests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstBusiness = $this->business(['business_name' => 'Админ бизнес едно']);
        $secondBusiness = $this->business(['business_name' => 'Админ бизнес две']);

        $this->directRequest($firstBusiness, ['name' => 'Първа admin заявка']);
        $this->directRequest($secondBusiness, ['name' => 'Втора admin заявка']);

        $this->actingAs($admin)
            ->get(route('admin.service-requests.index'))
            ->assertOk()
            ->assertSee('Първа admin заявка')
            ->assertSee('Втора admin заявка')
            ->assertSee('Админ бизнес едно')
            ->assertSee('Админ бизнес две');
    }

    public function test_email_is_sent_to_business_on_new_request(): void
    {
        Mail::fake();

        $business = $this->business([
            'business_name' => 'Email бизнес профил',
            'email' => 'business-request@example.com',
        ]);

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload([
            'customer_name' => 'Email клиент',
        ]))->assertRedirect(route('businesses.show', $business));

        Mail::assertSent(NewServiceRequestBusinessMail::class, function (NewServiceRequestBusinessMail $mail) use ($business) {
            return $mail->hasTo($business->email)
                && $mail->business->id === $business->id
                && $mail->serviceRequest->name === 'Email клиент';
        });
    }

    public function test_customer_confirmation_email_is_sent_only_when_customer_email_is_provided(): void
    {
        Mail::fake();

        $business = $this->business(['email' => 'business-confirmation@example.com']);

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload([
            'customer_name' => 'Клиент с имейл',
            'customer_email' => 'customer-confirmation@example.com',
        ]))->assertRedirect(route('businesses.show', $business));

        Mail::assertSent(CustomerServiceRequestConfirmationMail::class, function (CustomerServiceRequestConfirmationMail $mail) {
            return $mail->hasTo('customer-confirmation@example.com')
                && $mail->serviceRequest->name === 'Клиент с имейл';
        });

        Mail::fake();

        $this->post(route('businesses.service-requests.store', $business), $this->requestPayload([
            'customer_name' => 'Клиент без имейл',
            'customer_email' => null,
        ]))->assertRedirect(route('businesses.show', $business));

        Mail::assertNotSent(CustomerServiceRequestConfirmationMail::class);
    }

    private function requestPayload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Тест Клиент',
            'customer_phone' => '0888123456',
            'customer_email' => 'client@example.com',
            'city' => 'Плевен',
            'message' => 'Имам нужда от оферта за конкретна услуга в следващите дни.',
        ], $overrides);
    }

    private function directRequest(User $business, array $overrides = []): ServiceRequest
    {
        $serviceRequest = ServiceRequest::create(array_merge([
            'name' => 'Клиентска заявка',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Плевен',
            'category' => $business->business_category,
            'service' => 'Заявка от бизнес профил',
            'description' => 'Заявка към конкретен бизнес профил.',
            'urgency' => ServiceRequest::URGENCY_NORMAL,
            'assigned_business_id' => $business->id,
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_BUSINESS_PROFILE,
        ], $overrides));

        ServiceRequestAssignment::create([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'status' => ServiceRequestAssignment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return $serviceRequest;
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'name' => 'BON Business Owner',
            'email' => fake()->unique()->safeEmail(),
            'business_name' => 'BON тест бизнес',
            'business_category' => 'Ремонти',
            'service_categories' => ['Ремонти'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'phone' => '0888123456',
            'description' => 'Професионален тест бизнес профил за заявки.',
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

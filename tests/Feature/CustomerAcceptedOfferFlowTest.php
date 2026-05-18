<?php

namespace Tests\Feature;

use App\Mail\AcceptedOfferExecutorMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerAcceptedOfferFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_customer_can_accept_one_offer_for_own_request(): void
    {
        Mail::fake();

        $customer = User::factory()->create(['role' => 'customer']);
        $selectedExecutor = $this->executor(['email' => 'selected-executor@example.test']);
        $otherExecutor = $this->executor(['email' => 'other-executor@example.test']);
        $serviceRequest = $this->serviceRequest($customer);
        $selectedOffer = $this->offer($serviceRequest, $selectedExecutor, ['price_estimate' => 'от 500 лв.']);
        $otherOffer = $this->offer($serviceRequest, $otherExecutor, ['price_estimate' => 'от 700 лв.']);

        $this->actingAs($customer)
            ->patch(route('customer.offers.accept', $selectedOffer))
            ->assertRedirect(route('dashboard'));

        $serviceRequest->refresh();
        $selectedOffer->refresh();
        $otherOffer->refresh();

        $this->assertSame(ServiceRequest::STATUS_IN_PROGRESS, $serviceRequest->status);
        $this->assertSame($selectedExecutor->id, $serviceRequest->assigned_business_id);
        $this->assertSame($selectedOffer->id, $serviceRequest->selected_offer_id);
        $this->assertSame(ServiceRequestOffer::STATUS_ACCEPTED, $selectedOffer->status);
        $this->assertSame(ServiceRequestOffer::STATUS_NOT_SELECTED, $otherOffer->status);

        Mail::assertSent(AcceptedOfferExecutorMail::class, function (AcceptedOfferExecutorMail $mail) use ($selectedExecutor) {
            return $mail->hasTo($selectedExecutor->email);
        });
    }

    public function test_customer_cannot_accept_offer_for_another_customer_request(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $executor = $this->executor();
        $serviceRequest = $this->serviceRequest($owner);
        $offer = $this->offer($serviceRequest, $executor);

        $this->actingAs($otherCustomer)
            ->patch(route('customer.offers.accept', $offer))
            ->assertForbidden();

        $this->assertDatabaseHas('service_request_offers', [
            'id' => $offer->id,
            'status' => ServiceRequestOffer::STATUS_SENT,
        ]);
    }

    public function test_executor_cannot_accept_offer_instead_of_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $executor = $this->executor();
        $serviceRequest = $this->serviceRequest($customer);
        $offer = $this->offer($serviceRequest, $executor);

        $this->actingAs($executor)
            ->patch(route('customer.offers.accept', $offer))
            ->assertForbidden();
    }

    public function test_selected_executor_sees_accepted_offer_as_active_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $executor = $this->executor(['business_name' => 'Приет Изпълнител']);
        $serviceRequest = $this->serviceRequest($customer, [
            'category' => 'Ремонти и строителство',
            'city' => 'Плевен',
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $executor->id,
        ]);
        $offer = $this->offer($serviceRequest, $executor, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
        ]);
        $serviceRequest->forceFill(['selected_offer_id' => $offer->id])->save();

        $this->actingAs($executor)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Вашата оферта беше приета')
            ->assertSee('Активна поръчка')
            ->assertSee('Offer Customer');
    }

    public function test_second_offer_cannot_be_accepted_after_one_is_selected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $firstExecutor = $this->executor(['email' => 'first-executor@example.test']);
        $secondExecutor = $this->executor(['email' => 'second-executor@example.test']);
        $serviceRequest = $this->serviceRequest($customer);
        $firstOffer = $this->offer($serviceRequest, $firstExecutor);
        $secondOffer = $this->offer($serviceRequest, $secondExecutor);

        $this->actingAs($customer)
            ->patch(route('customer.offers.accept', $firstOffer))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($customer)
            ->patch(route('customer.offers.accept', $secondOffer))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('offer');

        $serviceRequest->refresh();
        $firstOffer->refresh();
        $secondOffer->refresh();

        $this->assertSame($firstOffer->id, $serviceRequest->selected_offer_id);
        $this->assertSame(ServiceRequest::STATUS_IN_PROGRESS, $serviceRequest->status);
        $this->assertSame(ServiceRequestOffer::STATUS_ACCEPTED, $firstOffer->status);
        $this->assertSame(ServiceRequestOffer::STATUS_NOT_SELECTED, $secondOffer->status);
    }

    public function test_customer_dashboard_shows_accepted_offer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $executor = $this->executor(['business_name' => 'Избран Майстор Плевен']);
        $serviceRequest = $this->serviceRequest($customer, [
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $executor->id,
        ]);
        $offer = $this->offer($serviceRequest, $executor, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
            'price_estimate' => 'от 650 лв.',
        ]);

        $serviceRequest->forceFill([
            'selected_offer_id' => $offer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Приета оферта')
            ->assertSee('Избран изпълнител')
            ->assertSee('Избран Майстор Плевен')
            ->assertSee('В процес')
            ->assertSee('от 650 лв.');
    }

    public function test_admin_can_see_selected_offer_in_requests_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $executor = $this->executor(['business_name' => 'Админ Видим Изпълнител']);
        $serviceRequest = $this->serviceRequest($customer, [
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $executor->id,
        ]);
        $offer = $this->offer($serviceRequest, $executor, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
            'price_estimate' => 'от 720 лв.',
        ]);

        $serviceRequest->forceFill([
            'selected_offer_id' => $offer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($admin)
            ->get(route('admin.service-requests.index'))
            ->assertOk()
            ->assertSee('Админ Видим Изпълнител')
            ->assertSee('Приета')
            ->assertSee('от 720 лв.');
    }

    public function test_customer_cannot_access_executor_service_requests_panel(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('business.service-requests.index'))
            ->assertForbidden();
    }

    public function test_executor_cannot_offer_on_non_matching_request(): void
    {
        $executor = $this->executor([
            'service_categories' => ['ВиК услуги'],
            'business_category' => 'ВиК услуги',
            'service_cities' => ['Плевен'],
            'city' => 'Плевен',
        ]);
        $customer = User::factory()->create(['role' => 'customer']);
        $serviceRequest = $this->serviceRequest($customer, [
            'category' => 'Електроуслуги',
            'city' => 'Плевен',
        ]);

        $this->actingAs($executor)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertForbidden();
    }

    public function test_customer_can_register_as_free_customer(): void
    {
        $this->post(route('register.post'), [
            'name' => 'Нов Клиент',
            'email' => 'new-customer@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'new-customer@example.test',
            'role' => 'customer',
        ]);
    }

    private function serviceRequest(User $customer, array $overrides = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'customer_id' => $customer->id,
            'name' => 'Offer Customer',
            'phone' => '0888123456',
            'email' => $customer->email,
            'city' => 'Плевен',
            'category' => 'Ремонти и строителство',
            'service' => 'Ремонт',
            'description' => 'Търся изпълнител за ремонт.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ], $overrides));
    }

    private function offer(ServiceRequest $serviceRequest, User $executor, array $overrides = []): ServiceRequestOffer
    {
        return ServiceRequestOffer::create(array_merge([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $executor->id,
            'price_estimate' => 'от 600 лв.',
            'timeframe' => 'до 5 дни',
            'message' => 'Можем да започнем след оглед.',
            'phone' => '0888999000',
            'email' => $executor->email,
            'status' => ServiceRequestOffer::STATUS_SENT,
            'points_spent' => ServiceRequestOffer::POINTS_COST,
        ], $overrides));
    }

    private function executor(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow Изпълнител',
            'business_category' => 'Ремонти и строителство',
            'service_categories' => ['Ремонти и строителство'],
            'city' => 'Плевен',
            'service_cities' => ['Плевен'],
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
            'offer_points_balance' => 30,
        ], $overrides));
    }

    private function offerPayload(array $overrides = []): array
    {
        return array_merge([
            'price_estimate' => 'от 500 лв.',
            'timeframe' => 'до 3 дни',
            'message' => 'Можем да изпълним заявката след кратък разговор.',
            'phone' => '0888999000',
            'email' => 'executor@example.test',
        ], $overrides);
    }
}

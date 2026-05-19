<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestOffersAndPointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_request_based_category_accepts_public_requests(): void
    {
        $this->post(route('request.service.store'), $this->requestPayload([
            'category' => 'Ремонти и строителство',
        ]))
            ->assertRedirect(route('request.service'))
            ->assertSessionHas('offers_url');

        $this->assertDatabaseHas('service_requests', [
            'name' => 'Offer Client',
            'category' => 'Ремонти и строителство',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);
    }

    public function test_directory_based_category_is_not_accepted_in_offer_request_form(): void
    {
        $this->post(route('request.service.store'), $this->requestPayload([
            'category' => 'Автосервизи',
        ]))->assertSessionHasErrors('category');
    }

    public function test_active_business_sees_relevant_open_request(): void
    {
        $business = $this->business();
        ServiceRequest::create($this->serviceRequestPayload());

        $this->actingAs($business)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Нови заявки за оферти')
            ->assertSee('Изпрати оферта')
            ->assertSee('data-track="offer_submit"', false);
    }

    public function test_expired_business_does_not_see_offer_flow(): void
    {
        $business = $this->business([
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);

        ServiceRequest::create($this->serviceRequestPayload());

        $this->actingAs($business)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertDontSee('Изпрати оферта');
    }

    public function test_business_cannot_send_offer_without_enough_points(): void
    {
        $business = $this->business(['offer_points_balance' => 2]);
        $serviceRequest = ServiceRequest::create($this->serviceRequestPayload());

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertSessionHasErrors('offer');

        $this->assertDatabaseMissing('service_request_offers', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
        ]);
    }

    public function test_sending_offer_spends_three_points(): void
    {
        $business = $this->business(['offer_points_balance' => 30]);
        $serviceRequest = ServiceRequest::create($this->serviceRequestPayload());

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertRedirect(route('business.service-requests.index'));

        $this->assertDatabaseHas('service_request_offers', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'points_spent' => 3,
            'status' => ServiceRequestOffer::STATUS_SENT,
        ]);

        $this->assertSame(27, $business->fresh()->offer_points_balance);
    }

    public function test_business_cannot_send_duplicate_offer_to_same_request(): void
    {
        $business = $this->business(['offer_points_balance' => 30]);
        $serviceRequest = ServiceRequest::create($this->serviceRequestPayload());

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertRedirect(route('business.service-requests.index'));

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload([
                'price_estimate' => '150 лв.',
            ]))
            ->assertSessionHasErrors('offer');

        $this->assertSame(1, ServiceRequestOffer::where('service_request_id', $serviceRequest->id)->count());
    }

    public function test_offer_points_by_plan_helpers(): void
    {
        $trial = $this->business([
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(20),
            'subscription_started_at' => null,
            'subscription_ends_at' => null,
        ]);
        $standard = $this->business(['subscription_plan' => 'standard']);
        $premium = $this->business(['subscription_plan' => 'premium']);

        $this->assertSame(45, $trial->includedMonthlyOfferPoints());
        $this->assertSame(30, $standard->includedMonthlyOfferPoints());
        $this->assertSame(90, $premium->includedMonthlyOfferPoints());
    }

    private function requestPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Offer Client',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Плевен',
            'category' => 'Ремонти и строителство',
            'service' => 'Ремонт на баня',
            'description' => 'Търся оферта за ремонт на баня и смяна на плочки.',
            'urgency' => ServiceRequest::URGENCY_THIS_WEEK,
            'budget' => 'до 2000 лв.',
        ], $overrides);
    }

    private function serviceRequestPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Offer Client',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Плевен',
            'category' => 'Ремонти и строителство',
            'service' => 'Ремонт на баня',
            'description' => 'Търся оферта за ремонт на баня и смяна на плочки.',
            'urgency' => ServiceRequest::URGENCY_THIS_WEEK,
            'budget' => 'до 2000 лв.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ], $overrides);
    }

    private function offerPayload(array $overrides = []): array
    {
        return array_merge([
            'price_estimate' => 'от 1200 лв.',
            'timeframe' => 'до 10 дни',
            'message' => 'Можем да направим оглед и да дадем точна оферта след разговор.',
            'phone' => '0888999000',
            'email' => 'business@example.com',
        ], $overrides);
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'FixNow Offers Business',
            'business_category' => 'Ремонти и строителство',
            'service_categories' => ['Ремонти и строителство'],
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
            'offer_points_balance' => 30,
        ], $overrides));
    }
}

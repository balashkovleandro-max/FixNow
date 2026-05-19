<?php

namespace Tests\Feature;

use App\Mail\NewServiceRequestOfferCustomerMail;
use App\Mail\ServiceRequestOfferAcceptedBusinessMail;
use App\Mail\ServiceRequestOfferNotSelectedBusinessMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use App\Support\CategoryCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ServiceRequestPublicOffersTest extends TestCase
{
    use RefreshDatabase;

    private string $requestCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->requestCategory = CategoryCatalog::requestBased()->first()['name'] ?? 'Ремонти и строителство';
    }

    public function test_customer_can_view_request_offers_with_valid_public_token(): void
    {
        $serviceRequest = $this->serviceRequest();
        $offer = $this->offer($serviceRequest, $this->business([
            'business_name' => 'FixNow Executor',
        ]));

        $this->get(route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token]))
            ->assertOk()
            ->assertSee('Получени оферти')
            ->assertSee('FixNow Executor')
            ->assertSee($offer->price_estimate)
            ->assertSee('Избери изпълнител')
            ->assertSee('data-track="offer_accept"', false);
    }

    public function test_invalid_or_missing_token_does_not_expose_request(): void
    {
        $serviceRequest = $this->serviceRequest();

        $this->get('/zayavka/not-a-valid-token/offers')
            ->assertNotFound()
            ->assertDontSee($serviceRequest->description);

        $this->get('/zayavka/offers')
            ->assertNotFound();
    }

    public function test_customer_sees_empty_state_when_no_offers_exist(): void
    {
        $serviceRequest = $this->serviceRequest();

        $this->get(route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token]))
            ->assertOk()
            ->assertSee('Все още няма получени оферти');
    }

    public function test_customer_receives_email_when_business_sends_offer(): void
    {
        Mail::fake();

        $business = $this->business(['offer_points_balance' => 30]);
        $serviceRequest = $this->serviceRequest([
            'email' => 'customer-offers@example.com',
        ]);

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertRedirect(route('business.service-requests.index'));

        Mail::assertSent(NewServiceRequestOfferCustomerMail::class, function (NewServiceRequestOfferCustomerMail $mail) {
            return $mail->hasTo('customer-offers@example.com')
                && str_contains($mail->offersUrl, '/zayavka/')
                && str_contains($mail->render(), $mail->offersUrl);
        });
    }

    public function test_no_customer_email_is_sent_if_request_is_already_accepted_or_closed(): void
    {
        Mail::fake();

        $business = $this->business(['offer_points_balance' => 30]);
        $serviceRequest = $this->serviceRequest([
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'email' => 'customer-closed@example.com',
        ]);

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertForbidden();

        Mail::assertNotSent(NewServiceRequestOfferCustomerMail::class);
    }

    public function test_missing_customer_email_does_not_crash_offer_flow(): void
    {
        Mail::fake();

        $business = $this->business(['offer_points_balance' => 30]);
        $serviceRequest = $this->serviceRequest([
            'customer_id' => null,
            'email' => null,
        ]);

        $this->actingAs($business)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertRedirect(route('business.service-requests.index'));

        Mail::assertNotSent(NewServiceRequestOfferCustomerMail::class);
        $this->assertDatabaseHas('service_request_offers', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
        ]);
    }

    public function test_customer_can_accept_an_offer_from_public_token_page(): void
    {
        Mail::fake();

        $serviceRequest = $this->serviceRequest();
        $acceptedOffer = $this->offer($serviceRequest, $this->business([
            'business_name' => 'Chosen Executor',
            'email' => 'chosen@example.com',
        ]));
        $otherOffer = $this->offer($serviceRequest, $this->business([
            'business_name' => 'Other Executor',
            'email' => 'other@example.com',
        ]));

        $this->post(route('service-requests.offers.accept', [
            'serviceRequest' => $serviceRequest->public_token,
            'offer' => $acceptedOffer,
        ]))
            ->assertRedirect(route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token]))
            ->assertSessionHas('success', 'Изпълнителят е избран успешно. Можете да се свържете директно с него.');

        $serviceRequest->refresh();
        $acceptedOffer->refresh();
        $otherOffer->refresh();

        $this->assertSame(ServiceRequest::STATUS_IN_PROGRESS, $serviceRequest->status);
        $this->assertSame($acceptedOffer->id, $serviceRequest->selected_offer_id);
        $this->assertSame($acceptedOffer->business_id, $serviceRequest->assigned_business_id);
        $this->assertSame(ServiceRequestOffer::STATUS_ACCEPTED, $acceptedOffer->status);
        $this->assertSame(ServiceRequestOffer::STATUS_NOT_SELECTED, $otherOffer->status);
        $this->assertNotNull($serviceRequest->accepted_offer_at);

        Mail::assertSent(ServiceRequestOfferAcceptedBusinessMail::class, function (ServiceRequestOfferAcceptedBusinessMail $mail) {
            return $mail->hasTo('chosen@example.com');
        });
        Mail::assertSent(ServiceRequestOfferNotSelectedBusinessMail::class, function (ServiceRequestOfferNotSelectedBusinessMail $mail) {
            return $mail->hasTo('other@example.com');
        });
    }

    public function test_accepting_one_offer_prevents_accepting_another_offer(): void
    {
        Mail::fake();

        $serviceRequest = $this->serviceRequest();
        $firstOffer = $this->offer($serviceRequest, $this->business());
        $secondOffer = $this->offer($serviceRequest, $this->business([
            'email' => 'second@example.com',
        ]));

        $this->post(route('service-requests.offers.accept', [
            'serviceRequest' => $serviceRequest->public_token,
            'offer' => $firstOffer,
        ]))->assertRedirect(route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token]));

        $this->post(route('service-requests.offers.accept', [
            'serviceRequest' => $serviceRequest->public_token,
            'offer' => $secondOffer,
        ]))->assertSessionHasErrors('offer');

        $this->assertSame($firstOffer->id, $serviceRequest->fresh()->selected_offer_id);
        $this->assertSame(ServiceRequestOffer::STATUS_NOT_SELECTED, $secondOffer->fresh()->status);
    }

    public function test_accepted_offer_is_visible_in_business_service_requests_page(): void
    {
        $business = $this->business();
        $serviceRequest = $this->serviceRequest([
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $business->id,
        ]);
        $offer = $this->offer($serviceRequest, $business, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
        ]);

        $serviceRequest->forceFill([
            'selected_offer_id' => $offer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($business)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Избран изпълнител')
            ->assertSee('Вашата оферта беше приета');
    }

    public function test_business_dashboard_shows_selected_instruction_for_accepted_business(): void
    {
        $business = $this->business(['email' => 'accepted-dashboard@example.com']);
        $serviceRequest = $this->serviceRequest([
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $business->id,
        ]);
        $offer = $this->offer($serviceRequest, $business, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
        ]);

        $serviceRequest->forceFill([
            'selected_offer_id' => $offer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($business)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Клиентът избра вас');
    }

    public function test_business_dashboard_shows_closed_instruction_for_not_selected_business(): void
    {
        $selectedBusiness = $this->business(['email' => 'selected-dashboard@example.com']);
        $notSelectedBusiness = $this->business(['email' => 'not-selected-dashboard@example.com']);
        $serviceRequest = $this->serviceRequest([
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $selectedBusiness->id,
        ]);
        $acceptedOffer = $this->offer($serviceRequest, $selectedBusiness, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
        ]);
        $this->offer($serviceRequest, $notSelectedBusiness, [
            'status' => ServiceRequestOffer::STATUS_NOT_SELECTED,
        ]);

        $serviceRequest->forceFill([
            'selected_offer_id' => $acceptedOffer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($notSelectedBusiness)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Заявката е затворена. Клиентът избра друг изпълнител.');
    }

    public function test_business_cannot_send_new_offer_after_request_is_accepted(): void
    {
        $selectedBusiness = $this->business([
            'email' => 'selected@example.com',
        ]);
        $otherBusiness = $this->business([
            'email' => 'other-business@example.com',
            'offer_points_balance' => 30,
        ]);
        $serviceRequest = $this->serviceRequest([
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
            'assigned_business_id' => $selectedBusiness->id,
        ]);
        $acceptedOffer = $this->offer($serviceRequest, $selectedBusiness, [
            'status' => ServiceRequestOffer::STATUS_ACCEPTED,
        ]);

        $serviceRequest->forceFill([
            'selected_offer_id' => $acceptedOffer->id,
            'accepted_offer_at' => now(),
        ])->save();

        $this->actingAs($otherBusiness)
            ->post(route('business.service-requests.offers.store', $serviceRequest), $this->offerPayload())
            ->assertForbidden();

        $this->assertDatabaseMissing('service_request_offers', [
            'service_request_id' => $serviceRequest->id,
            'business_id' => $otherBusiness->id,
        ]);
    }

    private function serviceRequest(array $overrides = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'name' => 'Request Client',
            'phone' => '0888123456',
            'email' => 'client@example.com',
            'city' => 'Pleven',
            'category' => $this->requestCategory,
            'service' => 'Ремонт на баня',
            'description' => 'Търся изпълнител за ремонт на баня и подмяна на плочки.',
            'urgency' => ServiceRequest::URGENCY_THIS_WEEK,
            'budget' => 'до 2000 лв.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ], $overrides));
    }

    private function offer(ServiceRequest $serviceRequest, User $business, array $overrides = []): ServiceRequestOffer
    {
        return ServiceRequestOffer::create(array_merge([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'price_estimate' => 'от 1200 лв.',
            'timeframe' => 'до 10 дни',
            'message' => 'Можем да направим оглед и да дадем точна оферта след разговор.',
            'phone' => '0888999000',
            'email' => $business->email,
            'status' => ServiceRequestOffer::STATUS_SENT,
            'points_spent' => ServiceRequestOffer::POINTS_COST,
        ], $overrides));
    }

    private function offerPayload(array $overrides = []): array
    {
        return array_merge([
            'price_estimate' => 'от 1400 лв.',
            'timeframe' => 'до 12 дни',
            'message' => 'Можем да помогнем с оглед, материали и изпълнение в удобен срок.',
            'phone' => '0888777666',
            'email' => 'offer@example.com',
        ], $overrides);
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'name' => 'Executor User',
            'business_name' => 'FixNow Executor',
            'business_category' => $this->requestCategory,
            'service_categories' => [$this->requestCategory],
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
}

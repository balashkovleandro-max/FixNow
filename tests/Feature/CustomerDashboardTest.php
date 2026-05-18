<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_customer_can_see_own_requests(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@example.test',
        ]);
        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'email' => 'other-customer@example.test',
        ]);

        ServiceRequest::create([
            'customer_id' => $customer->id,
            'name' => 'Иван Клиент',
            'phone' => '0888000001',
            'email' => $customer->email,
            'city' => 'Плевен',
            'category' => 'Ремонт на баня',
            'description' => 'Искам оферта за ремонт на баня.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        ServiceRequest::create([
            'customer_id' => $otherCustomer->id,
            'name' => 'Друг Клиент',
            'phone' => '0888000002',
            'email' => $otherCustomer->email,
            'city' => 'София',
            'category' => 'Чужда заявка',
            'description' => 'Това не трябва да се вижда.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Клиентски панел')
            ->assertSee('Моите заявки')
            ->assertSee('Ремонт на баня')
            ->assertSee('Плевен')
            ->assertDontSee('Чужда заявка');
    }

    public function test_customer_can_see_offers_for_own_request(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-offers@example.test',
        ]);
        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'email' => 'other-offers@example.test',
        ]);
        $business = User::factory()->create([
            'role' => 'business',
            'business_name' => 'Fix Майстор Плевен',
        ]);

        $ownRequest = ServiceRequest::create([
            'customer_id' => $customer->id,
            'name' => 'Иван Клиент',
            'phone' => '0888000001',
            'email' => $customer->email,
            'city' => 'Плевен',
            'category' => 'ВиК услуги',
            'description' => 'Теч в банята и нужда от оглед.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        $otherRequest = ServiceRequest::create([
            'customer_id' => $otherCustomer->id,
            'name' => 'Друг Клиент',
            'phone' => '0888000002',
            'email' => $otherCustomer->email,
            'city' => 'Варна',
            'category' => 'Скрита чужда заявка',
            'description' => 'Тази оферта не трябва да се вижда.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        ServiceRequestOffer::create([
            'service_request_id' => $ownRequest->id,
            'business_id' => $business->id,
            'price_estimate' => 'от 120 лв.',
            'timeframe' => 'до 2 дни',
            'message' => 'Можем да направим оглед още тази седмица.',
            'phone' => '0888111222',
            'status' => ServiceRequestOffer::STATUS_SENT,
        ]);

        ServiceRequestOffer::create([
            'service_request_id' => $otherRequest->id,
            'business_id' => $business->id,
            'price_estimate' => 'от 999 лв.',
            'timeframe' => 'до 10 дни',
            'message' => 'Скрита чужда оферта.',
            'phone' => '0888333444',
            'status' => ServiceRequestOffer::STATUS_SENT,
        ]);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Получени оферти')
            ->assertSee('Fix Майстор Плевен')
            ->assertSee('от 120 лв.')
            ->assertSee('Можем да направим оглед още тази седмица.')
            ->assertDontSee('Скрита чужда оферта');
    }

    public function test_legacy_client_role_can_see_own_requests(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'legacy-client@example.test',
        ]);

        ServiceRequest::create([
            'customer_id' => $client->id,
            'name' => 'Legacy Client',
            'phone' => '0888000003',
            'email' => $client->email,
            'city' => 'Пловдив',
            'category' => 'Почистване',
            'description' => 'Почистване след ремонт.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        $this->actingAs($client)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Почистване')
            ->assertSee('Пловдив');
    }

    public function test_customer_cannot_access_business_billing_features(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get(route('business.billing'))
            ->assertForbidden();
    }
}

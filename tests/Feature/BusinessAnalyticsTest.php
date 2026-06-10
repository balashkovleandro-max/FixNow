<?php

namespace Tests\Feature;

use App\Models\BusinessAnalyticsEvent;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_profile_view_is_recorded_for_active_business(): void
    {
        $business = $this->business([
            'business_name' => 'Analytics Active Business',
        ]);

        $this->get(route('businesses.show', $business))
            ->assertOk();

        $this->assertDatabaseHas('business_analytics_events', [
            'business_id' => $business->id,
            'event_type' => BusinessAnalyticsEvent::PROFILE_VIEW,
        ]);

        $event = BusinessAnalyticsEvent::query()
            ->where('business_id', $business->id)
            ->where('event_type', BusinessAnalyticsEvent::PROFILE_VIEW)
            ->firstOrFail();

        $this->assertSame('business_profile', data_get($event->metadata, 'source'));
    }

    public function test_expired_and_cancelled_public_pages_do_not_record_profile_view(): void
    {
        $expired = $this->business([
            'business_name' => 'Analytics Expired Business',
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $cancelled = $this->business([
            'business_name' => 'Analytics Cancelled Business',
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        foreach ([$expired, $cancelled] as $business) {
            $this->get(route('businesses.show', $business))
                ->assertNotFound();
        }

        $this->assertDatabaseMissing('business_analytics_events', [
            'event_type' => BusinessAnalyticsEvent::PROFILE_VIEW,
        ]);
    }

    public function test_click_routes_record_expected_event_types(): void
    {
        $business = $this->business([
            'phone' => '0888123456',
            'website' => 'example.com',
            'facebook' => 'https://facebook.com/bon-test',
        ]);

        $this->get(route('businesses.track.phone', $business))
            ->assertRedirect('tel:0888123456');

        $this->get(route('businesses.track.inquiry', $business))
            ->assertRedirect(route('businesses.show', [
                'user' => $business,
                'analytics_intent' => 'inquiry',
            ]) . '#send-request');

        $this->get(route('businesses.track.chat', $business))
            ->assertRedirect(route('businesses.show', [
                'user' => $business,
                'analytics_intent' => 'chat',
            ]) . '#contact');

        $this->get(route('businesses.track.website', $business))
            ->assertRedirect('https://example.com');

        $this->get(route('businesses.track.social', [$business, 'facebook']))
            ->assertRedirect('https://facebook.com/bon-test');

        foreach ([
            BusinessAnalyticsEvent::PHONE_CLICK,
            BusinessAnalyticsEvent::INQUIRY_CLICK,
            BusinessAnalyticsEvent::CHAT_CLICK,
            BusinessAnalyticsEvent::WEBSITE_CLICK,
            BusinessAnalyticsEvent::SOCIAL_CLICK,
        ] as $eventType) {
            $this->assertDatabaseHas('business_analytics_events', [
                'business_id' => $business->id,
                'event_type' => $eventType,
            ]);
        }

        $phoneEvent = BusinessAnalyticsEvent::query()
            ->where('business_id', $business->id)
            ->where('event_type', BusinessAnalyticsEvent::PHONE_CLICK)
            ->firstOrFail();

        $this->assertSame('public_profile', data_get($phoneEvent->metadata, 'source'));
    }

    public function test_business_owner_dashboard_uses_only_own_analytics(): void
    {
        $owner = $this->business([
            'business_name' => 'Owner Analytics Business',
            'subscription_plan' => 'premium',
        ]);

        $otherBusiness = $this->business([
            'business_name' => 'Other Analytics Business',
            'subscription_plan' => 'premium',
        ]);

        $this->event($owner, BusinessAnalyticsEvent::PROFILE_VIEW);
        $this->event($owner, BusinessAnalyticsEvent::PROFILE_VIEW);
        $this->event($owner, BusinessAnalyticsEvent::PHONE_CLICK);

        for ($i = 0; $i < 9; $i++) {
            $this->event($otherBusiness, BusinessAnalyticsEvent::PROFILE_VIEW);
        }

        $response = $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk();

        $content = $response->getContent();

        $this->assertSame('2', $this->metricValue($content, 'analytics-month-profile-views'));
        $this->assertSame('1', $this->metricValue($content, 'analytics-month-phone-clicks'));
    }

    public function test_business_dashboard_shows_service_request_counts(): void
    {
        $owner = $this->business([
            'business_name' => 'Owner Request Metrics Business',
        ]);

        $otherBusiness = $this->business([
            'business_name' => 'Other Request Metrics Business',
        ]);

        $this->serviceRequest($owner, ['status' => ServiceRequest::STATUS_NEW]);
        $this->serviceRequest($owner, ['status' => ServiceRequest::STATUS_CONTACTED]);
        $this->serviceRequest($owner, ['status' => ServiceRequest::STATUS_COMPLETED]);
        $this->serviceRequest($owner, ['status' => ServiceRequest::STATUS_CANCELLED]);
        $this->serviceRequest($otherBusiness, ['status' => ServiceRequest::STATUS_NEW]);

        $response = $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk();

        $content = $response->getContent();

        $this->assertSame('4', $this->metricValue($content, 'service-requests-total'));
        $this->assertSame('1', $this->metricValue($content, 'service-requests-new'));
        $this->assertSame('1', $this->metricValue($content, 'service-requests-contacted'));
        $this->assertSame('1', $this->metricValue($content, 'service-requests-completed'));
    }

    public function test_admin_dashboard_shows_platform_analytics(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $first = $this->business(['business_name' => 'First Admin Analytics Business']);
        $second = $this->business(['business_name' => 'Second Admin Analytics Business']);

        $this->event($first, BusinessAnalyticsEvent::PROFILE_VIEW);
        $this->event($first, BusinessAnalyticsEvent::PROFILE_VIEW);
        $this->event($second, BusinessAnalyticsEvent::PROFILE_VIEW);
        $this->event($first, BusinessAnalyticsEvent::PHONE_CLICK);
        $this->event($second, BusinessAnalyticsEvent::CHAT_CLICK);
        $this->event($second, BusinessAnalyticsEvent::SOCIAL_CLICK);
        $this->serviceRequest($first, ['status' => ServiceRequest::STATUS_NEW]);
        $this->serviceRequest($second, ['status' => ServiceRequest::STATUS_CONTACTED]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk();

        $content = $response->getContent();

        $this->assertSame('3', $this->metricValue($content, 'admin-total-profile-views'));
        $this->assertSame('3', $this->metricValue($content, 'admin-total-clicks'));
        $this->assertSame('1', $this->metricValue($content, 'admin-month-phone-clicks'));
        $this->assertSame('2', $this->metricValue($content, 'admin-service-requests-total'));
        $this->assertSame('1', $this->metricValue($content, 'admin-service-requests-new'));
        $this->assertStringContainsString('First Admin Analytics Business', $content);
        $this->assertStringContainsString('Second Admin Analytics Business', $content);
    }

    public function test_premium_business_sees_premium_value_message(): void
    {
        $business = $this->business([
            'business_name' => 'Premium Value Business',
            'subscription_plan' => 'premium',
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('premium-value-message', false)
            ->assertSee('Premium профилите получават по-високо позициониране', false);
    }

    public function test_standard_business_sees_upgrade_hint(): void
    {
        $business = $this->business([
            'business_name' => 'Standard Value Business',
            'subscription_plan' => 'standard',
        ]);

        $this->actingAs($business)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('standard-upgrade-hint', false)
            ->assertSee('Искате повече видимост?', false);
    }

    private function event(User $business, string $eventType): BusinessAnalyticsEvent
    {
        return BusinessAnalyticsEvent::create([
            'business_id' => $business->id,
            'event_type' => $eventType,
            'ip_hash' => hash('sha256', $business->id . $eventType),
            'user_agent' => 'Feature test',
            'metadata' => ['test' => true],
        ]);
    }

    private function serviceRequest(User $business, array $overrides = []): ServiceRequest
    {
        $serviceRequest = ServiceRequest::create(array_merge([
            'name' => 'Analytics Request Client',
            'phone' => '0888123456',
            'city' => 'София',
            'category' => $business->business_category,
            'service' => 'Заявка от бизнес профил',
            'description' => 'Тестова заявка за analytics dashboard.',
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

    private function metricValue(string $html, string $testId): string
    {
        if (!preg_match('/<[^>]+data-testid="' . preg_quote($testId, '/') . '"[^>]*>(.*?)<\/[^>]+>/s', $html, $matches)) {
            $this->fail("Metric {$testId} was not found.");
        }

        return trim(strip_tags($matches[1]));
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'BON Analytics Test Business',
            'business_category' => 'Автосервиз',
            'city' => 'София',
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

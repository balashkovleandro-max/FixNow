<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreLaunchAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_core_public_pages_load_successfully(): void
    {
        foreach ($this->corePublicUrls() as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_health_route_returns_safe_ok_json(): void
    {
        $this->get(route('health'))
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'app' => 'FixNow.bg',
            ]);
    }

    public function test_robots_txt_is_accessible(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('User-agent: *')
            ->assertSee('Allow: /')
            ->assertSee('Sitemap: https://fixnow.bg/sitemap.xml');
    }

    public function test_sitemap_xml_is_accessible(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<urlset', false)
            ->assertSee('https://fixnow.bg/zayavi-oferta', false)
            ->assertSee('https://fixnow.bg/grad/pleven/vik-uslugi', false);
    }

    public function test_public_pages_include_basic_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_homepage_contains_critical_conversion_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('/zayavi-oferta', false)
            ->assertSee('/za-biznesi', false)
            ->assertSee('/plans', false)
            ->assertSee('/businesses', false)
            ->assertSee('/top-biznesi', false)
            ->assertSee('data-track="cta_request"', false)
            ->assertSee('data-track="cta_business_signup"', false)
            ->assertSee('data-track="cta_view_business"', false);
    }

    public function test_analytics_snippets_are_not_rendered_when_ids_are_empty(): void
    {
        config([
            'services.analytics.ga_measurement_id' => null,
            'services.analytics.meta_pixel_id' => null,
            'services.analytics.clarity_project_id' => null,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('googletagmanager.com/gtag/js', false)
            ->assertDontSee('connect.facebook.net/en_US/fbevents.js', false)
            ->assertDontSee('clarity.ms/tag', false);
    }

    public function test_analytics_snippets_render_when_ids_are_configured(): void
    {
        config([
            'services.analytics.ga_measurement_id' => 'G-FIXNOWTEST',
            'services.analytics.meta_pixel_id' => '123456789',
            'services.analytics.clarity_project_id' => 'claritytest',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('googletagmanager.com/gtag/js?id=G-FIXNOWTEST', false)
            ->assertSee("fbq('init', '123456789')", false)
            ->assertSee('www.clarity.ms/tag', false)
            ->assertSee('claritytest', false);
    }

    public function test_customer_offer_page_loads_with_valid_public_token(): void
    {
        $serviceRequest = ServiceRequest::create([
            'name' => 'Launch Audit Client',
            'phone' => '0888123456',
            'email' => 'audit-client@example.com',
            'city' => 'Плевен',
            'category' => 'ВиК услуги',
            'service' => 'Ремонт на теч',
            'description' => 'Тестова заявка за преглед на получени оферти.',
            'urgency' => ServiceRequest::URGENCY_NORMAL,
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        $this->get(route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token]))
            ->assertOk()
            ->assertSee('Получени оферти')
            ->assertSee('Все още няма получени оферти');
    }

    public function test_business_service_requests_page_loads_for_executor(): void
    {
        $business = User::factory()->create([
            'role' => 'business',
            'business_name' => 'Launch Audit Executor',
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
            'offer_points_balance' => 30,
        ]);

        $this->actingAs($business)
            ->get(route('business.service-requests.index'))
            ->assertOk()
            ->assertSee('Заявки и оферти')
            ->assertSee('Нови заявки за оферти');
    }

    public function test_admin_service_request_pages_require_admin_access(): void
    {
        $serviceRequest = ServiceRequest::create([
            'name' => 'Admin Guard Client',
            'phone' => '0888123456',
            'city' => 'Плевен',
            'category' => 'Почистване',
            'service' => 'Почистване след ремонт',
            'description' => 'Заявка за admin guard проверка.',
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ]);

        $this->get(route('admin.service-requests.index'))
            ->assertRedirect(route('login'));

        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.service-requests.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.service-requests.show', $serviceRequest))
            ->assertForbidden();
    }

    public function test_key_empty_states_render_with_clear_ctas(): void
    {
        $this->get(route('businesses.index'))
            ->assertOk()
            ->assertSee('public-businesses-empty-state', false)
            ->assertSee('Пусни заявка')
            ->assertSee('Стани изпълнител');

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('public-services-empty-state', false)
            ->assertSee('Пусни заявка')
            ->assertSee('Стани изпълнител');

        $this->get(route('top.businesses'))
            ->assertOk()
            ->assertSee('Топ изпълнители')
            ->assertSee('Все още няма активни изпълнители');
    }

    private function corePublicUrls(): array
    {
        return [
            '/',
            '/services',
            '/categories',
            '/businesses',
            '/top-biznesi',
            '/plans',
            '/za-biznesi',
            '/zayavi-oferta',
            '/how-it-works',
            '/contact',
            '/terms',
            '/privacy',
            '/cookies',
            '/grad/pleven',
            '/grad/pleven/maistori',
            '/grad/pleven/vik-uslugi',
            '/grad/pleven/elektrouslugi',
            '/grad/pleven/avtoservizi',
            '/grad/pleven/pochistvane',
        ];
    }
}

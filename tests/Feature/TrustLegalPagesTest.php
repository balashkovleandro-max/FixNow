<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustLegalPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_terms_page_loads(): void
    {
        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('Общи условия')
            ->assertSee('FixNow.bg');
    }

    public function test_privacy_page_loads(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Политика за поверителност')
            ->assertSee('Stripe');
    }

    public function test_cookies_page_loads(): void
    {
        $this->get(route('cookies'))
            ->assertOk()
            ->assertSee('Политика за бисквитки')
            ->assertSee('Stripe');
    }

    public function test_public_footer_contains_legal_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('public-footer', false)
            ->assertSee('/terms', false)
            ->assertSee('/privacy', false)
            ->assertSee('/cookies', false);
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Контакт')
            ->assertSee('Заяви оферта');
    }

    public function test_how_it_works_page_loads(): void
    {
        $this->get('/how-it-works')
            ->assertOk()
            ->assertSee('Как работи')
            ->assertSee('За клиенти')
            ->assertSee('За изпълнители');
    }

    public function test_core_public_pages_still_load(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('businesses.index'))->assertOk();
        $this->get(route('services.index'))->assertOk();
        $this->get(route('plans'))->assertOk();
        $this->get('/categories')->assertOk();
    }

    public function test_core_public_pages_share_premium_header(): void
    {
        $pages = [
            '/',
            route('services.index'),
            route('businesses.index'),
            route('plans'),
            route('business.landing'),
            route('request.service'),
            '/how-it-works',
        ];

        foreach ($pages as $page) {
            $this->get($page)
                ->assertOk()
                ->assertSee('Услуги')
                ->assertSee('За клиенти')
                ->assertSee('За изпълнители')
                ->assertSee('Планове')
                ->assertSee('Пусни заявка');
        }
    }
}

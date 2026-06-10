<?php

namespace Tests\Feature;

use Tests\TestCase;

class BonFrontendTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_bon_landing_page_loads_with_core_positioning(): void
    {
        $this->get(route('bon.index'))
            ->assertOk()
            ->assertSee('BON')
            ->assertSee('Business Operating Network')
            ->assertSee('От проблем до решение')
            ->assertSee('Имаш бизнес проблем')
            ->assertSee('Търсиш решение')
            ->assertSee('Проверени бизнеси');
    }

    public function test_root_homepage_uses_bon_product_direction(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('BON')
            ->assertSee('Business Operating Network')
            ->assertSee('От проблем до решение');
    }

    public function test_bon_command_center_demo_loads(): void
    {
        $this->get(route('bon.command-center'))
            ->assertOk()
            ->assertSee('Business Command Center')
            ->assertSee('BON Score')
            ->assertSee('BON Operator');
    }

    public function test_bon_business_problem_form_loads(): void
    {
        $this->get(route('bon.business-problem'))
            ->assertOk()
            ->assertSee('BON Diagnose')
            ->assertSee('business_name')
            ->assertSee('problem_type');
    }

    public function test_bon_profile_demo_loads(): void
    {
        $this->get(route('bon.profile'))
            ->assertOk()
            ->assertSee('Studio Dental Care')
            ->assertSee('BON Score');
    }

    public function test_bon_alias_routes_are_available(): void
    {
        $this->get('/business/command-center')
            ->assertRedirect('/bon/command-center');

        $this->get('/bon/business-profile')
            ->assertRedirect('/bon/profile');

        $this->get('/bon/demo-business-profile')
            ->assertOk()
            ->assertSee('Studio Dental Care');
    }
}

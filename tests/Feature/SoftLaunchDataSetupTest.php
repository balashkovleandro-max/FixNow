<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\SoftLaunchPlevenSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SoftLaunchDataSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_admin_command_creates_admin_user(): void
    {
        $this->artisan('fixnow:create-admin', [
            '--name' => 'Launch Admin',
            '--email' => 'launch-admin@example.com',
            '--password' => 'secret-password',
        ])
            ->expectsOutput('Admin user is ready: launch-admin@example.com')
            ->assertExitCode(0);

        $admin = User::query()->where('email', 'launch-admin@example.com')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertSame('Launch Admin', $admin->name);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('secret-password', $admin->password));
        $this->assertNotSame('secret-password', $admin->password);
    }

    public function test_create_admin_command_updates_existing_user_to_admin(): void
    {
        $user = User::factory()->create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'role' => 'customer',
            'password' => Hash::make('old-password'),
        ]);

        $this->artisan('fixnow:create-admin', [
            '--name' => 'Existing Admin',
            '--email' => 'existing@example.com',
            '--password' => 'new-password',
        ])
            ->expectsOutput('Admin user is ready: existing@example.com')
            ->assertExitCode(0);

        $this->assertSame(1, User::query()->where('email', 'existing@example.com')->count());

        $user->refresh();

        $this->assertSame('admin', $user->role);
        $this->assertSame('Existing Admin', $user->name);
        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertFalse(Hash::check('old-password', $user->password));
    }

    public function test_soft_launch_pleven_seeder_creates_visible_executor_profiles(): void
    {
        $this->seed(SoftLaunchPlevenSeeder::class);

        $executors = User::query()
            ->where('role', 'business')
            ->where('city', 'Плевен')
            ->get();

        $this->assertGreaterThanOrEqual(5, $executors->count());
        $this->assertGreaterThanOrEqual(2, $executors->where('subscription_plan', 'premium')->count());
        $this->assertGreaterThanOrEqual(2, $executors->where('is_verified', true)->count());

        $executors->each(function (User $executor): void {
            $this->assertTrue($executor->isPubliclyVisible());
            $this->assertGreaterThanOrEqual(80, $executor->profileCompleteness()['percent']);
            $this->assertNotEmpty($executor->serviceCategories());
            $this->assertContains('Плевен', $executor->serviceCities());
        });
    }

    public function test_soft_launch_profiles_appear_in_public_business_listing(): void
    {
        $this->withoutVite();
        $this->seed(SoftLaunchPlevenSeeder::class);

        $this->get(route('businesses.index', ['city' => 'Плевен']))
            ->assertOk()
            ->assertSee('Плевен Ремонт Про')
            ->assertSee('ВиК Плевен Експрес')
            ->assertSee('Електро Майстор Плевен')
            ->assertSee('Авто Сервиз Север');
    }

    public function test_default_demo_seeder_is_skipped_in_production_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertDatabaseMissing('users', [
            'email' => 'admin@example.com',
        ]);
    }
}

<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bon:create-admin {--name=} {--email=} {--password=}', function () {
    $name = $this->option('name') ?: $this->ask('Admin name');
    $email = $this->option('email') ?: $this->ask('Admin email');
    $password = $this->option('password') ?: $this->secret('Admin password');

    if (blank($name) || blank($email) || blank($password)) {
        $this->error('Name, email and password are required.');

        return 1;
    }

    $admin = User::query()->firstOrNew(['email' => $email]);

    $admin->forceFill([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
        'email_verified_at' => now(),
    ])->save();

    $this->info("Admin user is ready: {$email}");

    return 0;
})->purpose('Create or promote a BON admin user for soft launch');

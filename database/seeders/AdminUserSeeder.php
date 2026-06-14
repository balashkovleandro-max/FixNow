<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->where('email', 'admin04@bon.bg')->exists()) {
            return;
        }

        $password = env('BON_ADMIN_PASSWORD');

        if (blank($password)) {
            $this->command?->warn('BON_ADMIN_PASSWORD is not set. Admin user was not created.');

            return;
        }

        $payload = [
            'name' => 'Admin04',
            'email' => 'admin04@bon.bg',
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ];

        if (Schema::hasColumn('users', 'is_suspended')) {
            $payload['is_suspended'] = false;
        }

        if (Schema::hasColumn('users', 'account_type')) {
            $payload['account_type'] = 'admin';
        }

        if (Schema::hasColumn('users', 'profile_type')) {
            $payload['profile_type'] = null;
        }

        User::query()->create($payload);
    }
}

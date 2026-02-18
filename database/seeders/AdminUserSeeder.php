<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Concerns\ReadsJsonSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the default admin user and role assignment.
 */
class AdminUserSeeder extends Seeder
{
    use ReadsJsonSeedData;

    /**
     * Run the admin user seeder.
     */
    public function run(): void
    {
        $payload = $this->readSeedJson('admin-user.json');

        if (! is_array($payload) || ! isset($payload['email'])) {
            $this->warn('Skipping AdminUserSeeder: invalid payload.');

            return;
        }

        $password = (string) ($payload['password'] ?? 'password');

        $admin = User::updateOrCreate(
            ['email' => (string) $payload['email']],
            [
                'name' => (string) ($payload['name'] ?? 'Admin'),
                'password' => Hash::make($password),
            ]
        );

        $role = (string) ($payload['role'] ?? 'admin');
        if (! $admin->hasRole($role)) {
            $admin->assignRole($role);
        }
    }
}

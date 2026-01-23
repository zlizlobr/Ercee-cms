<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Seeds default roles and permission cache.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the roles and permissions seeder.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'operator']);
        Role::firstOrCreate(['name' => 'marketing']);
    }
}

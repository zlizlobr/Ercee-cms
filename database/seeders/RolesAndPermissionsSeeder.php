<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\Module\ModuleManager;
use Database\Seeders\Concerns\ReadsJsonSeedData;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    use ReadsJsonSeedData;

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $payload = $this->readSeedJson('roles-permissions.json');
        if (! is_array($payload)) {
            $this->warn('Skipping RolesAndPermissionsSeeder: invalid payload.');

            return;
        }

        $roles = is_array($payload['roles'] ?? null) ? $payload['roles'] : [];
        $corePermissions = is_array($payload['core_permissions'] ?? null) ? $payload['core_permissions'] : [];
        $adminRoleName = (string) ($payload['admin_role'] ?? 'admin');

        foreach ($roles as $roleName) {
            if (is_string($roleName) && $roleName !== '') {
                Role::firstOrCreate(['name' => $roleName]);
            }
        }

        foreach ($corePermissions as $permissionName) {
            if (is_string($permissionName) && $permissionName !== '') {
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        $modulePermissions = app(ModuleManager::class)->getAllPermissions();
        foreach ($modulePermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $admin = Role::firstOrCreate(['name' => $adminRoleName]);
        $admin->syncPermissions(Permission::all());
    }
}

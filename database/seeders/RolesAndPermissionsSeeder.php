<?php

namespace Database\Seeders;

use App\Support\Module\ModuleManager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Core roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'operator']);
        Role::firstOrCreate(['name' => 'marketing']);

        // Core permissions
        $corePermissions = [
            'view_content',
            'create_content',
            'update_content',
            'delete_content',
            'view_media',
            'upload_media',
            'delete_media',
            'view_subscribers',
            'create_subscribers',
            'update_subscribers',
            'delete_subscribers',
            'manage_settings',
        ];

        foreach ($corePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Module permissions (prefixed with module.<name>.)
        $moduleManager = app(ModuleManager::class);
        $modulePermissions = $moduleManager->getAllPermissions();

        foreach ($modulePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin gets all permissions
        $admin->syncPermissions(
            Permission::all()
        );
    }
}

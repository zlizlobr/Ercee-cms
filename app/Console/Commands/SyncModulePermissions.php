<?php

namespace App\Console\Commands;

use App\Support\Module\ModuleManager;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class SyncModulePermissions extends Command
{
    protected $signature = 'modules:sync-permissions';

    protected $description = 'Synchronize permissions from all enabled modules into the database';

    public function handle(ModuleManager $moduleManager): int
    {
        $permissions = $moduleManager->getAllPermissions();

        if (empty($permissions)) {
            $this->info('No module permissions found.');

            return self::SUCCESS;
        }

        $created = 0;

        foreach ($permissions as $permission) {
            $result = Permission::firstOrCreate(['name' => $permission]);

            if ($result->wasRecentlyCreated) {
                $created++;
                $this->line("  Created: {$permission}");
            }
        }

        $this->info("Synced {$created} new permissions out of " . count($permissions) . ' total module permissions.');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Contracts\Module\AdminExtensionInterface;
use App\Support\Module\ModuleManager;
use Illuminate\Console\Command;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list';

    protected $description = 'List all registered modules and their status';

    public function handle(ModuleManager $moduleManager): int
    {
        $configModules = config('modules.modules', []);
        $rows = [];

        foreach ($configModules as $name => $config) {
            $enabled = $config['enabled'] ?? false;
            $module = $moduleManager->getModule($name);

            if ($module) {
                $resources = $module instanceof AdminExtensionInterface
                    ? count($module->getResources())
                    : 0;
                $blocks = $module instanceof AdminExtensionInterface
                    ? count($module->getBlocks())
                    : 0;
                $permissions = count($module->getPermissions());

                $rows[] = [
                    $name,
                    $module->getVersion(),
                    $enabled ? '<fg=green>enabled</>' : '<fg=red>disabled</>',
                    implode(', ', array_keys($module->getDependencies())) ?: '-',
                    $resources,
                    $blocks,
                    $permissions,
                ];
            } else {
                $rows[] = [
                    $name,
                    $config['version'] ?? '?',
                    $enabled ? '<fg=yellow>not loaded</>' : '<fg=red>disabled</>',
                    '-',
                    0,
                    0,
                    0,
                ];
            }
        }

        $this->table(
            ['Module', 'Version', 'Status', 'Dependencies', 'Resources', 'Blocks', 'Permissions'],
            $rows,
        );

        return self::SUCCESS;
    }
}

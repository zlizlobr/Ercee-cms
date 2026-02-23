<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Scaffold a new module package in the modules workspace.
 */
class ModuleMakeCommand extends Command
{
    protected $signature = 'module:make {name : The module name (lowercase, e.g. "blog")}';

    protected $description = 'Scaffold a new module in the ercee-modules directory';

    /**
     * Generate module directories and base files.
     *
     * @return int Exit code (`Command::SUCCESS` or `Command::FAILURE`).
     */
    public function handle(): int
    {
        $name = Str::lower($this->argument('name'));
        $studly = Str::studly($name);
        $modulesDir = dirname(base_path()) . '/ercee-modules';
        $moduleDir = "{$modulesDir}/ercee-module-{$name}";

        if (is_dir($moduleDir)) {
            $this->error("Module directory already exists: {$moduleDir}");

            return self::FAILURE;
        }

        $directories = [
            'src',
            'src/Domain',
            'src/Application',
            'src/Filament',
            'src/Filament/Resources',
            'src/Http/Controllers',
            'config',
            'database/migrations',
            'routes',
            'resources/views',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$moduleDir}/{$dir}", 0755, true);
        }

        File::put("{$moduleDir}/composer.json", $this->composerJson($name, $studly));
        File::put("{$moduleDir}/src/{$studly}ModuleServiceProvider.php", $this->serviceProvider($name, $studly));
        File::put("{$moduleDir}/config/module.php", $this->moduleConfig($name));
        File::put("{$moduleDir}/routes/api.php", $this->routesFile());
        File::put("{$moduleDir}/routes/web.php", $this->routesFile());

        $this->info("Module scaffolded: {$moduleDir}");
        $this->newLine();
        $this->line('Next steps:');
        $this->line("  1. Add <fg=yellow>\"ercee/module-{$name}\": \"@dev\"</> to your composer.json require");
        $this->line("  2. Run <fg=yellow>composer update ercee/module-{$name}</>");
        $this->line("  3. Add the module to <fg=yellow>config/modules.php</>");
        $this->line("  4. Run <fg=yellow>php artisan module:list</> to verify");

        return self::SUCCESS;
    }

    /**
     * Build a composer.json payload for the module.
     *
     * @param string $name Module machine name.
     * @param string $studly StudlyCase module name.
     * @return string JSON payload with trailing newline.
     */
    private function composerJson(string $name, string $studly): string
    {
        $json = [
            'name' => "ercee/module-{$name}",
            'description' => "Ercee CMS {$studly} module",
            'type' => 'library',
            'version' => '1.0.0',
            'license' => 'proprietary',
            'require' => new \stdClass(),
            'autoload' => [
                'psr-4' => [
                    "Modules\\{$studly}\\" => 'src/',
                ],
            ],
            'extra' => [
                'ercee' => [
                    'name' => $name,
                    'provider' => "Modules\\{$studly}\\{$studly}ModuleServiceProvider",
                ],
                'laravel' => [
                    'providers' => [
                        "Modules\\{$studly}\\{$studly}ModuleServiceProvider",
                    ],
                ],
            ],
        ];

        return json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }

    /**
     * Build the module service provider class template.
     *
     * @param string $name Module machine name.
     * @param string $studly StudlyCase module name.
     * @return string PHP class template.
     */
    private function serviceProvider(string $name, string $studly): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$studly};

use App\Support\Module\BaseModuleServiceProvider;

class {$studly}ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string \$name = '{$name}';
    protected string \$version = '1.0.0';
    protected string \$description = '';
    protected array \$dependencies = [];
    protected array \$permissions = [];

    protected function registerBindings(): void
    {
        //
    }

    public function getResources(): array
    {
        return [];
    }

    protected function getModulePath(string \$path = ''): string
    {
        return __DIR__ . '/../' . ltrim(\$path, '/');
    }
}

PHP;
    }

    /**
     * Build the module config file template.
     *
     * @param string $name Module machine name.
     * @return string PHP config template.
     */
    private function moduleConfig(string $name): string
    {
        return <<<PHP
<?php

return [
    'name' => '{$name}',
    'version' => '1.0.0',
    'description' => '',
];

PHP;
    }

    /**
     * Build an empty routes file template.
     *
     * @return string PHP routes template.
     */
    private function routesFile(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

PHP;
    }
}


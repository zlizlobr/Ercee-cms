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
            'tests',
            'tests/Unit',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$moduleDir}/{$dir}", 0755, true);
        }

        File::put("{$moduleDir}/composer.json", $this->composerJson($name, $studly));
        File::put("{$moduleDir}/src/{$studly}ModuleServiceProvider.php", $this->serviceProvider($name, $studly));
        File::put("{$moduleDir}/config/module.php", $this->moduleConfig($name));
        File::put("{$moduleDir}/routes/api.php", $this->routesFile());
        File::put("{$moduleDir}/routes/web.php", $this->routesFile());
        File::put("{$moduleDir}/phpstan.neon.dist", $this->phpstanConfig());
        File::put("{$moduleDir}/phpunit.xml", $this->phpunitConfig());
        File::put("{$moduleDir}/tests/phpstan-bootstrap.php", $this->phpstanBootstrap());
        File::put("{$moduleDir}/tests/TestCase.php", $this->testCase($studly));
        File::put("{$moduleDir}/tests/Unit/SmokeTest.php", $this->smokeTest($studly));

        $this->info("Module scaffolded: {$moduleDir}");
        $this->newLine();
        $this->line('Next steps:');
        $this->line("  1. Add <fg=yellow>\"ercee/module-{$name}\": \"@dev\"</> to your composer.json require");
        $this->line("  2. Run <fg=yellow>composer update ercee/module-{$name}</>");
        $this->line("  3. Add the module to <fg=yellow>config/modules.php</>");
        $this->line("  4. Run <fg=yellow>php artisan module:list</> to verify");
        $this->line("  5. Run <fg=yellow>cd {$moduleDir} && composer install</>");
        $this->line("  6. Run <fg=yellow>cd {$moduleDir} && composer ci:simulate</>");
        $this->line("Shared CI guide: <fg=yellow>{$modulesDir}/docs/guides/ci-workflow-junior.md</>");

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
            'require' => [
                'php' => '^8.2',
            ],
            'require-dev' => [
                'laravel/pint' => '^1.17',
                'phpstan/phpstan' => '^1.11',
                'phpunit/phpunit' => '^11.0',
            ],
            'autoload' => [
                'psr-4' => [
                    "Modules\\{$studly}\\" => 'src/',
                ],
            ],
            'autoload-dev' => [
                'psr-4' => [
                    "Modules\\{$studly}\\Tests\\" => 'tests/',
                ],
            ],
            'scripts' => [
                'ci:simulate' => 'bash ../scripts/workflow/simulate-ci.sh --module-path .',
                'ci:simulate-fast' => 'bash ../scripts/workflow/simulate-ci.sh --module-path . --fast',
                'test' => 'phpunit --configuration=phpunit.xml --colors=always',
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

    /**
     * Build the default PHPStan configuration.
     *
     * @return string PHPStan config payload.
     */
    private function phpstanConfig(): string
    {
        return <<<'NEON'
parameters:
  level: 0
  bootstrapFiles:
    - tests/phpstan-bootstrap.php

NEON;
    }

    /**
     * Build the default PHPUnit configuration.
     *
     * @return string PHPUnit XML payload.
     */
    private function phpunitConfig(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php" colors="true" cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
</phpunit>

XML;
    }

    /**
     * Build a PHPStan bootstrap that keeps standalone module analysis green.
     *
     * @return string PHP bootstrap payload.
     */
    private function phpstanBootstrap(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Support\Module {
    if (! class_exists(BaseModuleServiceProvider::class)) {
        abstract class BaseModuleServiceProvider
        {
            protected string $name = '';

            protected string $version = '';

            protected string $description = '';

            protected array $dependencies = [];

            protected array $permissions = [];

            public function register(): void
            {
                $this->registerBindings();
            }

            public function boot(): void {}

            protected function registerBindings(): void {}

            public function getResources(): array
            {
                return [];
            }

            protected function getModulePath(string $path = ''): string
            {
                return __DIR__.'/../'.ltrim($path, '/');
            }
        }
    }
}

PHP;
    }

    /**
     * Build the base PHPUnit test case template.
     *
     * @return string PHP class template.
     */
    private function testCase(string $studly): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$studly}\\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {}

PHP;
    }

    /**
     * Build the default smoke test.
     *
     * @return string PHP class template.
     */
    private function smokeTest(string $studly): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$studly}\\Tests\\Unit;

use Modules\\{$studly}\\Tests\\TestCase;

final class SmokeTest extends TestCase
{
    public function test_truth_is_true(): void
    {
        self::assertTrue(true);
    }
}

PHP;
    }
}

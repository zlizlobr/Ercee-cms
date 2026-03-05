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
            '.github/workflows',
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
        File::put("{$moduleDir}/CHANGELOG.md", '');
        File::put("{$moduleDir}/.github/workflows/release.yml", $this->releaseWorkflow());

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

    private function releaseWorkflow(): string
    {
        return <<<'YAML'
name: Release

on:
  push:
    branches:
      - main

jobs:
  release:
    runs-on: ubuntu-latest

    permissions:
      contents: write

    steps:
      - name: Checkout repository
        uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: Check for release loop
        id: guard
        run: |
          AUTHOR=$(git log -1 --pretty=format:'%an')
          MESSAGE=$(git log -1 --pretty=format:'%s')
          if [ "$AUTHOR" = "github-actions" ] || echo "$MESSAGE" | grep -qE '^chore: release v'; then
            echo "skip=true" >> $GITHUB_OUTPUT
          else
            echo "skip=false" >> $GITHUB_OUTPUT
          fi

      - name: Read release intent
        id: intent
        if: steps.guard.outputs.skip == 'false'
        uses: actions/github-script@v7
        with:
          script: |
            const { owner, repo } = context.repo;
            const sha = context.sha;
            let title = '';

            try {
              const prs = await github.rest.repos.listPullRequestsAssociatedWithCommit({
                owner,
                repo,
                commit_sha: sha,
              });
              if (prs.data.length > 0) {
                title = prs.data[0].title || '';
              }
            } catch (error) {
              core.info(`PR lookup failed: ${error.message}`);
            }

            if (!title) {
              const msg = context.payload.head_commit?.message || '';
              title = msg.split('\n')[0] || '';
            }

            core.info(`Release intent source title: ${title}`);

            let type = '';
            if (/release:\s*patch/i.test(title)) type = 'patch';
            else if (/release:\s*minor/i.test(title)) type = 'minor';
            else if (/release:\s*major/i.test(title)) type = 'major';

            if (!type) {
              core.info('No release intent found. Skipping release.');
              return;
            }

            core.setOutput('type', type);

      - name: Get last tag
        id: last_tag
        if: steps.intent.outputs.type != ''
        run: |
          TAG=$(git tag --sort=-v:refname | head -n 1)
          if [ -z "$TAG" ]; then
            TAG="v0.0.0"
          fi
          echo "tag=$TAG" >> $GITHUB_OUTPUT

      - name: Calculate next version
        id: version
        if: steps.intent.outputs.type != ''
        run: |
          VERSION=${{ steps.last_tag.outputs.tag }}
          VERSION=${VERSION#v}

          IFS='.' read -r MAJOR MINOR PATCH <<< "$VERSION"

          case "${{ steps.intent.outputs.type }}" in
            patch)
              PATCH=$((PATCH + 1))
              ;;
            minor)
              MINOR=$((MINOR + 1))
              PATCH=0
              ;;
            major)
              MAJOR=$((MAJOR + 1))
              MINOR=0
              PATCH=0
              ;;
          esac

          NEXT="v$MAJOR.$MINOR.$PATCH"
          echo "next=$NEXT" >> $GITHUB_OUTPUT
          echo "next_bare=$MAJOR.$MINOR.$PATCH" >> $GITHUB_OUTPUT
          echo "Next version: $NEXT"

      - name: Capture HEAD SHA
        id: head_sha
        if: steps.intent.outputs.type != ''
        run: echo "sha=$(git rev-parse HEAD)" >> $GITHUB_OUTPUT

      - name: Update config/module.php
        if: steps.intent.outputs.type != ''
        run: |
          VERSION_BARE="${{ steps.version.outputs.next_bare }}"
          sed -i "s/'version' => '[^']*'/'version' => '$VERSION_BARE'/" config/module.php
          echo "Updated config/module.php to version $VERSION_BARE"

      - name: Verify version consistency
        if: steps.intent.outputs.type != ''
        run: |
          VERSION_BARE="${{ steps.version.outputs.next_bare }}"
          ACTUAL=$(grep -oP "(?<='version' => ')[^']*" config/module.php)
          if [ "$ACTUAL" != "$VERSION_BARE" ]; then
            echo "ERROR: config/module.php version ($ACTUAL) does not match release ($VERSION_BARE)"
            exit 1
          fi
          echo "Version verified: $ACTUAL"

      - name: Generate release notes
        id: notes
        if: steps.intent.outputs.type != ''
        run: |
          LAST_TAG=${{ steps.last_tag.outputs.tag }}

          NOTES=$(git log "$LAST_TAG..HEAD" --pretty=format:"- %s%n%b")

          echo "notes<<EOF" >> $GITHUB_OUTPUT
          echo "$NOTES" >> $GITHUB_OUTPUT
          echo "EOF" >> $GITHUB_OUTPUT

      - name: Update CHANGELOG.md
        if: steps.intent.outputs.type != ''
        run: |
          VERSION=${{ steps.version.outputs.next }}
          DATE=$(date +'%Y-%m-%d')

          {
            echo "## [$VERSION] – $DATE"
            echo ""
            echo "${{ steps.notes.outputs.notes }}"
            echo ""
            cat CHANGELOG.md
          } > CHANGELOG.tmp

          mv CHANGELOG.tmp CHANGELOG.md

      - name: Commit release artifacts
        if: steps.intent.outputs.type != ''
        run: |
          git config user.name "github-actions"
          git config user.email "github-actions@github.com"
          git add CHANGELOG.md config/module.php
          git commit -m "chore: release ${{ steps.version.outputs.next }}"

      - name: Verify fast-forward (conflict guard)
        if: steps.intent.outputs.type != ''
        run: |
          git fetch origin main
          REMOTE_SHA=$(git rev-parse origin/main)
          LOCAL_BASE="${{ steps.head_sha.outputs.sha }}"
          if [ "$REMOTE_SHA" != "$LOCAL_BASE" ]; then
            echo "ERROR: main has diverged since checkout (remote: $REMOTE_SHA, local base: $LOCAL_BASE). Aborting to prevent incorrect tag."
            exit 1
          fi

      - name: Push release commit
        if: steps.intent.outputs.type != ''
        run: git push origin main

      - name: Create tag
        if: steps.intent.outputs.type != ''
        run: |
          git tag ${{ steps.version.outputs.next }}
          git push origin ${{ steps.version.outputs.next }}

      - name: Create GitHub Release
        if: steps.intent.outputs.type != ''
        uses: softprops/action-gh-release@v1
        with:
          tag_name: ${{ steps.version.outputs.next }}
          name: ${{ steps.version.outputs.next }}
          body: ${{ steps.notes.outputs.notes }}
YAML;
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

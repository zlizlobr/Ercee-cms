<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Discover local modules and compare them with module config.
 */
class ModuleSyncCommand extends Command
{
    protected $signature = 'module:sync {--update-config : Automatically update config/modules.php}';

    protected $description = 'Scan modules directory and compare with config/modules.php';

    /**
     * Execute the module discovery and comparison flow.
     *
     * @return int Exit code (`Command::SUCCESS`).
     */
    public function handle(): int
    {
        $modulesPath = base_path('modules');
        $configModules = config('modules.modules', []);

        if (! is_dir($modulesPath)) {
            $this->warn("Modules directory not found: {$modulesPath}");

            return self::SUCCESS;
        }

        $discovered = [];

        foreach (File::directories($modulesPath) as $dir) {
            $composerPath = $dir . '/composer.json';

            if (! file_exists($composerPath)) {
                continue;
            }

            $composer = json_decode(file_get_contents($composerPath), true);

            if (! $composer) {
                $this->warn("Invalid composer.json in {$dir}");
                continue;
            }

            $name = $composer['extra']['ercee']['name']
                ?? basename($dir);
            $version = $composer['version'] ?? '0.0.0';
            $provider = $composer['extra']['ercee']['provider']
                ?? $composer['extra']['laravel']['providers'][0]
                ?? null;

            $discovered[$name] = [
                'path' => $dir,
                'version' => $version,
                'provider' => $provider,
                'package' => $composer['name'] ?? 'unknown',
            ];
        }

        if (empty($discovered)) {
            $this->info('No modules discovered in modules directory.');

            return self::SUCCESS;
        }

        $rows = [];
        $newModules = [];

        foreach ($discovered as $name => $info) {
            $inConfig = isset($configModules[$name]);
            $configVersion = $configModules[$name]['version'] ?? null;
            $versionMatch = $configVersion === $info['version'];

            if ($inConfig && $versionMatch) {
                $status = '<fg=green>OK</>';
            } elseif ($inConfig && ! $versionMatch) {
                $status = "<fg=yellow>version mismatch (config: {$configVersion})</>";
            } else {
                $status = '<fg=cyan>NEW - not in config</>';
                $newModules[$name] = $info;
            }

            $rows[] = [
                $name,
                $info['package'],
                $info['version'],
                $info['provider'] ?? '-',
                $status,
            ];
        }

        foreach ($configModules as $name => $config) {
            if (! isset($discovered[$name])) {
                $rows[] = [
                    $name,
                    '-',
                    $config['version'] ?? '?',
                    $config['provider'] ?? '-',
                    '<fg=red>MISSING - in config but not found</>',
                ];
            }
        }

        $this->table(
            ['Module', 'Package', 'Version', 'Provider', 'Status'],
            $rows,
        );

        if (! empty($newModules) && $this->option('update-config')) {
            $this->info('Updating config/modules.php with new modules...');

            foreach ($newModules as $name => $info) {
                $this->line("  Adding: {$name} ({$info['version']})");
            }

            $this->warn('Config auto-update requires manual editing of config/modules.php for now.');
        }

        return self::SUCCESS;
    }
}


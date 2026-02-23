<?php

namespace App\Console\Commands;

use App\Domain\Content\ThemeSetting;
use App\Domain\Media\MediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Migrate theme logo paths to media UUID references.
 */
class MediaMigrateThemeLogos extends Command
{
    protected $signature = 'media:migrate-theme-logos
                            {--dry-run : Show what would be migrated without making changes}';

    protected $description = 'Migrate theme logo images from legacy paths to MediaLibrary';

    /**
     * @var int Counter of records successfully migrated in the current run.
     */
    private int $migratedCount = 0;

    /**
     * @var int Counter of records intentionally skipped during migration.
     */
    private int $skippedCount = 0;

    /**
     * @var int Counter of records that failed processing during migration.
     */
    private int $errorCount = 0;

    /**
     * Execute logo migration for theme settings.
     *
     * @return int Exit code (`Command::SUCCESS`).
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('DRY RUN - No changes will be made');
        }

        $settings = ThemeSetting::first();

        if (! $settings) {
            $this->info('No theme settings found. Nothing to migrate.');

            return self::SUCCESS;
        }

        $sections = ['global', 'header', 'footer'];
        $hasChanges = false;

        foreach ($sections as $section) {
            $data = $settings->{$section} ?? [];

            if ($this->migrateSection($data, $section, $isDryRun)) {
                $settings->{$section} = $data;
                $hasChanges = true;
            }
        }

        if ($hasChanges && ! $isDryRun) {
            $settings->save();
            $this->info('Theme settings updated.');
        }

        $this->newLine();
        $this->info('Migration complete:');
        $this->line("  Migrated: {$this->migratedCount}");
        $this->line("  Skipped: {$this->skippedCount}");
        $this->line("  Errors: {$this->errorCount}");

        return self::SUCCESS;
    }

    /**
     * Migrate one theme settings section.
     *
     * @param array<string, mixed> $data Theme section payload (passed by reference).
     * @param string $section Section name.
     * @param bool $isDryRun Whether to skip write operations.
     * @return bool True when section data was changed.
     */
    private function migrateSection(array &$data, string $section, bool $isDryRun): bool
    {
        if (! empty($data['logo_media_uuid'])) {
            $this->line("[{$section}] Already has logo_media_uuid, skipping.");
            $this->skippedCount++;

            return false;
        }

        $logoImage = $data['logo_image'] ?? null;

        if (empty($logoImage)) {
            $this->line("[{$section}] No logo_image set, skipping.");
            $this->skippedCount++;

            return false;
        }

        $uuid = $this->migrateFile($logoImage, $section, $isDryRun);

        if (! $uuid) {
            return false;
        }

        $data['logo_media_uuid'] = $uuid;

        return true;
    }

    /**
     * Migrate a single logo file and return its media UUID.
     *
     * @param string $path Relative storage path.
     * @param string $section Section name for logging.
     * @param bool $isDryRun Whether to skip write operations.
     * @return string|null Media UUID or null on failure.
     */
    private function migrateFile(string $path, string $section, bool $isDryRun): ?string
    {
        $fullPath = Storage::disk('public')->path($path);

        if (! File::exists($fullPath)) {
            $this->warn("[{$section}] File not found: {$path}");
            $this->errorCount++;

            return null;
        }

        if ($isDryRun) {
            $this->line("[{$section}] Would migrate: {$path}");
            $this->migratedCount++;

            return 'dry-run-uuid';
        }

        try {
            DB::beginTransaction();

            $mediaItem = MediaLibrary::create([
                'title' => pathinfo($path, PATHINFO_FILENAME),
                'alt_text' => null,
            ]);

            $media = $mediaItem
                ->addMedia($fullPath)
                ->preservingOriginal()
                ->withCustomProperties(['legacy_path' => $path, 'theme_section' => $section])
                ->toMediaCollection('default');

            DB::commit();

            $this->info("[{$section}] Migrated: {$path} -> {$media->uuid}");
            $this->migratedCount++;

            return $media->uuid;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("[{$section}] Failed to migrate {$path}: {$e->getMessage()}");
            $this->errorCount++;

            return null;
        }
    }
}


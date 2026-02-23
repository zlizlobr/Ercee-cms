<?php

namespace App\Console\Commands;

use App\Domain\Content\Page;
use App\Domain\Media\MediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Migrate legacy block image paths to media UUID references.
 */
class MediaMigrateBlocks extends Command
{
    protected $signature = 'media:migrate-blocks
                            {--dry-run : Show what would be migrated without making changes}
                            {--page-id= : Migrate only a specific page}';

    protected $description = 'Migrate block images from legacy paths to MediaLibrary';

    private int $migratedCount = 0;
    private int $skippedCount = 0;
    private int $errorCount = 0;

    /**
     * Migrate legacy block images to MediaLibrary records.
     *
     * @return int Exit code (`Command::SUCCESS`).
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $pageId = $this->option('page-id');

        if ($isDryRun) {
            $this->info('DRY RUN - No changes will be made');
        }

        $query = Page::query();

        if ($pageId) {
            $query->where('id', $pageId);
        }

        $pages = $query->whereNotNull('content')->get();

        $this->info("Processing {$pages->count()} pages...");

        $progressBar = $this->output->createProgressBar($pages->count());
        $progressBar->start();

        foreach ($pages as $page) {
            $this->processPage($page, $isDryRun);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Migration complete:");
        $this->line("  Migrated: {$this->migratedCount}");
        $this->line("  Skipped: {$this->skippedCount}");
        $this->line("  Errors: {$this->errorCount}");

        return self::SUCCESS;
    }

    /**
     * Process a single page's block content.
     *
     * @param Page $page Page model to process.
     * @param bool $isDryRun Whether to skip write operations.
     * @return void
     */
    private function processPage(Page $page, bool $isDryRun): void
    {
        $content = $page->content;
        $hasChanges = false;

        if (! is_array($content)) {
            return;
        }

        foreach ($content as $index => $block) {
            if (! isset($block['type'], $block['data'])) {
                continue;
            }

            $result = match ($block['type']) {
                'image' => $this->migrateImageBlock($block['data'], $isDryRun),
                'hero' => $this->migrateHeroBlock($block['data'], $isDryRun),
                default => null,
            };

            if ($result !== null) {
                $content[$index]['data'] = $result;
                $hasChanges = true;
            }
        }

        if ($hasChanges && ! $isDryRun) {
            $page->content = $content;
            $page->save();
        }
    }

    /**
     * Migrate an Image block payload to a media UUID reference.
     *
     * @param array<string, mixed> $data Block payload.
     * @param bool $isDryRun Whether to skip write operations.
     * @return array<string, mixed>|null
     */
    private function migrateImageBlock(array $data, bool $isDryRun): ?array
    {
        if (isset($data['media_uuid'])) {
            $this->skippedCount++;
            return null;
        }

        if (! isset($data['image']) || empty($data['image'])) {
            $this->skippedCount++;
            return null;
        }

        $path = $data['image'];
        $mediaUuid = $this->migrateFile($path, $data['alt'] ?? null, $isDryRun);

        if (! $mediaUuid) {
            return null;
        }

        $data['media_uuid'] = $mediaUuid;
        $data['legacy_image'] = $path;
        unset($data['image']);

        return $data;
    }

    /**
     * Migrate a Hero block payload to a media UUID reference.
     *
     * @param array<string, mixed> $data Block payload.
     * @param bool $isDryRun Whether to skip write operations.
     * @return array<string, mixed>|null
     */
    private function migrateHeroBlock(array $data, bool $isDryRun): ?array
    {
        if (isset($data['background_media_uuid'])) {
            $this->skippedCount++;
            return null;
        }

        if (! isset($data['background_image']) || empty($data['background_image'])) {
            $this->skippedCount++;
            return null;
        }

        $path = $data['background_image'];
        $mediaUuid = $this->migrateFile($path, null, $isDryRun);

        if (! $mediaUuid) {
            return null;
        }

        $data['background_media_uuid'] = $mediaUuid;
        $data['legacy_background_image'] = $path;
        unset($data['background_image']);

        return $data;
    }

    /**
     * Migrate a single file path to MediaLibrary and return the UUID.
     *
     * @param string $path Relative storage path.
     * @param string|null $alt Optional alt text.
     * @param bool $isDryRun Whether to skip write operations.
     * @return string|null Media UUID or null on failure.
     */
    private function migrateFile(string $path, ?string $alt, bool $isDryRun): ?string
    {
        $fullPath = Storage::disk('public')->path($path);

        if (! File::exists($fullPath)) {
            $this->warn("File not found: {$path}");
            $this->errorCount++;
            return null;
        }

        if ($isDryRun) {
            $this->line("  Would migrate: {$path}");
            $this->migratedCount++;
            return 'dry-run-uuid';
        }

        try {
            DB::beginTransaction();

            $mediaItem = MediaLibrary::create([
                'title' => pathinfo($path, PATHINFO_FILENAME),
                'alt_text' => $alt,
            ]);

            $media = $mediaItem
                ->addMedia($fullPath)
                ->preservingOriginal()
                ->withCustomProperties(['legacy_path' => $path])
                ->toMediaCollection('default');

            DB::commit();

            $this->migratedCount++;

            return $media->uuid;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to migrate {$path}: {$e->getMessage()}");
            $this->errorCount++;
            return null;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Domain\Content\Page;
use App\Domain\Media\MediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaMigrateRichtext extends Command
{
    protected $signature = 'media:migrate-richtext
                            {--dry-run : Show what would be migrated without making changes}
                            {--model=Page : Model to migrate (Page or Product)}';

    protected $description = 'Migrate RichEditor embedded images from legacy URLs to MediaLibrary';

    private const STORAGE_URL_PATTERN = '#(https?://[^/]+)?/storage/([^"\'>\s]+\.(jpg|jpeg|png|gif|webp))#i';

    private int $migratedCount = 0;
    private int $skippedCount = 0;
    private int $errorCount = 0;
    private array $urlToUuidMap = [];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $modelType = $this->option('model');

        if ($isDryRun) {
            $this->info('DRY RUN - No changes will be made');
        }

        $this->info("Processing {$modelType} records...");

        match ($modelType) {
            'Page' => $this->migratePages($isDryRun),
            'Product' => $this->migrateProducts($isDryRun),
            default => $this->error("Unknown model type: {$modelType}"),
        };

        $this->newLine();
        $this->info("Migration complete:");
        $this->line("  Images migrated: {$this->migratedCount}");
        $this->line("  Skipped: {$this->skippedCount}");
        $this->line("  Errors: {$this->errorCount}");

        return self::SUCCESS;
    }

    private function migratePages(bool $isDryRun): void
    {
        $pages = Page::whereNotNull('content')->get();
        $progressBar = $this->output->createProgressBar($pages->count());
        $progressBar->start();

        foreach ($pages as $page) {
            $content = $page->content;
            $hasChanges = false;

            if (! is_array($content)) {
                $progressBar->advance();
                continue;
            }

            foreach ($content as $index => $block) {
                if (! isset($block['type'], $block['data'])) {
                    continue;
                }

                if ($block['type'] === 'text' && isset($block['data']['body'])) {
                    $newBody = $this->migrateHtml($block['data']['body'], $isDryRun);
                    if ($newBody !== $block['data']['body']) {
                        $content[$index]['data']['body'] = $newBody;
                        $hasChanges = true;
                    }
                }
            }

            if ($hasChanges && ! $isDryRun) {
                $page->content = $content;
                $page->save();
            }

            $progressBar->advance();
        }

        $progressBar->finish();
    }

    private function migrateProducts(bool $isDryRun): void
    {
        $this->info('Product migration not implemented - descriptions use RichEditor without attachFiles');
    }

    private function migrateHtml(string $html, bool $isDryRun): string
    {
        return preg_replace_callback(
            self::STORAGE_URL_PATTERN,
            function ($matches) use ($isDryRun) {
                $fullUrl = $matches[0];
                $path = $matches[2];

                if (isset($this->urlToUuidMap[$path])) {
                    return "/__media__/{$this->urlToUuidMap[$path]}/original";
                }

                $uuid = $this->migrateFile($path, $isDryRun);

                if (! $uuid) {
                    return $fullUrl;
                }

                $this->urlToUuidMap[$path] = $uuid;

                return "/__media__/{$uuid}/original";
            },
            $html
        );
    }

    private function migrateFile(string $path, bool $isDryRun): ?string
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
            ]);

            $media = $mediaItem
                ->addMedia($fullPath)
                ->preservingOriginal()
                ->withCustomProperties([
                    'legacy_url' => "/storage/{$path}",
                    'source' => 'richtext-migration',
                ])
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

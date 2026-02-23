<?php

namespace App\Console\Commands;

use App\Domain\Media\MediaLibrary;
use App\Domain\Media\MediaRenameService;
use Illuminate\Console\Command;

/**
 * Rename media files to SEO-friendly file names.
 */
class MediaRenameSeo extends Command
{
    protected $signature = 'media:rename-seo
                            {--dry-run : Show what would be renamed without making changes}
                            {--force : Rename even if already has SEO name}
                            {--id=* : Only process specific media library IDs}';

    protected $description = 'Rename media files to SEO-friendly names based on title/alt text';

    /**
     * Create a new command instance.
     */
    public function __construct(
        private MediaRenameService $renameService
    ) {
        parent::__construct();
    }

    /**
     * Execute the media rename workflow.
     *
     * @return int Exit code (`Command::SUCCESS`).
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $ids = $this->option('id');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - no changes will be made');
            $this->newLine();
        }

        $query = MediaLibrary::with('media')
            ->whereNotNull('title')
            ->orWhereNotNull('alt_text');

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $items = $query->get();

        $this->info("Found {$items->count()} media items with title/alt text");

        $renamed = 0;
        $skipped = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($items->count());
        $progressBar->start();

        foreach ($items as $item) {
            $result = $this->processItem($item, $dryRun, $force);

            match ($result) {
                'renamed' => $renamed++,
                'skipped' => $skipped++,
                'failed' => $failed++,
            };

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Renamed', $renamed],
                ['Skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        if ($renamed > 0 && ! $dryRun) {
            $this->newLine();
            $this->info('Run `php artisan media:export` to update the manifest.');
        }

        return self::SUCCESS;
    }

    /**
     * Process one media library record.
     *
     * @param MediaLibrary $item Media item to evaluate.
     * @param bool $dryRun Whether to skip write operations.
     * @param bool $force Whether to rename already-compliant files.
     * @return string Processing status (`renamed`, `skipped`, or `failed`).
     */
    private function processItem(MediaLibrary $item, bool $dryRun, bool $force): string
    {
        $media = $item->getFirstMedia('default');

        if (! $media) {
            return 'skipped';
        }

        $expectedName = $this->renameService->getExpectedFileName($item);

        if (! $expectedName) {
            return 'skipped';
        }

        if (! $force && $media->file_name === $expectedName) {
            return 'skipped';
        }

        if ($dryRun) {
            $this->newLine();
            $this->line("  [{$item->id}] {$media->file_name} → {$expectedName}");

            return 'renamed';
        }

        $newName = $this->renameService->renameToSeo($item);

        return $newName ? 'renamed' : 'failed';
    }
}


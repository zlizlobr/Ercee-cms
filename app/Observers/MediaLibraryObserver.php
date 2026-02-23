<?php

namespace App\Observers;

use App\Domain\Media\MediaLibrary;
use App\Domain\Media\MediaRenameService;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Log;

/**
 * Coordinates media library side effects like SEO renaming and frontend rebuild triggering.
 */
class MediaLibraryObserver
{
    /**
     * @param MediaRenameService $renameService Service that renames stored media files to SEO-friendly names.
     */
    public function __construct(
        private MediaRenameService $renameService
    ) {}

    /**
     * Handle media library updates, including optional SEO rename and rebuild scheduling.
     *
     * @param MediaLibrary $item Media library record that was created or updated.
     */
    public function saved(MediaLibrary $item): void
    {
        if ($item->wasChanged(['title', 'alt_text'])) {
            $this->handleSeoRename($item);
        }

        foreach (FrontendRebuildRegistry::reasonsFor($item, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Handle media library deletion and enqueue dependent frontend rebuild reasons.
     *
     * @param MediaLibrary $item Media library record that was deleted.
     */
    public function deleted(MediaLibrary $item): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($item, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Attempt SEO filename rename when metadata changes and report the outcome to logs.
     *
     * @param MediaLibrary $item Media library record whose file may be renamed.
     */
    private function handleSeoRename(MediaLibrary $item): void
    {
        if (! config('services.media.seo_rename_enabled', true)) {
            return;
        }

        try {
            $newFileName = $this->renameService->renameToSeo($item);

            if ($newFileName) {
                Log::info('Media SEO rename completed via observer', [
                    'media_library_id' => $item->id,
                    'new_file_name' => $newFileName,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Media SEO rename failed in observer', [
                'media_library_id' => $item->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

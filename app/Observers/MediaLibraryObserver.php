<?php

namespace App\Observers;

use App\Domain\Media\MediaLibrary;
use App\Domain\Media\MediaRenameService;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Log;

class MediaLibraryObserver
{
    public function __construct(
        private MediaRenameService $renameService
    ) {}

    public function saved(MediaLibrary $item): void
    {
        if ($item->wasChanged(['title', 'alt_text'])) {
            $this->handleSeoRename($item);
        }

        foreach (FrontendRebuildRegistry::reasonsFor($item, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(MediaLibrary $item): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($item, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

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

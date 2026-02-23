<?php

namespace App\Observers;

use App\Domain\Media\Events\MediaUploaded;
use App\Domain\Media\Media;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

/**
 * Handles media model lifecycle events and dispatches downstream media/frontend side effects.
 */
class MediaObserver
{
    /**
     * Process media save events and trigger upload and frontend rebuild workflows.
     *
     * @param Media $media Media entity that was created or updated.
     */
    public function saved(Media $media): void
    {
        if ($media->wasRecentlyCreated) {
            MediaUploaded::dispatch($media);
        }

        foreach (FrontendRebuildRegistry::reasonsFor($media, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Process media deletion and enqueue dependent frontend rebuild reasons.
     *
     * @param Media $media Media entity that was deleted.
     */
    public function deleted(Media $media): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($media, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

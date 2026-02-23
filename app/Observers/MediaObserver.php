<?php

namespace App\Observers;

use App\Domain\Media\Events\MediaUploaded;
use App\Domain\Media\Media;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class MediaObserver
{
    public function saved(Media $media): void
    {
        if ($media->wasRecentlyCreated) {
            MediaUploaded::dispatch($media);
        }

        foreach (FrontendRebuildRegistry::reasonsFor($media, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Media $media): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($media, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}


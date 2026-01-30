<?php

namespace App\Observers;

use App\Domain\Content\Events\ContentPublished;
use App\Domain\Content\Page;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page): void
    {
        $this->clearCache($page);

        if ($page->status === Page::STATUS_PUBLISHED && $page->wasChanged('status')) {
            ContentPublished::dispatch($page);
        }

        foreach (FrontendRebuildRegistry::reasonsFor($page, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Page $page): void
    {
        $this->clearCache($page);

        foreach (FrontendRebuildRegistry::reasonsFor($page, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    protected function clearCache(Page $page): void
    {
        Cache::forget("page:{$page->slug}");
        Cache::forget('pages:slugs');
    }
}

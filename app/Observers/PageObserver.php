<?php

namespace App\Observers;

use App\Domain\Content\Page;
use App\Jobs\TriggerFrontendRebuildJob;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page): void
    {
        $this->clearCache($page);

        if ($page->isPublished()) {
            TriggerFrontendRebuildJob::dispatch("page_updated:{$page->slug}");
        }
    }

    public function deleted(Page $page): void
    {
        $this->clearCache($page);
        TriggerFrontendRebuildJob::dispatch("page_deleted:{$page->slug}");
    }

    protected function clearCache(Page $page): void
    {
        Cache::forget("page:{$page->slug}");
        Cache::forget('pages:slugs');
    }
}

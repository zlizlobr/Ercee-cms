<?php

namespace App\Observers;

use App\Domain\Content\Events\ContentPublished;
use App\Domain\Content\Page;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * Handles page lifecycle side effects including cache invalidation, publish events, and rebuild scheduling.
 */
class PageObserver
{
    /**
     * Ensure publish timestamp is set when a page is saved as published.
     *
     * @param Page $page Page entity being persisted.
     */
    public function saving(Page $page): void
    {
        if ($page->status === Page::STATUS_PUBLISHED && blank($page->published_at)) {
            $page->published_at = now();
        }
    }

    /**
     * Handle page save events and trigger publish/rebuild flows when relevant.
     *
     * @param Page $page Page entity that was created or updated.
     */
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

    /**
     * Handle page deletion by clearing page cache and scheduling frontend rebuild reasons.
     *
     * @param Page $page Page entity that was deleted.
     */
    public function deleted(Page $page): void
    {
        $this->clearCache($page);

        foreach (FrontendRebuildRegistry::reasonsFor($page, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Invalidate cache entries affected by changes to the given page.
     *
     * @param Page $page Page entity used to resolve slug-scoped cache keys.
     */
    protected function clearCache(Page $page): void
    {
        Cache::forget("page:{$page->slug}");
        Cache::forget('pages:slugs');
    }
}

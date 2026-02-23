<?php

namespace App\Observers;

use App\Domain\Content\CookieSetting;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * Reacts to cookie setting lifecycle changes by clearing cache and scheduling frontend rebuilds.
 */
class CookieSettingObserver
{
    /**
     * Handle persisted cookie setting changes and enqueue dependent frontend rebuild reasons.
     *
     * @param CookieSetting $cookieSetting Cookie setting entity that was created or updated.
     */
    public function saved(CookieSetting $cookieSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($cookieSetting, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Handle deleted cookie setting changes and enqueue dependent frontend rebuild reasons.
     *
     * @param CookieSetting $cookieSetting Cookie setting entity that was deleted.
     */
    public function deleted(CookieSetting $cookieSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($cookieSetting, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Remove the cached cookie settings snapshot used by frontend configuration.
     */
    protected function clearCache(): void
    {
        Cache::forget(CookieSetting::CACHE_KEY);
    }
}

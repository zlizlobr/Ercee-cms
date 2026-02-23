<?php

namespace App\Observers;

use App\Domain\Content\ThemeSetting;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * Reacts to theme setting lifecycle changes by clearing cache and scheduling frontend rebuilds.
 */
class ThemeSettingObserver
{
    /**
     * Handle persisted theme setting changes and enqueue dependent frontend rebuild reasons.
     *
     * @param ThemeSetting $themeSetting Theme setting entity that was created or updated.
     */
    public function saved(ThemeSetting $themeSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($themeSetting, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Handle deleted theme setting changes and enqueue dependent frontend rebuild reasons.
     *
     * @param ThemeSetting $themeSetting Theme setting entity that was deleted.
     */
    public function deleted(ThemeSetting $themeSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($themeSetting, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Remove the cached theme settings snapshot used by frontend configuration.
     */
    protected function clearCache(): void
    {
        Cache::forget(ThemeSetting::CACHE_KEY);
    }
}

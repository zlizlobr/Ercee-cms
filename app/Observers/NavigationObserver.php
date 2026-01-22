<?php

namespace App\Observers;

use App\Domain\Content\Navigation;
use App\Jobs\TriggerFrontendRebuildJob;
use Illuminate\Support\Facades\Cache;

/**
 * Observes navigation changes to invalidate caches and trigger rebuilds.
 */
class NavigationObserver
{
    /**
     * Handle the Navigation "saved" event.
     *
     * @param Navigation $navigation
     * @return void
     */
    public function saved(Navigation $navigation): void
    {
        $this->clearCache($navigation);
        TriggerFrontendRebuildJob::dispatch('navigation_updated');
    }

    /**
     * Handle the Navigation "deleted" event.
     *
     * @param Navigation $navigation
     * @return void
     */
    public function deleted(Navigation $navigation): void
    {
        $this->clearCache($navigation);
        TriggerFrontendRebuildJob::dispatch('navigation_deleted');
    }

    /**
     * Clear cached navigation data for the affected menu.
     *
     * @param Navigation $navigation
     * @return void
     */
    protected function clearCache(Navigation $navigation): void
    {
        // Clear legacy cache key
        Cache::forget('navigation:tree');

        // Clear menu-specific cache
        if ($navigation->menu) {
            Cache::forget("navigation:{$navigation->menu->slug}");
            Cache::forget("menu:{$navigation->menu->slug}");
        }

        // Clear default main menu cache
        Cache::forget('navigation:main');
        Cache::forget('menu:main');
    }
}

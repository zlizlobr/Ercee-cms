<?php

namespace App\Observers;

use App\Domain\Content\Navigation;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * Observes navigation changes to invalidate caches and trigger rebuilds.
 */
class NavigationObserver
{
    /**
     * Handle the Navigation "saved" event.
     *
     * @param Navigation $navigation Navigation entity that was created or updated.
     */
    public function saved(Navigation $navigation): void
    {
        $this->clearCache($navigation);

        foreach (FrontendRebuildRegistry::reasonsFor($navigation, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Handle the Navigation "deleted" event.
     *
     * @param Navigation $navigation Navigation entity that was deleted.
     */
    public function deleted(Navigation $navigation): void
    {
        $this->clearCache($navigation);

        foreach (FrontendRebuildRegistry::reasonsFor($navigation, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Clear cached navigation data for the affected menu.
     *
     * @param Navigation $navigation Navigation entity used to resolve menu-scoped cache keys.
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

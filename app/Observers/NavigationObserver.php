<?php

namespace App\Observers;

use App\Domain\Content\Navigation;
use App\Jobs\TriggerFrontendRebuildJob;
use Illuminate\Support\Facades\Cache;

class NavigationObserver
{
    public function saved(Navigation $navigation): void
    {
        $this->clearCache($navigation);
        TriggerFrontendRebuildJob::dispatch('navigation_updated');
    }

    public function deleted(Navigation $navigation): void
    {
        $this->clearCache($navigation);
        TriggerFrontendRebuildJob::dispatch('navigation_deleted');
    }

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

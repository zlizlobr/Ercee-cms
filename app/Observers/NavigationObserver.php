<?php

namespace App\Observers;

use App\Domain\Content\Navigation;
use App\Jobs\TriggerFrontendRebuildJob;
use Illuminate\Support\Facades\Cache;

class NavigationObserver
{
    public function saved(Navigation $navigation): void
    {
        $this->clearCache();
        TriggerFrontendRebuildJob::dispatch('navigation_updated');
    }

    public function deleted(Navigation $navigation): void
    {
        $this->clearCache();
        TriggerFrontendRebuildJob::dispatch('navigation_deleted');
    }

    protected function clearCache(): void
    {
        Cache::forget('navigation:tree');
    }
}

<?php

namespace App\Observers;

use App\Domain\Content\CookieSetting;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

class CookieSettingObserver
{
    public function saved(CookieSetting $cookieSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($cookieSetting, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(CookieSetting $cookieSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($cookieSetting, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    protected function clearCache(): void
    {
        Cache::forget(CookieSetting::CACHE_KEY);
    }
}

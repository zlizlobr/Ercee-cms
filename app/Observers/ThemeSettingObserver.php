<?php

namespace App\Observers;

use App\Domain\Content\ThemeSetting;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;
use Illuminate\Support\Facades\Cache;

class ThemeSettingObserver
{
    public function saved(ThemeSetting $themeSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($themeSetting, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(ThemeSetting $themeSetting): void
    {
        $this->clearCache();

        foreach (FrontendRebuildRegistry::reasonsFor($themeSetting, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    protected function clearCache(): void
    {
        Cache::forget(ThemeSetting::CACHE_KEY);
    }
}


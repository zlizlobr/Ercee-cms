<?php

namespace App\Observers;

use App\Domain\Content\ThemeSetting;
use App\Jobs\TriggerFrontendRebuildJob;
use Illuminate\Support\Facades\Cache;

class ThemeSettingObserver
{
    public function saved(ThemeSetting $themeSetting): void
    {
        $this->clearCache();
        TriggerFrontendRebuildJob::dispatch('theme_settings_updated');
    }

    public function deleted(ThemeSetting $themeSetting): void
    {
        $this->clearCache();
        TriggerFrontendRebuildJob::dispatch('theme_settings_deleted');
    }

    protected function clearCache(): void
    {
        Cache::forget(ThemeSetting::CACHE_KEY);
    }
}

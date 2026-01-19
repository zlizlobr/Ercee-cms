<?php

namespace App\Observers;

use App\Domain\Content\Navigation;
use Illuminate\Support\Facades\Cache;

class NavigationObserver
{
    public function saved(Navigation $navigation): void
    {
        $this->clearCache();
    }

    public function deleted(Navigation $navigation): void
    {
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::forget('navigation:tree');
    }
}

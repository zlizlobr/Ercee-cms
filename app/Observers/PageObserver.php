<?php

namespace App\Observers;

use App\Domain\Content\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page): void
    {
        $this->clearCache($page);
    }

    public function deleted(Page $page): void
    {
        $this->clearCache($page);
    }

    protected function clearCache(Page $page): void
    {
        Cache::forget("page:{$page->slug}");
    }
}

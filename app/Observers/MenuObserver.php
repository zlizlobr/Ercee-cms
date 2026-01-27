<?php

namespace App\Observers;

use App\Domain\Content\Menu;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class MenuObserver
{
    public function saved(Menu $menu): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($menu, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Menu $menu): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($menu, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

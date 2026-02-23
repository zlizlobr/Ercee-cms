<?php

namespace App\Observers;

use App\Domain\Content\Events\MenuUpdated;
use App\Domain\Content\Menu;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

/**
 * Reacts to menu changes by broadcasting update events and scheduling frontend rebuilds.
 */
class MenuObserver
{
    /**
     * Handle menu save events and enqueue dependent frontend rebuild reasons.
     *
     * @param Menu $menu Menu entity that was created or updated.
     */
    public function saved(Menu $menu): void
    {
        MenuUpdated::dispatch($menu);

        foreach (FrontendRebuildRegistry::reasonsFor($menu, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    /**
     * Handle menu deletion and enqueue dependent frontend rebuild reasons.
     *
     * @param Menu $menu Menu entity that was deleted.
     */
    public function deleted(Menu $menu): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($menu, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

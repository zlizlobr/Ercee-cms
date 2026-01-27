<?php

namespace App\Observers;

use App\Domain\Commerce\Taxonomy;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class TaxonomyObserver
{
    public function saved(Taxonomy $taxonomy): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($taxonomy, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Taxonomy $taxonomy): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($taxonomy, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

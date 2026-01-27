<?php

namespace App\Observers;

use App\Domain\Commerce\Attribute;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class AttributeObserver
{
    public function saved(Attribute $attribute): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($attribute, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Attribute $attribute): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($attribute, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

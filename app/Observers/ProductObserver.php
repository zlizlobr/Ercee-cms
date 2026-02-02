<?php

namespace App\Observers;

use Modules\Commerce\Domain\Product;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class ProductObserver
{
    public function saved(Product $product): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($product, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Product $product): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($product, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

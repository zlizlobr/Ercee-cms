<?php

namespace App\Observers;

use App\Domain\Commerce\ProductReview;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class ProductReviewObserver
{
    public function saved(ProductReview $review): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($review, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(ProductReview $review): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($review, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

<?php

namespace App\Observers;

use App\Domain\Form\Form;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class FormObserver
{
    public function saved(Form $form): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($form, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Form $form): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($form, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}

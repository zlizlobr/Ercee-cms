<?php

namespace App\Domain\Funnel\StepExecutors;

use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\FunnelStep;
use App\Domain\Subscriber\Subscriber;

interface StepExecutorInterface
{
    public function execute(FunnelStep $step, FunnelRun $run, Subscriber $subscriber): array;
}

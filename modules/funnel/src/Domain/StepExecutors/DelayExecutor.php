<?php

namespace Modules\Funnel\Domain\StepExecutors;

use Modules\Funnel\Domain\FunnelRun;
use Modules\Funnel\Domain\FunnelStep;
use App\Domain\Subscriber\Subscriber;

class DelayExecutor implements StepExecutorInterface
{
    public function execute(FunnelStep $step, FunnelRun $run, Subscriber $subscriber): array
    {
        $seconds = $step->config['seconds'] ?? 0;

        return [
            'delay' => $seconds,
            'payload' => [
                'delay_seconds' => $seconds,
            ],
        ];
    }
}

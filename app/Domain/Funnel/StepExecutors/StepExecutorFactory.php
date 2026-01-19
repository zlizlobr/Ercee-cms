<?php

namespace App\Domain\Funnel\StepExecutors;

use App\Domain\Funnel\FunnelStep;
use InvalidArgumentException;

class StepExecutorFactory
{
    protected array $executors = [
        FunnelStep::TYPE_DELAY => DelayExecutor::class,
        FunnelStep::TYPE_EMAIL => EmailExecutor::class,
        FunnelStep::TYPE_WEBHOOK => WebhookExecutor::class,
        FunnelStep::TYPE_TAG => TagExecutor::class,
    ];

    public function make(string $type): StepExecutorInterface
    {
        if (! isset($this->executors[$type])) {
            throw new InvalidArgumentException("Unknown step type: {$type}");
        }

        return app($this->executors[$type]);
    }
}

<?php

namespace App\Domain\Funnel\StepExecutors;

use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\FunnelStep;
use App\Domain\Subscriber\Subscriber;

class TagExecutor implements StepExecutorInterface
{
    public function execute(FunnelStep $step, FunnelRun $run, Subscriber $subscriber): array
    {
        $tag = $step->config['tag'] ?? null;

        if ($tag) {
            $subscriber->addTag($tag);
        }

        return [
            'payload' => [
                'tag' => $tag,
                'subscriber_id' => $subscriber->id,
            ],
        ];
    }
}

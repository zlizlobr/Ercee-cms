<?php

namespace Modules\Funnel\Domain\StepExecutors;

use Modules\Funnel\Domain\FunnelRun;
use Modules\Funnel\Domain\FunnelStep;
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

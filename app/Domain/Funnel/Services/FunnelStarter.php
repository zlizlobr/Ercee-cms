<?php

namespace App\Domain\Funnel\Services;

use App\Domain\Funnel\Funnel;
use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\Jobs\ExecuteFunnelStepJob;
use App\Domain\Subscriber\Subscriber;

class FunnelStarter
{
    public function startByTrigger(string $triggerType, Subscriber $subscriber): array
    {
        $funnels = Funnel::active()
            ->byTrigger($triggerType)
            ->with('steps')
            ->get();

        $runs = [];

        foreach ($funnels as $funnel) {
            if ($funnel->steps->isEmpty()) {
                continue;
            }

            $run = $this->createRun($funnel, $subscriber);
            $runs[] = $run;

            $this->dispatchFirstStep($run);
        }

        return $runs;
    }

    public function startManually(Funnel $funnel, Subscriber $subscriber): ?FunnelRun
    {
        if (! $funnel->active || $funnel->steps->isEmpty()) {
            return null;
        }

        $run = $this->createRun($funnel, $subscriber);
        $this->dispatchFirstStep($run);

        return $run;
    }

    protected function createRun(Funnel $funnel, Subscriber $subscriber): FunnelRun
    {
        return FunnelRun::create([
            'funnel_id' => $funnel->id,
            'subscriber_id' => $subscriber->id,
            'status' => FunnelRun::STATUS_RUNNING,
            'current_step' => 0,
            'started_at' => now(),
        ]);
    }

    protected function dispatchFirstStep(FunnelRun $run): void
    {
        ExecuteFunnelStepJob::dispatch($run->id, 0);
    }
}

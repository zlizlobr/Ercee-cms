<?php

namespace Modules\Funnel\Domain\Jobs;

use Modules\Funnel\Domain\FunnelRun;
use Modules\Funnel\Domain\FunnelRunStep;
use Modules\Funnel\Domain\FunnelStep;
use Modules\Funnel\Domain\StepExecutors\StepExecutorFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteFunnelStepJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public int $funnelRunId,
        public int $stepPosition
    ) {}

    public function handle(StepExecutorFactory $executorFactory): void
    {
        $run = FunnelRun::with(['funnel.steps', 'subscriber'])->find($this->funnelRunId);

        if (! $run || $run->status !== FunnelRun::STATUS_RUNNING) {
            return;
        }

        $step = $run->funnel->steps->firstWhere('position', $this->stepPosition);

        if (! $step) {
            $run->markAsCompleted();

            return;
        }

        // Idempotency check
        $existingRunStep = FunnelRunStep::where('funnel_run_id', $run->id)
            ->where('funnel_step_id', $step->id)
            ->where('status', FunnelRunStep::STATUS_SUCCESS)
            ->exists();

        if ($existingRunStep) {
            $this->scheduleNextStep($run, $step);

            return;
        }

        $runStep = FunnelRunStep::firstOrCreate([
            'funnel_run_id' => $run->id,
            'funnel_step_id' => $step->id,
        ], [
            'status' => FunnelRunStep::STATUS_PENDING,
        ]);

        try {
            $executor = $executorFactory->make($step->type);
            $result = $executor->execute($step, $run, $run->subscriber);

            $runStep->markAsSuccess($result['payload'] ?? []);

            $run->update(['current_step' => $this->stepPosition]);

            if ($result['delay'] ?? false) {
                self::dispatch($this->funnelRunId, $this->stepPosition + 1)
                    ->delay(now()->addSeconds($result['delay']));
            } else {
                $this->scheduleNextStep($run, $step);
            }
        } catch (\Throwable $e) {
            Log::error('Funnel step execution failed', [
                'funnel_run_id' => $run->id,
                'step_id' => $step->id,
                'error' => $e->getMessage(),
            ]);

            $runStep->markAsFailed($e->getMessage());
            $run->markAsFailed();
        }
    }

    protected function scheduleNextStep(FunnelRun $run, FunnelStep $currentStep): void
    {
        $nextStep = $run->funnel->steps
            ->where('position', '>', $currentStep->position)
            ->sortBy('position')
            ->first();

        if ($nextStep) {
            self::dispatch($this->funnelRunId, $nextStep->position);
        } else {
            $run->markAsCompleted();
        }
    }

    public function uniqueId(): string
    {
        return "funnel_run_{$this->funnelRunId}_step_{$this->stepPosition}";
    }
}

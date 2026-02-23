<?php

namespace App\Jobs;

use App\Infrastructure\Frontend\FrontendRebuildService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TriggerFrontendRebuildJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int Maximum number of retry attempts for this queued job.
     */
    public int $tries = 3;

    /**
     * @var int Delay strategy in seconds between job retry attempts.
     */
    public int $backoff = 30;

    /**
     * @var int Duration in seconds for which unique job lock is held.
     */
    public int $uniqueFor = 60;

    public function __construct(
        public string $reason
    ) {}

    public function handle(FrontendRebuildService $rebuildService): void
    {
        if (! $rebuildService->isEnabled()) {
            Log::debug('Frontend rebuild skipped (disabled)', [
                'reason' => $this->reason,
            ]);

            return;
        }

        $lockKey = 'frontend_rebuild_lock';

        if (Cache::has($lockKey)) {
            Log::info('Frontend rebuild skipped (debounced)', [
                'reason' => $this->reason,
                'mode' => $rebuildService->getMode(),
            ]);

            return;
        }

        Cache::put($lockKey, true, 30);

        try {
            $rebuildService->trigger($this->reason);

            Log::info('Frontend rebuild job completed', [
                'reason' => $this->reason,
                'mode' => $rebuildService->getMode(),
            ]);
        } catch (\Exception $e) {
            Cache::forget($lockKey);

            Log::error('Frontend rebuild job failed', [
                'reason' => $this->reason,
                'mode' => $rebuildService->getMode(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function uniqueId(): string
    {
        return 'frontend_rebuild';
    }
}


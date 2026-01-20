<?php

namespace App\Jobs;

use App\Infrastructure\GitHub\GitHubDispatchService;
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

    public int $tries = 3;

    public int $backoff = 30;

    public int $uniqueFor = 60;

    public function __construct(
        public string $reason
    ) {}

    public function handle(GitHubDispatchService $gitHubDispatch): void
    {
        $lockKey = 'frontend_rebuild_lock';

        if (Cache::has($lockKey)) {
            Log::info('Frontend rebuild skipped (debounced)', [
                'reason' => $this->reason,
            ]);

            return;
        }

        Cache::put($lockKey, true, 30);

        try {
            $gitHubDispatch->triggerFrontendBuild($this->reason);

            Log::info('Frontend rebuild job completed', [
                'reason' => $this->reason,
            ]);
        } catch (\Exception $e) {
            Cache::forget($lockKey);

            Log::error('Frontend rebuild job failed', [
                'reason' => $this->reason,
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

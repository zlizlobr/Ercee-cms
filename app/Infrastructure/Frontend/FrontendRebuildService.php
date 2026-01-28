<?php

namespace App\Infrastructure\Frontend;

use App\Infrastructure\GitHub\GitHubDispatchService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class FrontendRebuildService
{
    private bool $enabled;

    private string $mode;

    private ?string $localPath;

    public function __construct(
        private GitHubDispatchService $gitHubDispatch
    ) {
        $this->enabled = config('services.frontend.rebuild_enabled', true);
        $this->mode = config('services.frontend.rebuild_mode', 'github');
        $this->localPath = config('services.frontend.local_frontend_path');
    }

    public function trigger(string $reason): void
    {
        if (! $this->enabled) {
            Log::info('Frontend rebuild disabled', ['reason' => $reason]);

            return;
        }

        match ($this->mode) {
            'github' => $this->triggerGitHub($reason),
            'local' => $this->triggerLocal($reason),
            'log' => $this->triggerLog($reason),
            default => $this->triggerDisabled($reason),
        };
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    private function triggerGitHub(string $reason): void
    {
        $this->gitHubDispatch->triggerFrontendBuild($reason);

        Log::info('Frontend rebuild triggered via GitHub', [
            'reason' => $reason,
            'mode' => 'github',
        ]);
    }

    private function triggerLocal(string $reason): void
    {
        if (empty($this->localPath)) {
            Log::warning('Frontend local path not configured, skipping local rebuild', [
                'reason' => $reason,
            ]);

            return;
        }

        $syncScript = $this->localPath.'/scripts/sync-media.sh';

        if (! file_exists($syncScript)) {
            Log::warning('Frontend sync script not found', [
                'path' => $syncScript,
                'reason' => $reason,
            ]);

            return;
        }

        try {
            $result = Process::path($this->localPath)
                ->timeout(120)
                ->run(['bash', $syncScript]);

            if ($result->successful()) {
                Log::info('Frontend local sync completed', [
                    'reason' => $reason,
                    'mode' => 'local',
                    'output' => $result->output(),
                ]);
            } else {
                Log::warning('Frontend local sync failed', [
                    'reason' => $reason,
                    'error' => $result->errorOutput(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Frontend local sync exception', [
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function triggerLog(string $reason): void
    {
        Log::info('Frontend rebuild would be triggered (log mode)', [
            'reason' => $reason,
            'mode' => 'log',
            'message' => 'In production, this would trigger a GitHub dispatch',
        ]);
    }

    private function triggerDisabled(string $reason): void
    {
        Log::debug('Frontend rebuild skipped (disabled mode)', [
            'reason' => $reason,
            'mode' => $this->mode,
        ]);
    }
}

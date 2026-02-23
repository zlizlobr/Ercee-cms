<?php

namespace App\Infrastructure\Frontend;

use App\Infrastructure\GitHub\GitHubDispatchService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * Orchestrates frontend rebuild triggers across configured execution modes.
 */
class FrontendRebuildService
{
    /**
     * @var bool Flag that determines whether frontend rebuild triggering is active.
     */
    private bool $enabled;

    /**
     * @var string Configured rebuild execution mode for frontend synchronization.
     */
    private string $mode;

    /**
     * @var ?string Local filesystem path used for file-based rebuild triggering.
     */
    private ?string $localPath;

    /**
     * @param GitHubDispatchService $gitHubDispatch Service that publishes repository dispatch events to GitHub.
     */
    public function __construct(
        private GitHubDispatchService $gitHubDispatch
    ) {
        $this->enabled = config('services.frontend.rebuild_enabled', true);
        $this->mode = config('services.frontend.rebuild_mode', 'github');
        $this->localPath = config('services.frontend.local_frontend_path');
    }

    /**
     * Triggers frontend rebuild according to configured mode and runtime guard flags.
     *
     * @param string $reason Business reason attached to logs and dispatch payloads.
     */
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

    /**
     * Indicates whether frontend rebuild integration is currently enabled by configuration.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns the active frontend rebuild mode used by trigger routing.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Triggers rebuild through GitHub repository dispatch integration.
     *
     * @param string $reason Business reason attached to audit logs.
     */
    private function triggerGitHub(string $reason): void
    {
        $this->gitHubDispatch->triggerFrontendBuild($reason);

        Log::info('Frontend rebuild triggered via GitHub', [
            'reason' => $reason,
            'mode' => 'github',
        ]);
    }

    /**
     * Triggers local frontend media sync script when local integration path is configured.
     *
     * @param string $reason Business reason attached to local sync logs.
     */
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

    /**
     * Emits informational log entry instead of triggering real rebuild side effects.
     *
     * @param string $reason Business reason included in diagnostic log context.
     */
    private function triggerLog(string $reason): void
    {
        Log::info('Frontend rebuild would be triggered (log mode)', [
            'reason' => $reason,
            'mode' => 'log',
            'message' => 'In production, this would trigger a GitHub dispatch',
        ]);
    }

    /**
     * Emits debug log for unknown or disabled trigger mode.
     *
     * @param string $reason Business reason included in skip log context.
     */
    private function triggerDisabled(string $reason): void
    {
        Log::debug('Frontend rebuild skipped (disabled mode)', [
            'reason' => $reason,
            'mode' => $this->mode,
        ]);
    }
}

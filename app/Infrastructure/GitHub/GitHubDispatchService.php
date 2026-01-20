<?php

namespace App\Infrastructure\GitHub;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubDispatchService
{
    private string $token;

    private string $repository;

    public function __construct()
    {
        $this->token = config('services.github.token', '');
        $this->repository = config('services.github.frontend_repository', '');
    }

    public function triggerFrontendBuild(string $reason = 'content_update'): void
    {
        if (empty($this->token) || empty($this->repository)) {
            throw new \RuntimeException(
                'GitHub token or repository not configured. Set GITHUB_TOKEN and GITHUB_FRONTEND_REPOSITORY in .env'
            );
        }

        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'Authorization' => "Bearer {$this->token}",
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->post("https://api.github.com/repos/{$this->repository}/dispatches", [
            'event_type' => 'frontend_rebuild',
            'client_payload' => [
                'reason' => $reason,
                'triggered_at' => now()->toIso8601String(),
                'source' => 'ercee-cms',
            ],
        ]);

        if ($response->failed()) {
            Log::error('GitHub dispatch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'repository' => $this->repository,
            ]);

            throw new \RuntimeException(
                "GitHub dispatch failed with status {$response->status()}: {$response->body()}"
            );
        }

        Log::info('GitHub dispatch successful', [
            'repository' => $this->repository,
            'reason' => $reason,
        ]);
    }
}

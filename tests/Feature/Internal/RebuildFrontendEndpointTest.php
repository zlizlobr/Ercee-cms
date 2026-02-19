<?php

namespace Tests\Feature\Internal;

use App\Infrastructure\GitHub\GitHubDispatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Contract tests for internal frontend rebuild trigger endpoint.
 *
 * These tests focus on security boundary and side-effect execution:
 * - unauthorized callers are rejected,
 * - valid token dispatches rebuild exactly once,
 * - response payload preserves the triggering reason.
 */
class RebuildFrontendEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure endpoint rejects requests without internal API token.
     *
     * Note:
     * Service call is explicitly blocked to verify there is no accidental
     * side effect when auth fails.
     */
    public function test_rebuild_endpoint_requires_api_token(): void
    {
        config()->set('services.api.internal_token', 'secret-token');

        $mockService = Mockery::mock(GitHubDispatchService::class);
        $mockService->shouldNotReceive('triggerFrontendBuild');
        $this->app->instance(GitHubDispatchService::class, $mockService);

        $this->postJson('/api/internal/rebuild-frontend', ['reason' => 'test'])
            ->assertStatus(401)
            ->assertJsonPath('error', 'Unauthorized');
    }

    /**
     * Ensure endpoint rejects requests with wrong bearer token.
     *
     * Note:
     * This protects against token guessing and validates strict token equality.
     */
    public function test_rebuild_endpoint_rejects_invalid_token(): void
    {
        config()->set('services.api.internal_token', 'secret-token');

        $mockService = Mockery::mock(GitHubDispatchService::class);
        $mockService->shouldNotReceive('triggerFrontendBuild');
        $this->app->instance(GitHubDispatchService::class, $mockService);

        $this->withHeader('Authorization', 'Bearer wrong-token')
            ->postJson('/api/internal/rebuild-frontend', ['reason' => 'test'])
            ->assertStatus(401)
            ->assertJsonPath('error', 'Unauthorized');
    }

    /**
     * Ensure endpoint dispatches frontend rebuild for authorized calls.
     *
     * Note:
     * This verifies both behavior (service invocation) and API contract
     * (`data.reason` echo) for automation clients.
     */
    public function test_rebuild_endpoint_dispatches_frontend_build_with_valid_token(): void
    {
        config()->set('services.api.internal_token', 'secret-token');

        $mockService = Mockery::mock(GitHubDispatchService::class);
        $mockService->shouldReceive('triggerFrontendBuild')
            ->once()
            ->with('content-update');
        $this->app->instance(GitHubDispatchService::class, $mockService);

        $this->withHeader('Authorization', 'Bearer secret-token')
            ->postJson('/api/internal/rebuild-frontend', ['reason' => 'content-update'])
            ->assertStatus(200)
            ->assertJsonPath('data.reason', 'content-update');
    }
}

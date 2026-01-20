<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\GitHub\GitHubDispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RebuildFrontendController extends Controller
{
    public function __construct(
        private readonly GitHubDispatchService $gitHubDispatch
    ) {}

    public function rebuild(Request $request): JsonResponse
    {
        $token = $request->header('X-Rebuild-Token');
        $expectedToken = config('services.frontend.rebuild_token');

        if (empty($expectedToken)) {
            Log::warning('Frontend rebuild attempted but FRONTEND_REBUILD_TOKEN is not configured');

            return response()->json([
                'error' => 'Rebuild token not configured',
            ], 500);
        }

        if (! hash_equals($expectedToken, $token ?? '')) {
            Log::warning('Frontend rebuild attempted with invalid token', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Invalid token',
            ], 401);
        }

        $reason = $request->input('reason', 'manual');

        try {
            $this->gitHubDispatch->triggerFrontendBuild($reason);

            Log::info('Frontend rebuild triggered', [
                'reason' => $reason,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Frontend rebuild triggered successfully',
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to trigger frontend rebuild', [
                'error' => $e->getMessage(),
                'reason' => $reason,
            ]);

            return response()->json([
                'error' => 'Failed to trigger rebuild',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

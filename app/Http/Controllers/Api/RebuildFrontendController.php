<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RebuildFrontendRequest;
use App\Infrastructure\GitHub\GitHubDispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RebuildFrontendController extends Controller
{
    public function __construct(
        private readonly GitHubDispatchService $gitHubDispatch
    ) {}

    public function rebuild(RebuildFrontendRequest $request): JsonResponse
    {
        $reason = $request->input('reason', 'manual');

        try {
            $this->gitHubDispatch->triggerFrontendBuild($reason);

            Log::info('Frontend rebuild triggered', [
                'reason' => $reason,
                'ip' => $request->ip(),
                'request_id' => $request->attributes->get('request_id'),
            ]);

            return response()->json([
                'data' => [
                    'reason' => $reason,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to trigger frontend rebuild', [
                'error' => $e->getMessage(),
                'reason' => $reason,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Failed to trigger rebuild',
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Domain\Media\MediaManifestService;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Provide read-only access to the public media manifest.
 */
class MediaController extends ApiController
{
    public function __construct(
        private readonly MediaManifestService $manifestService,
    ) {}

    /**
     * Return the full media manifest in API format.
     */
    public function index(): JsonResponse
    {
        return $this->safeGet(function () {
            $manifest = $this->manifestService->getManifest();

            $data = collect($manifest)->map(
                fn ($entry) => $this->manifestService->toApiFormat($entry)
            )->values();

            return response()->json([
                'data' => $data,
            ]);
        });
    }

    /**
     * Return a single media entry by UUID.
     */
    public function show(string $uuid): JsonResponse
    {
        return $this->safeGet(function () use ($uuid) {
            $entry = $this->manifestService->getByUuid($uuid);

            if (! $entry) {
                return response()->json([
                    'error' => 'Media not found',
                ], 404);
            }

            return response()->json([
                'data' => $this->manifestService->toApiFormat($entry),
            ]);
        });
    }

    /**
     * Resolve media entries by numeric IDs.
     */
    public function resolve(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = $request->input('ids');
        $resolved = $this->manifestService->resolveMediaIds($ids);

        $data = collect($resolved)->map(
            fn ($entry) => $this->manifestService->toApiFormat($entry)
        );

        return response()->json([
            'data' => $data,
        ]);
    }
}


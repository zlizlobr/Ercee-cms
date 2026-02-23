<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replay cached responses for repeated requests with the same idempotency key.
 */
class IdempotencyKey
{
    protected const CACHE_TTL = 86400; // 24 hours

    /**
     * Cache successful and client-error responses by request fingerprint.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        if (empty($idempotencyKey)) {
            return $next($request);
        }

        $cacheKey = $this->buildCacheKey($request, $idempotencyKey);

        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse !== null) {
            return response()->json(
                $cachedResponse['body'],
                $cachedResponse['status'],
                ['X-Idempotent-Replay' => 'true']
            );
        }

        $response = $next($request);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 500) {
            $this->cacheResponse($cacheKey, $response);
        }

        return $response;
    }

    /**
     * Build cache key from caller, path and provided idempotency key.
     */
    protected function buildCacheKey(Request $request, string $idempotencyKey): string
    {
        return sprintf(
            'idempotency:%s:%s:%s',
            $request->ip(),
            $request->path(),
            hash('sha256', $idempotencyKey)
        );
    }

    /**
     * Persist response payload for future idempotent replays.
     */
    protected function cacheResponse(string $cacheKey, Response $response): void
    {
        $body = json_decode($response->getContent(), true);

        Cache::put($cacheKey, [
            'status' => $response->getStatusCode(),
            'body' => $body,
        ], self::CACHE_TTL);
    }
}

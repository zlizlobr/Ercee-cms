<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attach request ID and audit-log selected API calls.
 */
class ApiAuditLog
{
    /**
     * Add request ID and write audit log entry for critical or failed API requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = Str::uuid()->toString();
        $request->attributes->set('request_id', $requestId);

        $response = $next($request);

        $this->logRequest($request, $response, $requestId);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }

    /**
     * Build and emit API audit context for selected requests.
     */
    protected function logRequest(Request $request, Response $response, string $requestId): void
    {
        $context = [
            'request_id' => $requestId,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'user_agent' => $request->userAgent(),
        ];

        if ($request->bearerToken() || $request->header('X-Api-Token')) {
            $context['auth_type'] = 'token';
        }

        $isSuccess = $response->getStatusCode() >= 200 && $response->getStatusCode() < 400;
        $isCriticalPath = $this->isCriticalPath($request->path());

        if ($isCriticalPath || ! $isSuccess) {
            $logLevel = $isSuccess ? 'info' : 'warning';
            Log::channel('api')->$logLevel('API request', $context);
        }
    }

    /**
     * Determine whether request path should always be audit-logged.
     */
    protected function isCriticalPath(string $path): bool
    {
        $criticalPatterns = [
            'checkout',
            'webhooks',
            'internal',
            'forms/*/submit',
        ];

        foreach ($criticalPatterns as $pattern) {
            if (Str::is('*'.$pattern.'*', $path)) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PublicApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $expectedToken = config('services.api.public_token');

        if (empty($expectedToken)) {
            return response()->json([
                'error' => 'Public API token not configured',
            ], 500);
        }

        if (empty($token) || ! hash_equals($expectedToken, $token)) {
            $tokenName = config('services.api.public_token_name', 'public-api');

            Log::warning('Public API authentication failed', [
                'token_name' => $tokenName,
                'ip' => $request->ip(),
                'path' => $request->path(),
                'method' => $request->method(),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}


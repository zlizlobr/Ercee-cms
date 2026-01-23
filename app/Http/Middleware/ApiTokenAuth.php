<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-Api-Token');
        $expectedToken = config('services.api.internal_token');

        if (empty($expectedToken)) {
            return response()->json([
                'error' => 'API authentication not configured',
            ], 500);
        }

        if (empty($token) || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        if ($ability !== null) {
            $allowedAbilities = config('services.api.token_abilities', []);
            if (! in_array($ability, $allowedAbilities, true)) {
                return response()->json([
                    'error' => 'Forbidden - insufficient permissions',
                ], 403);
            }
        }

        return $next($request);
    }
}

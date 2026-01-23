<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToFrontend
{
    /**
     * Redirect public routes to the headless frontend.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $frontendUrl = config('app.frontend_url');

        // If no frontend URL configured, proceed normally
        if (empty($frontendUrl) || $frontendUrl === '/' || $frontendUrl === '*') {
            return $next($request);
        }

        // Build the redirect URL preserving the path
        $path = $request->getPathInfo();
        $redirectUrl = rtrim($frontendUrl, '/').$path;

        // Preserve query string if present
        if ($request->getQueryString()) {
            $redirectUrl .= '?'.$request->getQueryString();
        }

        return redirect()->away($redirectUrl, 301);
    }
}

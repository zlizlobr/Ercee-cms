<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Apply user-selected locale from session for web requests.
 */
class SetLocale
{
    /**
     * @var array<int, string> Allowed locale codes accepted by locale middleware.
     */
    protected array $supportedLocales = ['cs', 'en'];

    /**
     * Set application locale from session when locale is supported.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if ($locale && in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}

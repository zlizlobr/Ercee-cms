<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict webhook endpoints to configured IP addresses or CIDR ranges.
 */
class WebhookIpWhitelist
{
    /**
     * Allow request only when source IP matches whitelist configuration.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = config('services.webhook_whitelist', []);

        if (empty($allowedIps)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        foreach ($allowedIps as $allowedIp) {
            if ($this->ipMatches($clientIp, $allowedIp)) {
                return $next($request);
            }
        }

        abort(403, 'IP address not allowed');
    }

    /**
     * Determine whether a client IP matches exact IP or CIDR entry.
     */
    protected function ipMatches(string $clientIp, string $allowedIp): bool
    {
        if (str_contains($allowedIp, '/')) {
            return $this->ipInCidr($clientIp, $allowedIp);
        }

        return $clientIp === $allowedIp;
    }

    /**
     * Check whether IP belongs to provided CIDR range.
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int) $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}

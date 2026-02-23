<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Support\Module\ModuleManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Provide a lightweight health status endpoint.
 */
class HealthController extends ApiController
{
    /**
     * Return health checks and module versions.
     */
    public function __invoke(ModuleManager $moduleManager): JsonResponse
    {
        return $this->safeGet(function () use ($moduleManager) {
            $checks = [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
            ];

            $modules = [];
            foreach ($moduleManager->getLoadedModules() as $name => $module) {
                $modules[$name] = $module['version'] ?? 'unknown';
            }

            $healthy = ! in_array(false, $checks, true);

            return response()->json([
                'status' => $healthy ? 'ok' : 'degraded',
                'checks' => $checks,
                'modules' => $modules,
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
            ], $healthy ? 200 : 503);
        });
    }

    /**
     * Check database connectivity.
     */
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Check cache read/write availability.
     */
    private function checkCache(): bool
    {
        try {
            Cache::put('health_check', true, 10);

            return Cache::get('health_check') === true;
        } catch (\Throwable) {
            return false;
        }
    }
}


<?php

use App\Support\DevLayer\ErceeDevLayerPolicy;
use Illuminate\Support\Facades\Log;

if (! function_exists('dev_debug')) {
    /**
     * Emit debug log only when Ercee dev-layer policy allows it.
     *
     * @param array<string, mixed> $context
     */
    function dev_debug(string $message, array $context = []): void
    {
        if (! app(ErceeDevLayerPolicy::class)->canWriteDebugLogs()) {
            return;
        }

        Log::debug($message, $context);
    }
}

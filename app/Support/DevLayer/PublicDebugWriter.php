<?php

namespace App\Support\DevLayer;

use Illuminate\Support\Facades\File;

/**
 * Central guard for debug artifacts that may be written under public/.
 */
class PublicDebugWriter
{
    public function __construct(
        private ErceeDevLayerPolicy $policy
    ) {}

    /**
     * Write JSON artifact into public/debug only when policy allows it.
     *
     * @param array<string, mixed> $payload
     */
    public function writeJson(string $relativePath, array $payload): bool
    {
        if (! $this->policy->isPublicDebugEnabled()) {
            return false;
        }

        $normalized = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($normalized === '' || str_contains($normalized, '..')) {
            return false;
        }

        $targetPath = public_path('debug/'.$normalized);
        File::ensureDirectoryExists(dirname($targetPath));
        File::put(
            $targetPath,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        return true;
    }
}

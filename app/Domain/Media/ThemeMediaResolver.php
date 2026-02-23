<?php

namespace App\Domain\Media;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves theme logo references from UUID manifest or legacy disk paths.
 */
class ThemeMediaResolver
{
    /**
     * @param MediaManifestService $manifestService Media lookup service.
     */
    public function __construct(
        private readonly MediaManifestService $manifestService,
    ) {}

    /**
     * @param array<string, mixed> $settings
     */
    public function resolveLogoImageUrl(array $settings): ?string
    {
        $mediaUuid = $settings['logo_media_uuid'] ?? null;

        if ($mediaUuid) {
            return $this->resolveByUuid($mediaUuid);
        }

        $legacyPath = $settings['logo_image'] ?? null;

        if ($legacyPath) {
            return Storage::disk('public')->url($legacyPath);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $settings
     * @return array<string, mixed>|null
     */
    public function resolveLogoMedia(array $settings): ?array
    {
        $mediaUuid = $settings['logo_media_uuid'] ?? null;

        if ($mediaUuid) {
            $entry = $this->manifestService->getByUuid($mediaUuid);

            if ($entry) {
                return $this->manifestService->toApiFormat($entry);
            }
        }

        return null;
    }

    /**
     * Resolves public URL by media UUID.
     */
    private function resolveByUuid(string $uuid): ?string
    {
        return $this->manifestService->getUrl($uuid);
    }
}

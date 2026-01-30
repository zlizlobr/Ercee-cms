<?php

namespace App\Domain\Media;

use Illuminate\Support\Facades\Storage;

class ThemeMediaResolver
{
    public function __construct(
        private readonly MediaManifestService $manifestService,
    ) {}

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

    private function resolveByUuid(string $uuid): ?string
    {
        return $this->manifestService->getUrl($uuid);
    }
}

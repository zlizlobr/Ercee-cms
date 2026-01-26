<?php

namespace App\Domain\Media;

class RichContentTransformer
{
    private const MEDIA_PLACEHOLDER_PATTERN = '#/__media__/([a-f0-9-]+)/(\w+)#i';
    private const LEGACY_STORAGE_PATTERN = '#/storage/([^"\'>\s]+\.(jpg|jpeg|png|gif|webp))#i';

    public function __construct(
        private readonly MediaManifestService $manifestService,
    ) {}

    public function transform(string $html): string
    {
        $html = $this->transformMediaPlaceholders($html);
        $html = $this->transformLegacyUrls($html);

        return $html;
    }

    private function transformMediaPlaceholders(string $html): string
    {
        return preg_replace_callback(
            self::MEDIA_PLACEHOLDER_PATTERN,
            function ($matches) {
                $uuid = $matches[1];
                $variant = $matches[2];

                $entry = $this->manifestService->getByUuid($uuid);

                if (! $entry) {
                    return $matches[0];
                }

                if ($variant === 'original') {
                    return $entry['original']['url'] ?? $matches[0];
                }

                return $entry['variants'][$variant]['url'] ?? $entry['original']['url'] ?? $matches[0];
            },
            $html
        );
    }

    private function transformLegacyUrls(string $html): string
    {
        return $html;
    }

    public function extractMediaUuids(string $html): array
    {
        preg_match_all(self::MEDIA_PLACEHOLDER_PATTERN, $html, $matches);

        return array_unique($matches[1] ?? []);
    }

    public function extractLegacyPaths(string $html): array
    {
        preg_match_all(self::LEGACY_STORAGE_PATTERN, $html, $matches);

        return array_unique($matches[1] ?? []);
    }
}

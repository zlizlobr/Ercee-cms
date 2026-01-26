<?php

namespace App\Domain\Media;

/**
 * Transform rich HTML content with media placeholders and legacy URLs.
 */
class RichContentTransformer
{
    private const MEDIA_PLACEHOLDER_PATTERN = '#/__media__/([a-f0-9-]+)/(\w+)#i';
    private const LEGACY_STORAGE_PATTERN = '#/storage/([^"\'>\s]+\.(jpg|jpeg|png|gif|webp))#i';

    public function __construct(
        private readonly MediaManifestService $manifestService,
    ) {}

    /**
     * Transform HTML to resolved media URLs.
     */
    public function transform(string $html): string
    {
        $html = $this->transformMediaPlaceholders($html);
        $html = $this->transformLegacyUrls($html);

        return $html;
    }

    /**
     * Replace media placeholder URLs with manifest URLs.
     */
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

    /**
     * Replace legacy storage URLs with resolved media URLs.
     */
    private function transformLegacyUrls(string $html): string
    {
        return $html;
    }

    /**
     * Extract media UUIDs from placeholder URLs.
     *
     * @return array<int, string>
     */
    public function extractMediaUuids(string $html): array
    {
        preg_match_all(self::MEDIA_PLACEHOLDER_PATTERN, $html, $matches);

        return array_unique($matches[1] ?? []);
    }

    /**
     * Extract legacy storage paths from HTML.
     *
     * @return array<int, string>
     */
    public function extractLegacyPaths(string $html): array
    {
        preg_match_all(self::LEGACY_STORAGE_PATTERN, $html, $matches);

        return array_unique($matches[1] ?? []);
    }
}

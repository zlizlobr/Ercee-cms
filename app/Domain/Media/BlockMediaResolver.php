<?php

namespace App\Domain\Media;

use Illuminate\Support\Facades\Storage;

/**
 * Resolve block media references from UUIDs or legacy storage paths.
 */
class BlockMediaResolver
{
    public function __construct(
        private readonly MediaManifestService $manifestService,
    ) {}

    /**
     * Resolve a single block's media data by block type.
     *
     * @param array<string, mixed> $blockData
     * @return array<string, mixed>
     */
    public function resolve(array $blockData, string $blockType): array
    {
        return match ($blockType) {
            'image' => $this->resolveImageBlock($blockData),
            'hero' => $this->resolveHeroBlock($blockData),
            default => $blockData,
        };
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveImageBlock(array $data): array
    {
        $media = null;

        if (isset($data['media_uuid'])) {
            $media = $this->resolveByUuid($data['media_uuid']);
        } elseif (isset($data['image'])) {
            $media = $this->resolveLegacyPath($data['image']);
        }

        if ($media) {
            $data['media'] = $media;
            if (empty($data['alt']) && ! empty($media['alt'])) {
                $data['alt'] = $media['alt'];
            }
        }

        unset($data['media_uuid'], $data['image']);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveHeroBlock(array $data): array
    {
        $media = null;

        if (isset($data['background_media_uuid'])) {
            $media = $this->resolveByUuid($data['background_media_uuid']);
        } elseif (isset($data['background_image'])) {
            $media = $this->resolveLegacyPath($data['background_image']);
        }

        if ($media) {
            $data['background_media'] = $media;
        }

        unset($data['background_media_uuid'], $data['background_image']);

        return $data;
    }

    /**
     * Resolve a media manifest entry and map it to API format.
     *
     * @return array<string, mixed>|null
     */
    private function resolveByUuid(string $uuid): ?array
    {
        $entry = $this->manifestService->getByUuid($uuid);

        if (! $entry) {
            return null;
        }

        return $this->manifestService->toApiFormat($entry);
    }

    /**
     * Resolve a legacy public storage path into a media-like payload.
     *
     * @return array<string, mixed>|null
     */
    private function resolveLegacyPath(string $path): ?array
    {
        if (empty($path)) {
            return null;
        }

        $url = Storage::disk('public')->url($path);
        $fullPath = Storage::disk('public')->path($path);

        $dimensions = $this->getImageDimensions($fullPath);

        return [
            'uuid' => null,
            'url' => $url,
            'alt' => null,
            'title' => pathinfo($path, PATHINFO_FILENAME),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'mime' => $this->getMimeType($fullPath),
            'focal_point' => null,
            'variants' => [],
            'legacy' => true,
        ];
    }

    /**
     * @return array{width: int|null, height: int|null}
     */
    private function getImageDimensions(string $path): array
    {
        if (! file_exists($path)) {
            return ['width' => null, 'height' => null];
        }

        $info = @getimagesize($path);

        return [
            'width' => $info[0] ?? null,
            'height' => $info[1] ?? null,
        ];
    }

    /**
     * Get a file MIME type from disk.
     */
    private function getMimeType(string $path): ?string
    {
        if (! file_exists($path)) {
            return null;
        }

        return mime_content_type($path) ?: null;
    }

    /**
     * Resolve all blocks that contain media references.
     *
     * @param array<int, array<string, mixed>> $blocks
     * @return array<int, array<string, mixed>>
     */
    public function resolveAllBlocks(array $blocks): array
    {
        return array_map(function ($block) {
            if (! isset($block['type'], $block['data'])) {
                return $block;
            }

            $block['data'] = $this->resolve($block['data'], $block['type']);

            return $block;
        }, $blocks);
    }
}

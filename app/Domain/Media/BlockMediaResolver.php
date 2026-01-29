<?php

namespace App\Domain\Media;

use App\Domain\Content\Page;
use Illuminate\Support\Facades\Storage;

/**
 * Resolve block media references from UUIDs or legacy storage paths.
 */
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
            'testimonials' => $this->resolveTestimonialsBlock($blockData),
            'premium_cta' => $this->resolvePremiumCtaBlock($blockData),
            'service_highlights' => $this->resolveServiceHighlightsBlock($blockData),
            default => $blockData,
        };
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
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
            if (! empty($media['url'])) {
                $data['background_image'] = $media['url'];
            }
        }

        if (empty($data['title']) && ! empty($data['heading'])) {
            $data['title'] = $data['heading'];
        }

        if (empty($data['subtitle']) && ! empty($data['subheading'])) {
            $data['subtitle'] = $data['subheading'];
        }

        if (
            empty($data['cta_primary_label'])
            && empty($data['cta_primary'])
            && ! empty($data['button_text'])
        ) {
            $data['cta_primary_label'] = $data['button_text'];
        }

        if (
            empty($data['cta_primary_url'])
            && empty($data['cta_primary'])
            && ! empty($data['button_url'])
        ) {
            $data['cta_primary_url'] = $data['button_url'];
        }

        $data = $this->resolveHeroCta($data, 'cta_primary');
        $data = $this->resolveHeroCta($data, 'cta_secondary');

        unset(
            $data['background_media_uuid'],
            $data['heading'],
            $data['subheading'],
            $data['button_text'],
            $data['button_url']
        );

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveTestimonialsBlock(array $data): array
    {
        if (! isset($data['testimonials']) || ! is_array($data['testimonials'])) {
            return $data;
        }

        $data['testimonials'] = array_map(function ($testimonial) {
            if (! is_array($testimonial)) {
                return $testimonial;
            }

            $media = null;

            if (isset($testimonial['media_uuid'])) {
                $media = $this->resolveByUuid($testimonial['media_uuid']);
            } elseif (
                isset($testimonial['image'])
                && is_string($testimonial['image'])
                && ! filter_var($testimonial['image'], FILTER_VALIDATE_URL)
            ) {
                $media = $this->resolveLegacyPath($testimonial['image']);
            }

            if ($media) {
                $testimonial['image'] = $media['url'] ?? null;
            }

            unset($testimonial['media_uuid']);

            return $testimonial;
        }, $data['testimonials']);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolvePremiumCtaBlock(array $data): array
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

        if (isset($data['buttons']) && is_array($data['buttons'])) {
            $data['buttons'] = array_map(function ($button) {
                if (! is_array($button)) {
                    return $button;
                }

                if (empty($button['url']) && ! empty($button['page_id'])) {
                    $page = Page::find($button['page_id']);
                    if ($page) {
                        $button['url'] = '/'.$page->slug;
                    }
                }

                return $button;
            }, $data['buttons']);
        }

        unset($data['background_media_uuid'], $data['background_image']);

        return $data;
    }


    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveServiceHighlightsBlock(array $data): array
    {
        if (isset($data['services']) && is_array($data['services'])) {
            $data['services'] = array_map(function ($service) {
                if (! is_array($service)) {
                    return $service;
                }

                if (isset($service['link']) && is_array($service['link'])) {
                    $service['link'] = $this->resolveBlockLink($service['link']);
                }

                return $service;
            }, $data['services']);
        }

        if (isset($data['cta']) && is_array($data['cta'])) {
            if (isset($data['cta']['link']) && is_array($data['cta']['link'])) {
                $data['cta']['link'] = $this->resolveBlockLink($data['cta']['link']);
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $link
     * @return array<string, mixed>
     */
    private function resolveBlockLink(array $link): array
    {
        $url = $link['url'] ?? null;
        $pageId = $link['page_id'] ?? null;
        $anchor = $link['anchor'] ?? null;

        if (empty($url) && ! empty($pageId)) {
            $page = Page::find($pageId);
            if ($page) {
                $url = '/'.$page->slug;
            }
        }

        if (empty($url) && ! empty($anchor)) {
            $url = '#'.ltrim((string) $anchor, '#');
        }

        if (
            is_string($url)
            && $url !== ''
            && ! empty($anchor)
            && strpos($url, '#') === false
        ) {
            $url .= '#'.ltrim((string) $anchor, '#');
        }

        if (is_string($url) && $url !== '') {
            $link['url'] = $url;
        } else {
            unset($link['url']);
        }

        return $link;
    }

    /**
     * Normalize hero CTA fields into { label, url } objects.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveHeroCta(array $data, string $prefix): array
    {
        $labelKey = "{$prefix}_label";
        $pageIdKey = "{$prefix}_page_id";
        $urlKey = "{$prefix}_url";

        $label = $data[$labelKey] ?? null;
        $url = $data[$urlKey] ?? null;
        $pageId = $data[$pageIdKey] ?? null;

        if (isset($data[$prefix]) && is_array($data[$prefix])) {
            $cta = $data[$prefix];
            $label = $cta['label'] ?? $label;
            $url = $cta['url'] ?? $url;
            $pageId = $cta['page_id'] ?? $pageId;
        }

        if (empty($url) && ! empty($pageId)) {
            $page = Page::find($pageId);
            if ($page) {
                $url = '/'.$page->slug;
            }
        }

        if (is_string($label) && $label !== '' && is_string($url) && $url !== '') {
            $data[$prefix] = [
                'label' => $label,
                'url' => $url,
            ];
        } else {
            unset($data[$prefix]);
        }

        unset($data[$labelKey], $data[$pageIdKey], $data[$urlKey]);

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

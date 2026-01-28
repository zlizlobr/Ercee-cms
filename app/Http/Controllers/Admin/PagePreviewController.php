<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Page;
use App\Domain\Media\Media;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Resolve media URLs for the admin page preview view.
 */
class PagePreviewController extends Controller
{
    /**
     * Render the preview view with resolved block media.
     */
    public function __invoke(Page $page): View
    {
        $blocks = $this->resolveMediaInBlocks($page->getBlocks());

        return view('filament.pages.preview', [
            'page' => $page,
            'resolvedBlocks' => $blocks,
        ]);
    }

    /**
     * Resolve media URLs for block data used in preview.
     *
     * @param array<int, array<string, mixed>> $blocks
     * @return array<int, array<string, mixed>>
     */
    private function resolveMediaInBlocks(array $blocks): array
    {
        return array_map(function ($block) {
            if (! isset($block['data'])) {
                return $block;
            }

            $block['data'] = match ($block['type'] ?? '') {
                'image' => $this->resolveImageBlock($block['data']),
                'hero' => $this->resolveHeroBlock($block['data']),
                'testimonials' => $this->resolveTestimonialsBlock($block['data']),
                'premium_cta' => $this->resolvePremiumCtaBlock($block['data']),
                'service_highlights' => $this->resolveServiceHighlightsBlock($block['data']),
                default => $block['data'],
            };

            return $block;
        }, $blocks);
    }

    /**
     * Resolve image block media URLs.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveImageBlock(array $data): array
    {
        if (isset($data['media_uuid'])) {
            $media = Media::where('uuid', $data['media_uuid'])->first();
            if ($media) {
                $data['image_url'] = $media->getUrl();
                $data['image_url_medium'] = $media->getUrl('medium');
                if (empty($data['alt'])) {
                    $data['alt'] = $media->getCustomProperty('alt') ?? '';
                }
            }
        } elseif (isset($data['image'])) {
            $data['image_url'] = Storage::disk('public')->url($data['image']);
        }

        return $data;
    }

    /**
     * Resolve hero block media URLs.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolveHeroBlock(array $data): array
    {
        if (isset($data['background_media_uuid'])) {
            $media = Media::where('uuid', $data['background_media_uuid'])->first();
            if ($media) {
                $data['background_image_url'] = $media->getUrl();
                $data['background_image_url_large'] = $media->getUrl('large');
            }
        } elseif (isset($data['background_image'])) {
            $data['background_image_url'] = filter_var($data['background_image'], FILTER_VALIDATE_URL)
                ? $data['background_image']
                : Storage::disk('public')->url($data['background_image']);
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

        return $data;
    }

    /**
     * Resolve testimonials block media URLs.
     *
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

            if (isset($testimonial['media_uuid'])) {
                $media = Media::where('uuid', $testimonial['media_uuid'])->first();
                if ($media) {
                    $testimonial['image'] = $media->getUrl();
                }
            } elseif (
                isset($testimonial['image'])
                && is_string($testimonial['image'])
                && ! filter_var($testimonial['image'], FILTER_VALIDATE_URL)
            ) {
                $testimonial['image'] = Storage::disk('public')->url($testimonial['image']);
            }

            unset($testimonial['media_uuid']);

            return $testimonial;
        }, $data['testimonials']);

        return $data;
    }

    /**
     * Resolve premium CTA block background media URLs.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function resolvePremiumCtaBlock(array $data): array
    {
        if (isset($data['background_media_uuid'])) {
            $media = Media::where('uuid', $data['background_media_uuid'])->first();
            if ($media) {
                $data['background_image_url'] = $media->getUrl();
                $data['background_image_url_large'] = $media->getUrl('large');
            }
        } elseif (isset($data['background_image'])) {
            $data['background_image_url'] = Storage::disk('public')->url($data['background_image']);
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
                    $service['link'] = $this->resolvePreviewLink($service['link']);
                }

                return $service;
            }, $data['services']);
        }

        if (isset($data['cta']) && is_array($data['cta'])) {
            if (isset($data['cta']['link']) && is_array($data['cta']['link'])) {
                $data['cta']['link'] = $this->resolvePreviewLink($data['cta']['link']);
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $link
     * @return array<string, mixed>
     */
    private function resolvePreviewLink(array $link): array
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
     * Normalize hero CTA fields into { label, url } objects for preview.
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

        return $data;
    }
}

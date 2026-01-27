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
            $data['background_image_url'] = Storage::disk('public')->url($data['background_image']);
        }

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
}

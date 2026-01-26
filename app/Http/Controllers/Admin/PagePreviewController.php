<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Page;
use App\Domain\Media\Media;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PagePreviewController extends Controller
{
    public function __invoke(Page $page): View
    {
        $blocks = $this->resolveMediaInBlocks($page->getBlocks());

        return view('filament.pages.preview', [
            'page' => $page,
            'resolvedBlocks' => $blocks,
        ]);
    }

    private function resolveMediaInBlocks(array $blocks): array
    {
        return array_map(function ($block) {
            if (! isset($block['data'])) {
                return $block;
            }

            $block['data'] = match ($block['type'] ?? '') {
                'image' => $this->resolveImageBlock($block['data']),
                'hero' => $this->resolveHeroBlock($block['data']),
                default => $block['data'],
            };

            return $block;
        }, $blocks);
    }

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
        }

        return $data;
    }

    private function resolveHeroBlock(array $data): array
    {
        if (isset($data['background_media_uuid'])) {
            $media = Media::where('uuid', $data['background_media_uuid'])->first();
            if ($media) {
                $data['background_image_url'] = $media->getUrl();
                $data['background_image_url_large'] = $media->getUrl('large');
            }
        }

        return $data;
    }
}

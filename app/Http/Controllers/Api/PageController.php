<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Page;
use App\Domain\Media\BlockMediaResolver;
use App\Domain\Media\RichContentTransformer;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Provide read-only access to published pages and their block content.
 */
class PageController extends ApiController
{
    public function __construct(
        private readonly BlockMediaResolver $blockMediaResolver,
        private readonly RichContentTransformer $richContentTransformer,
    ) {}

    /**
     * Return all published page slugs.
     */
    public function index(): JsonResponse
    {
        return $this->safeGet(function () {
            $slugs = Cache::remember('pages:slugs', 3600, function () {
                return Page::published()
                    ->pluck('slug')
                    ->toArray();
            });

            return response()->json([
                'data' => $slugs,
            ]);
        });
    }

    /**
     * Return a published page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        return $this->safeGet(function () use ($slug) {
            $cacheKey = "page:{$slug}";

            $page = Cache::remember($cacheKey, 3600, function () use ($slug) {
                return Page::published()
                    ->where('slug', $slug)
                    ->first();
            });

            if (! $page) {
                return response()->json([
                    'error' => 'Page not found',
                ], 404);
            }

            $blocks = $this->transformBlocks($page->getBlocks());

            return response()->json([
                'data' => [
                    'id' => $page->id,
                    'slug' => $page->slug,
                    'title' => $page->title,
                    'blocks' => $blocks,
                    'seo' => $page->seo_meta,
                    'published_at' => $page->published_at?->toIso8601String(),
                ],
            ]);
        });
    }

    /**
     * Resolve media references and transform rich text blocks.
     *
     * @param array<int, array<string, mixed>> $blocks
     * @return array<int, array<string, mixed>>
     */
    private function transformBlocks(array $blocks): array
    {
        $blocks = $this->blockMediaResolver->resolveAllBlocks($blocks);

        foreach ($blocks as &$block) {
            if (isset($block['data']['body']) && is_string($block['data']['body'])) {
                $block['data']['body'] = $this->richContentTransformer->transform($block['data']['body']);
            }
        }

        return $blocks;
    }
}

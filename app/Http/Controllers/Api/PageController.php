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
            $latestUpdatedAt = Page::published()->max('updated_at');
            $latestUpdatedAtIso = $this->normalizeIsoDate($latestUpdatedAt);
            $latestUpdatedAtTs = $this->normalizeTimestamp($latestUpdatedAt);
            $cacheKey = 'pages:slugs:'.($latestUpdatedAtTs ?? 'none');

            $pages = Cache::remember($cacheKey, 3600, function () {
                return Page::published()
                    ->orderBy('slug')
                    ->get(['slug', 'updated_at'])
                    ->map(fn (Page $page) => [
                        'slug' => $page->slug,
                        'updated_at' => $page->updated_at?->toIso8601String(),
                    ])
                    ->toArray();
            });

            return response()->json([
                'data' => $pages,
                'meta' => [
                    'updated_at' => $latestUpdatedAtIso,
                ],
            ]);
        });
    }

    /**
     * Return a published page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        return $this->safeGet(function () use ($slug) {
            $updatedAt = Page::published()->where('slug', $slug)->value('updated_at');
            $cacheKey = "page:{$slug}:".($this->normalizeTimestamp($updatedAt) ?? 'none');

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
                    'updated_at' => $page->updated_at?->toIso8601String(),
                ],
            ]);
        });
    }

    /**
     * Normalize timestamp-like value to unix timestamp for cache keys.
     */
    private function normalizeTimestamp(mixed $value): ?int
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $parsed = strtotime((string) $value);

        return $parsed === false ? null : $parsed;
    }

    /**
     * Normalize timestamp-like value to ISO 8601 string for API metadata.
     */
    private function normalizeIsoDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_numeric($value)) {
            return date(DATE_ATOM, (int) $value);
        }

        $parsed = strtotime((string) $value);

        return $parsed === false ? (string) $value : date(DATE_ATOM, $parsed);
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

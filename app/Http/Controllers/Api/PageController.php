<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
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

        return response()->json([
            'data' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'blocks' => $page->getBlocks(),
                'seo' => $page->seo_meta,
                'published_at' => $page->published_at?->toIso8601String(),
            ],
        ]);
    }
}

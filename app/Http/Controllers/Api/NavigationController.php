<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Provide read-only access to navigation menus.
 */
class NavigationController extends ApiController
{
    /**
     * Return navigation items for a menu slug.
     */
    public function index(?string $menuSlug = 'main'): JsonResponse
    {
        return $this->safeGet(function () use ($menuSlug) {
            $menu = Menu::where('slug', $menuSlug)->first();

            if (! $menu) {
                return response()->json([
                    'data' => [],
                    'meta' => [
                        'updated_at' => null,
                    ],
                ]);
            }

            $latestItemUpdatedAt = Navigation::where('menu_id', $menu->id)->max('updated_at');
            $latestUpdatedAt = collect([$menu->updated_at, $latestItemUpdatedAt])->filter()->max();
            $latestUpdatedAtIso = $this->normalizeIsoDate($latestUpdatedAt);
            $latestUpdatedAtTs = $this->normalizeTimestamp($latestUpdatedAt);
            $cacheKey = "navigation:{$menuSlug}:".($latestUpdatedAtTs ?? 'none');

            $navigation = Cache::remember($cacheKey, 3600, function () use ($menu) {
                return Navigation::where('menu_id', $menu->id)
                    ->active()
                    ->roots()
                    ->ordered()
                    ->with([
                        'page',
                        'children' => fn ($q) => $q->active()->ordered(),
                        'children.page',
                    ])
                    ->get()
                    ->map(fn ($item) => $item->toArray())
                    ->toArray();
            });

            return response()->json([
                'data' => $navigation,
                'meta' => [
                    'updated_at' => $latestUpdatedAtIso,
                ],
            ]);
        });
    }

    /**
     * Return a menu with all items by slug.
     */
    public function show(string $menuSlug): JsonResponse
    {
        return $this->safeGet(function () use ($menuSlug) {
            $menu = Menu::where('slug', $menuSlug)->first();

            if (! $menu) {
                return response()->json([
                    'error' => 'Menu not found',
                ], 404);
            }

            $latestItemUpdatedAt = Navigation::where('menu_id', $menu->id)->max('updated_at');
            $latestUpdatedAt = collect([$menu->updated_at, $latestItemUpdatedAt])->filter()->max();
            $latestUpdatedAtIso = $this->normalizeIsoDate($latestUpdatedAt);
            $latestUpdatedAtTs = $this->normalizeTimestamp($latestUpdatedAt);
            $cacheKey = "menu:{$menuSlug}:".($latestUpdatedAtTs ?? 'none');

            $menuData = Cache::remember($cacheKey, 3600, function () use ($menuSlug) {
                $menu = Menu::where('slug', $menuSlug)
                    ->with([
                        'items.page',
                        'items.children' => fn ($q) => $q->active()->ordered(),
                        'items.children.page',
                    ])
                    ->first();

                return $menu?->toArray();
            });

            if (! $menuData) {
                return response()->json([
                    'error' => 'Menu not found',
                ], 404);
            }

            return response()->json([
                'data' => $menuData,
                'meta' => [
                    'updated_at' => $latestUpdatedAtIso,
                ],
            ]);
        });
    }

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
}

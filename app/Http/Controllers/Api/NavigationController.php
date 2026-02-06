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
            $cacheKey = "navigation:{$menuSlug}";

            $navigation = Cache::remember($cacheKey, 3600, function () use ($menuSlug) {
                $menu = Menu::where('slug', $menuSlug)->first();

                if (! $menu) {
                    return [];
                }

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
            ]);
        });
    }

    /**
     * Return a menu with all items by slug.
     */
    public function show(string $menuSlug): JsonResponse
    {
        return $this->safeGet(function () use ($menuSlug) {
            $cacheKey = "menu:{$menuSlug}";

            $menu = Cache::remember($cacheKey, 3600, function () use ($menuSlug) {
                $menu = Menu::where('slug', $menuSlug)
                    ->with([
                        'items.page',
                        'items.children' => fn ($q) => $q->active()->ordered(),
                        'items.children.page',
                    ])
                    ->first();

                return $menu?->toArray();
            });

            if (! $menu) {
                return response()->json([
                    'error' => 'Menu not found',
                ], 404);
            }

            return response()->json([
                'data' => $menu,
            ]);
        });
    }
}

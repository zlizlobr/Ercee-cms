<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NavigationController extends Controller
{
    /**
     * Get navigation by menu slug (default: 'main')
     */
    public function index(?string $menuSlug = 'main'): JsonResponse
    {
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
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->get()
                ->map(fn ($item) => $item->toArray())
                ->toArray();
        });

        return response()->json([
            'data' => $navigation,
        ]);
    }

    /**
     * Get specific menu by slug with all items
     */
    public function show(string $menuSlug): JsonResponse
    {
        $cacheKey = "menu:{$menuSlug}";

        $menu = Cache::remember($cacheKey, 3600, function () use ($menuSlug) {
            $menu = Menu::where('slug', $menuSlug)
                ->with(['items.children' => fn ($q) => $q->active()->ordered()])
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
    }
}

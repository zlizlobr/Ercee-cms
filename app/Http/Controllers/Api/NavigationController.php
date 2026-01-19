<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Navigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NavigationController extends Controller
{
    public function index(): JsonResponse
    {
        $navigation = Cache::remember('navigation:tree', 3600, function () {
            return Navigation::active()
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
}

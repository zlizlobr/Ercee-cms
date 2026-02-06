<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\Commerce\Domain\Taxonomy;

class TaxonomyMappingController extends Controller
{
    public function index(): JsonResponse
    {
        $mapping = Cache::remember('taxonomy-mapping:products', 3600, function () {
            return [
                'products' => [
                    Taxonomy::TYPE_CATEGORY => $this->taxonomySlugs(Taxonomy::TYPE_CATEGORY),
                    Taxonomy::TYPE_TAG => $this->taxonomySlugs(Taxonomy::TYPE_TAG),
                    Taxonomy::TYPE_BRAND => $this->taxonomySlugs(Taxonomy::TYPE_BRAND),
                ],
            ];
        });

        return response()->json([
            'data' => $mapping,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function taxonomySlugs(string $type): array
    {
        return Taxonomy::query()
            ->where('type', $type)
            ->whereHas('products', fn ($query) => $query->active())
            ->orderBy('slug')
            ->pluck('slug')
            ->unique()
            ->values()
            ->all();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Domain\Commerce\Product;
use App\Domain\Commerce\Services\ProductPricingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductPricingService $pricingService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'products:active:'.md5(serialize($request->only(['type', 'category', 'tag', 'brand'])));

        $products = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Product::active()
                ->with(['categories', 'tags', 'brands']);

            // Filter by type
            if ($request->has('type')) {
                $query->ofType($request->input('type'));
            }

            // Filter by category
            if ($request->has('category')) {
                $query->filterByCategories((array) $request->input('category'));
            }

            // Filter by tag
            if ($request->has('tag')) {
                $query->filterByTags((array) $request->input('tag'));
            }

            // Filter by brand
            if ($request->has('brand')) {
                $query->filterByBrands((array) $request->input('brand'));
            }

            return $query->orderBy('name')->get();
        });

        return response()->json([
            'data' => $products->map(fn (Product $product) => $this->formatProductListItem($product)),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $cacheKey = "product:{$id}";

        $product = Cache::remember($cacheKey, 3600, function () use ($id) {
            return Product::active()
                ->with([
                    'categories',
                    'tags',
                    'brands',
                    'attributeValues.attribute',
                    'variants.attributeValues',
                    'reviews' => fn ($q) => $q->approved()->latest()->take(10),
                ])
                ->find($id);
        });

        if (! $product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'data' => $this->formatProductDetail($product),
        ]);
    }

    private function formatProductListItem(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'type' => $product->type,
            'short_description' => $product->short_description,
            'price' => $product->price,
            'price_formatted' => $product->price_formatted,
            'image' => $product->attachment ? Storage::disk('public')->url($product->attachment) : null,
            'categories' => $product->categories->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]),
            'tags' => $product->tags->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]),
            'brands' => $product->brands->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]),
        ];
    }

    private function formatProductDetail(Product $product): array
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'type' => $product->type,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'price' => $product->price,
            'price_formatted' => $product->price_formatted,
            'image' => $product->attachment ? Storage::disk('public')->url($product->attachment) : null,
            'gallery' => collect($product->gallery)->map(fn ($img) => Storage::disk('public')->url($img))->all(),
            'categories' => $product->categories->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]),
            'tags' => $product->tags->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]),
            'brands' => $product->brands->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]),
            'attributes' => $product->attributeValues->groupBy('attribute.name')->map(fn ($values, $name) => [
                'name' => $name,
                'values' => $values->pluck('value')->all(),
            ])->values(),
            'seo' => $product->data['seo'] ?? null,
        ];

        // Add variants for variable products
        if ($product->isVariable()) {
            $data['price_range'] = $this->pricingService->getPriceRangeFormatted($product);
            $data['variants'] = $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'price_formatted' => $variant->price_formatted,
                'stock' => $variant->stock,
                'in_stock' => $variant->stock > 0,
                'attributes' => $variant->attributeValues->map(fn ($av) => [
                    'attribute' => $av->attribute->name ?? null,
                    'value' => $av->value,
                ]),
            ]);
        }

        // Add reviews
        if ($product->reviews->count() > 0) {
            $data['reviews'] = [
                'count' => $product->reviews->count(),
                'average_rating' => round($product->reviews->avg('rating'), 1),
                'items' => $product->reviews->map(fn ($review) => [
                    'id' => $review->id,
                    'author' => $review->author_name,
                    'rating' => $review->rating,
                    'content' => $review->content,
                    'created_at' => $review->created_at->toIso8601String(),
                ]),
            ];
        }

        return $data;
    }
}

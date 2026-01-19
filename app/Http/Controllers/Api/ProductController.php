<?php

namespace App\Http\Controllers\Api;

use App\Domain\Commerce\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Cache::remember('products:active', 3600, function () {
            return Product::active()
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'data' => $products->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'price_formatted' => $product->price_formatted,
            ]),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $cacheKey = "product:{$id}";

        $product = Cache::remember($cacheKey, 3600, function () use ($id) {
            return Product::active()->find($id);
        });

        if (! $product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'price_formatted' => $product->price_formatted,
            ],
        ]);
    }
}

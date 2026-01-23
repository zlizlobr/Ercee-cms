<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Commerce\Product;
use App\Domain\Commerce\Services\ProductPricingService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProductPreviewController extends Controller
{
    public function __invoke(Product $product, ProductPricingService $pricingService): View
    {
        $product->load(['variants', 'categories', 'tags', 'brands', 'attributeValues.attribute', 'reviews' => fn ($q) => $q->approved()->latest()->take(5)]);

        return view('filament.products.preview', [
            'product' => $product,
            'pricingService' => $pricingService,
        ]);
    }
}

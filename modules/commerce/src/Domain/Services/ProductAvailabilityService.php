<?php

namespace Modules\Commerce\Domain\Services;

use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\ProductVariant;

class ProductAvailabilityService
{
    public function isAvailable(Product $product, ?ProductVariant $variant = null): bool
    {
        if (! $product->active) {
            return false;
        }

        return match ($product->type) {
            Product::TYPE_SIMPLE => true,
            Product::TYPE_VIRTUAL => true,
            Product::TYPE_VARIABLE => $this->isVariableProductAvailable($product, $variant),
            default => false,
        };
    }

    public function isPurchasable(Product $product, ?ProductVariant $variant = null): bool
    {
        if (! $this->isAvailable($product, $variant)) {
            return false;
        }

        return match ($product->type) {
            Product::TYPE_SIMPLE => true,
            Product::TYPE_VIRTUAL => true,
            Product::TYPE_VARIABLE => $variant !== null && $variant->isInStock(),
            default => false,
        };
    }

    public function getStock(Product $product, ?ProductVariant $variant = null): ?int
    {
        return match ($product->type) {
            Product::TYPE_VARIABLE => $variant?->stock ?? $this->getTotalVariantStock($product),
            default => null,
        };
    }

    public function getTotalVariantStock(Product $product): int
    {
        if (! $product->isVariable()) {
            return 0;
        }

        return $product->variants()->sum('stock');
    }

    public function getAvailableVariants(Product $product): \Illuminate\Database\Eloquent\Collection
    {
        if (! $product->isVariable()) {
            return collect();
        }

        return $product->variants()->where('stock', '>', 0)->get();
    }

    private function isVariableProductAvailable(Product $product, ?ProductVariant $variant): bool
    {
        if ($variant) {
            return $variant->isInStock();
        }

        return $product->variants()->where('stock', '>', 0)->exists();
    }
}

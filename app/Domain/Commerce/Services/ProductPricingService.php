<?php

namespace App\Domain\Commerce\Services;

use App\Domain\Commerce\Product;
use App\Domain\Commerce\ProductVariant;

class ProductPricingService
{
    public function getPrice(Product $product, ?ProductVariant $variant = null): float
    {
        return match ($product->type) {
            Product::TYPE_VARIABLE => $this->getVariablePrice($product, $variant),
            default => (float) $product->price,
        };
    }

    public function getPriceFormatted(Product $product, ?ProductVariant $variant = null): string
    {
        $price = $this->getPrice($product, $variant);

        return $this->formatPrice($price);
    }

    public function getPriceRange(Product $product): array
    {
        if (! $product->isVariable()) {
            return [
                'min' => (float) $product->price,
                'max' => (float) $product->price,
            ];
        }

        $variants = $product->variants;

        if ($variants->isEmpty()) {
            return [
                'min' => (float) ($product->price ?? 0),
                'max' => (float) ($product->price ?? 0),
            ];
        }

        return [
            'min' => (float) $variants->min('price'),
            'max' => (float) $variants->max('price'),
        ];
    }

    public function getPriceRangeFormatted(Product $product): string
    {
        $range = $this->getPriceRange($product);

        if ($range['min'] === $range['max']) {
            return $this->formatPrice($range['min']);
        }

        $decimals = config('commerce.currency.decimals');
        $currency = config('commerce.currency.code');

        return sprintf(
            '%s - %s %s',
            number_format($range['min'], $decimals),
            number_format($range['max'], $decimals),
            $currency
        );
    }

    private function getVariablePrice(Product $product, ?ProductVariant $variant): float
    {
        if ($variant) {
            return (float) $variant->price;
        }

        $lowestPricedVariant = $product->variants()->orderBy('price')->first();

        return (float) ($lowestPricedVariant?->price ?? $product->price ?? 0);
    }

    private function formatPrice(float $price): string
    {
        return number_format(
            $price,
            config('commerce.currency.decimals'),
        ).' '.config('commerce.currency.code');
    }
}

<?php

namespace App\Domain\Commerce;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'stock' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values')
            ->withTimestamps();
    }

    // Accessors

    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format(
                $this->price,
                config('commerce.currency.decimals'),
            ) . ' ' . config('commerce.currency.code'),
        );
    }

    // Stock helpers

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function decrementStock(int $quantity = 1): void
    {
        $this->decrement('stock', $quantity);
    }

    public function incrementStock(int $quantity = 1): void
    {
        $this->increment('stock', $quantity);
    }
}

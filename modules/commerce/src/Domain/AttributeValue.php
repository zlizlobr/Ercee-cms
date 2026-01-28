<?php

namespace Modules\Commerce\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (AttributeValue $attributeValue) {
            if (empty($attributeValue->slug)) {
                $attributeValue->slug = Str::slug($attributeValue->value);
            }
        });
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
            ->withTimestamps();
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values')
            ->withTimestamps();
    }
}

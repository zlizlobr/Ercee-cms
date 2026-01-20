<?php

namespace App\Domain\Commerce;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'attachment',
        'price',
        'data',
        'active',

    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'data' => 'array',
        ];
    }

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->data['gallery'] ?? [],
            set: fn($value) => [
                'data->gallery' => $value,
            ],
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->price / 100, 2) . ' CZK',
        );
    }
}

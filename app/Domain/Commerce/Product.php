<?php

namespace App\Domain\Commerce;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->price / 100, 2).' CZK',
        );
    }
}

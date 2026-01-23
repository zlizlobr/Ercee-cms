<?php

namespace App\Domain\Commerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_filterable',
    ];

    protected function casts(): array
    {
        return [
            'is_filterable' => 'boolean',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    // Scopes

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }
}

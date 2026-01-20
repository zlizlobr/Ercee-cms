<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Navigation::class)
            ->whereNull('parent_id')
            ->orderBy('position');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(Navigation::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'items' => $this->items->map(fn ($item) => $item->toArray())->toArray(),
        ];
    }
}

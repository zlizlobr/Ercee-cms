<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menu model for grouping navigation items.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Navigation> $items
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Navigation> $allItems
 */
class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Root navigation items for this menu.
     *
     * @return HasMany<Navigation>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Navigation::class)
            ->whereNull('parent_id')
            ->orderBy('position');
    }

    /**
     * All navigation items for this menu.
     *
     * @return HasMany<Navigation>
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(Navigation::class);
    }

    /**
     * Array form used by the API response.
     *
     * @return array<string, mixed>
     */
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


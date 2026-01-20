<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Navigation extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'title',
        'slug',
        'url',
        'target',
        'parent_id',
        'page_id',
        'navigable_type',
        'navigable_id',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Navigation::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Navigation::class, 'parent_id')->orderBy('position');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function navigable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    /**
     * Get URL with priority: navigable > page_id (legacy) > url
     */
    public function getUrl(): ?string
    {
        // Priority 1: Polymorphic navigable
        if ($this->navigable) {
            return $this->resolveNavigableUrl();
        }

        // Priority 2: Legacy page_id support
        if ($this->page) {
            return '/' . $this->page->slug;
        }

        // Priority 3: Direct URL
        return $this->url;
    }

    protected function resolveNavigableUrl(): ?string
    {
        $navigable = $this->navigable;

        if ($navigable instanceof Page) {
            return '/' . $navigable->slug;
        }

        // Add more types as needed (Contact, etc.)
        if (method_exists($navigable, 'getUrl')) {
            return $navigable->getUrl();
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->getUrl(),
            'target' => $this->target ?? '_self',
            'children' => $this->children->map(fn ($child) => $child->toArray())->toArray(),
        ];
    }
}

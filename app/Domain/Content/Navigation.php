<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Navigation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'url',
        'parent_id',
        'page_id',
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

    public function getUrl(): ?string
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->page) {
            return '/'.$this->page->slug;
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
            'children' => $this->children->map(fn ($child) => $child->toArray())->toArray(),
        ];
    }
}

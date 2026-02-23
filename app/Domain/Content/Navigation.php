<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Navigation item within a menu tree.
 *
 * @property int $id
 * @property int $menu_id
 * @property string $title
 * @property string|null $slug
 * @property string|null $url
 * @property string|null $target
 * @property int|null $parent_id
 * @property int|null $page_id
 * @property string|null $navigable_type
 * @property int|null $navigable_id
 * @property int $position
 * @property bool $is_active
 * @property-read Menu $menu
 * @property-read Navigation|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Navigation> $children
 * @property-read Page|null $page
 * @property-read \Illuminate\Database\Eloquent\Model|null $navigable
 *
 * @method static Builder active()
 * @method static Builder roots()
 * @method static Builder ordered()
 */
class Navigation extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'title',
        'slug',
        'classes',
        'url',
        'target',
        'parent_id',
        'page_id',
        'navigable_type',
        'navigable_id',
        'position',
        'is_active',
    ];

    /**
     * Attribute casts for navigation fields.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * Menu that owns this navigation item.
     *
     * @return BelongsTo<Menu, Navigation>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Parent navigation item.
     *
     * @return BelongsTo<Navigation, Navigation>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Navigation::class, 'parent_id');
    }

    /**
     * Child navigation items.
     *
     * @return HasMany<Navigation>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Navigation::class, 'parent_id')->orderBy('position');
    }

    /**
     * Legacy page relation for direct page links.
     *
     * @return BelongsTo<Page, Navigation>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Polymorphic relation for dynamic link targets.
     */
    public function navigable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope active navigation items.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope root navigation items.
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope items ordered by position.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    /**
     * Get URL with priority: page_id > url.
     */
    public function getUrl(): ?string
    {
        if ($this->page) {
            return '/'.$this->page->slug;
        }

        return $this->url;
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
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->getUrl(),
            'page_slug' => $this->resolvePageSlug(),
            'target' => $this->target ?? '_self',
            'children' => $this->children->map(fn ($child) => $child->toArray())->toArray(),
        ];
    }

    protected function resolvePageSlug(): ?string
    {
        return $this->page?->slug;
    }
}


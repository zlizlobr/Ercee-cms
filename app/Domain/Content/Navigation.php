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
     *
     * @return MorphTo
     */
    public function navigable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope active navigation items.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope root navigation items.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope items ordered by position.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    /**
     * Get URL with priority: navigable > page_id (legacy) > url.
     *
     * @return string|null
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

    /**
     * Resolve URL from a polymorphic navigable model.
     *
     * @return string|null
     */
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
            'target' => $this->target ?? '_self',
            'children' => $this->children->map(fn($child) => $child->toArray())->toArray(),
        ];
    }
}

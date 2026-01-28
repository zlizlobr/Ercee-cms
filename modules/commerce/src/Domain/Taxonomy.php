<?php

namespace Modules\Commerce\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Taxonomy extends Model
{
    public const TYPE_CATEGORY = 'category';
    public const TYPE_TAG = 'tag';
    public const TYPE_BRAND = 'brand';

    public const TYPES = [
        self::TYPE_CATEGORY => 'Category',
        self::TYPE_TAG => 'Tag',
        self::TYPE_BRAND => 'Brand',
    ];

    protected $fillable = [
        'type',
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (Taxonomy $taxonomy) {
            if (empty($taxonomy->slug)) {
                $taxonomy->slug = Str::slug($taxonomy->name);
            }
        });
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'taxable', 'taxables')
            ->withTimestamps();
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCategories($query)
    {
        return $query->where('type', self::TYPE_CATEGORY);
    }

    public function scopeTags($query)
    {
        return $query->where('type', self::TYPE_TAG);
    }

    public function scopeBrands($query)
    {
        return $query->where('type', self::TYPE_BRAND);
    }
}

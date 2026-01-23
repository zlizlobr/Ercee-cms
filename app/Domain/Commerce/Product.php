<?php

namespace App\Domain\Commerce;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    // Product types
    public const TYPE_SIMPLE = 'simple';

    public const TYPE_VIRTUAL = 'virtual';

    public const TYPE_VARIABLE = 'variable';

    public const TYPES = [
        self::TYPE_SIMPLE => 'Simple',
        self::TYPE_VIRTUAL => 'Virtual',
        self::TYPE_VARIABLE => 'Variable',
    ];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'type',
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

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Accessors

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['gallery'] ?? [],
            set: fn ($value) => [
                'data->gallery' => $value,
            ],
        );
    }

    protected function shortDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['short_description'] ?? null,
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['description'] ?? null,
        );
    }

    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format(
                $this->price,
                config('commerce.currency.decimals'),
            ).' '.config('commerce.currency.code'),
        );
    }

    // Type checks

    public function isSimple(): bool
    {
        return $this->type === self::TYPE_SIMPLE;
    }

    public function isVirtual(): bool
    {
        return $this->type === self::TYPE_VIRTUAL;
    }

    public function isVariable(): bool
    {
        return $this->type === self::TYPE_VARIABLE;
    }

    // Relationships

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values')
            ->withTimestamps();
    }

    public function taxonomies(): BelongsToMany
    {
        return $this->morphToMany(Taxonomy::class, 'taxable', 'taxables')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->taxonomies()->where('type', 'category');
    }

    public function tags(): BelongsToMany
    {
        return $this->taxonomies()->where('type', 'tag');
    }

    public function brands(): BelongsToMany
    {
        return $this->taxonomies()->where('type', 'brand');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSimple($query)
    {
        return $query->where('type', self::TYPE_SIMPLE);
    }

    public function scopeVirtual($query)
    {
        return $query->where('type', self::TYPE_VIRTUAL);
    }

    public function scopeVariable($query)
    {
        return $query->where('type', self::TYPE_VARIABLE);
    }

    public function scopeFilterByAttributes($query, array $attributeSlugs)
    {
        return $query->whereHas('attributeValues', function ($q) use ($attributeSlugs) {
            $q->whereIn('slug', $attributeSlugs);
        });
    }

    public function scopeFilterByCategories($query, array $categorySlugs)
    {
        return $query->whereHas('categories', function ($q) use ($categorySlugs) {
            $q->whereIn('slug', $categorySlugs);
        });
    }

    public function scopeFilterByTags($query, array $tagSlugs)
    {
        return $query->whereHas('tags', function ($q) use ($tagSlugs) {
            $q->whereIn('slug', $tagSlugs);
        });
    }

    public function scopeFilterByBrands($query, array $brandSlugs)
    {
        return $query->whereHas('brands', function ($q) use ($brandSlugs) {
            $q->whereIn('slug', $brandSlugs);
        });
    }
}

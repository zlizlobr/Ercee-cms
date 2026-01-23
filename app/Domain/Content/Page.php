<?php

namespace App\Domain\Content;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const BLOCK_TYPE_HERO = 'hero';

    public const BLOCK_TYPE_TEXT = 'text';

    public const BLOCK_TYPE_IMAGE = 'image';

    public const BLOCK_TYPE_CTA = 'cta';

    public const BLOCK_TYPE_FORM_EMBED = 'form_embed';

    public const SUPPORTED_LOCALES = ['cs', 'en'];

    protected $fillable = [
        'slug',
        'title',
        'content',
        'seo_meta',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'content' => 'array',
            'seo_meta' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public static function blockTypes(): array
    {
        return [
            self::BLOCK_TYPE_HERO => __('admin.page.blocks.hero'),
            self::BLOCK_TYPE_TEXT => __('admin.page.blocks.text'),
            self::BLOCK_TYPE_IMAGE => __('admin.page.blocks.image'),
            self::BLOCK_TYPE_CTA => __('admin.page.blocks.cta'),
            self::BLOCK_TYPE_FORM_EMBED => __('admin.page.blocks.form_embed'),
        ];
    }

    /**
     * Get localized title for current locale with fallback.
     */
    public function getLocalizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        $titles = $this->title;

        if (is_string($titles)) {
            return $titles;
        }

        return $titles[$locale] ?? $titles[$fallback] ?? array_values($titles)[0] ?? '';
    }

    /**
     * Set title for a specific locale.
     */
    public function setLocalizedTitle(string $value, ?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();
        $titles = $this->title ?? [];

        if (is_string($titles)) {
            $titles = [$locale => $titles];
        }

        $titles[$locale] = $value;
        $this->title = $titles;
    }

    /**
     * Get all translations for title.
     */
    public function getTitleTranslations(): array
    {
        $titles = $this->title;

        if (is_string($titles)) {
            return [app()->getLocale() => $titles];
        }

        return $titles ?? [];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Get blocks from content.
     * Supports both Filament Builder format (flat array with 'type' and 'data')
     * and legacy Repeater format (nested under 'blocks' key).
     */
    public function getBlocks(): array
    {
        $content = $this->content ?? [];

        // Filament Builder stores blocks as flat array with 'type' and 'data' keys
        if (isset($content[0]['type'])) {
            return $content;
        }

        // Legacy format: blocks nested under 'blocks' key
        $blocks = $content['blocks'] ?? [];

        usort($blocks, fn ($a, $b) => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));

        return $blocks;
    }
}

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

    public const BLOCK_TYPE_TEXT = 'text';

    public const BLOCK_TYPE_IMAGE = 'image';

    public const BLOCK_TYPE_CTA = 'cta';

    public const BLOCK_TYPE_FORM_EMBED = 'form_embed';

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
            'content' => 'array',
            'seo_meta' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public static function blockTypes(): array
    {
        return [
            self::BLOCK_TYPE_TEXT => 'Text',
            self::BLOCK_TYPE_IMAGE => 'Image',
            self::BLOCK_TYPE_CTA => 'Call to Action',
            self::BLOCK_TYPE_FORM_EMBED => 'Form Embed',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function getBlocks(): array
    {
        $blocks = $this->content['blocks'] ?? [];

        usort($blocks, fn ($a, $b) => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));

        return $blocks;
    }
}

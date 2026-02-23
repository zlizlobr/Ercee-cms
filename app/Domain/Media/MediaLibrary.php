<?php

namespace App\Domain\Media;

use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * Aggregate for media library metadata and conversion configuration.
 */
class MediaLibrary extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_library_items';

    protected $fillable = [
        'title',
        'alt_text',
        'focal_point',
        'tags',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'focal_point' => 'array',
            'tags' => 'array',
        ];
    }

    /**
     * Registers the main media collection used by the domain.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->singleFile()
            ->useDisk('media');
    }

    /**
     * Registers conversion variants for stored media items.
     */
    public function registerMediaConversions(?SpatieMedia $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 150, 150)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 600, 600)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 1200, 1200)
            ->nonQueued();

        $this->addMediaConversion('webp')
            ->format('webp')
            ->fit(Fit::Contain, 1200, 1200)
            ->nonQueued();
    }
}


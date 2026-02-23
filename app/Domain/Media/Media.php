<?php

namespace App\Domain\Media;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

/**
 * Media model extension that defines project image conversions.
 */
class Media extends BaseMedia
{
    /**
     * Registers image conversion variants for uploaded media.
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
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

<?php

namespace App\Domain\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Generates media storage paths based on UUID directory layout.
 */
class UuidPathGenerator implements PathGenerator
{
    /**
     * Returns base path for original media file.
     */
    public function getPath(Media $media): string
    {
        return $media->uuid.'/';
    }

    /**
     * Returns path for conversion files.
     */
    public function getPathForConversions(Media $media): string
    {
        return $media->uuid.'/conversions/';
    }

    /**
     * Returns path for responsive image files.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $media->uuid.'/responsive/';
    }
}


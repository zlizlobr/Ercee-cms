<?php

namespace App\Domain\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class UuidPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $media->uuid.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $media->uuid.'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $media->uuid.'/responsive/';
    }
}

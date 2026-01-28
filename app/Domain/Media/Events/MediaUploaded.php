<?php

declare(strict_types=1);

namespace App\Domain\Media\Events;

use App\Contracts\Events\BaseDomainEvent;
use App\Domain\Media\Media;

class MediaUploaded extends BaseDomainEvent
{
    public function __construct(
        public Media $media
    ) {
        parent::__construct();
    }

    public function getPayload(): array
    {
        return [
            'media_id' => $this->media->id,
            'name' => $this->media->name,
            'file_name' => $this->media->file_name,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Media\Events;

use App\Contracts\Events\BaseDomainEvent;
use App\Domain\Media\Media;

/**
 * Domain event fired after media is uploaded and persisted.
 */
class MediaUploaded extends BaseDomainEvent
{
    /**
     * @param Media $media Uploaded media entity.
     */
    public function __construct(
        public Media $media
    ) {
        parent::__construct();
    }

    /**
     * @return array{media_id: int|string|null, name: string|null, file_name: string|null}
     */
    public function getPayload(): array
    {
        return [
            'media_id' => $this->media->id,
            'name' => $this->media->name,
            'file_name' => $this->media->file_name,
        ];
    }
}

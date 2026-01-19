<?php

namespace App\Application\Content\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class PublishPageCommand implements CommandInterface
{
    public function __construct(
        public int $pageId,
    ) {}

    public function toArray(): array
    {
        return [
            'page_id' => $this->pageId,
        ];
    }
}

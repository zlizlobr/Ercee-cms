<?php

namespace App\Application\Content\Commands;

use App\Application\Contracts\CommandInterface;

/**
 * Carries input required to publish a page.
 */
final readonly class PublishPageCommand implements CommandInterface
{
    /**
     * @param int $pageId Target page identifier.
     */
    public function __construct(
        public int $pageId,
    ) {}

    /**
     * @return array{page_id: int}
     */
    public function toArray(): array
    {
        return [
            'page_id' => $this->pageId,
        ];
    }
}

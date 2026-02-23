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
    // todo: modernizovat php 
    public function __construct(
        public int $pageId,
    ) {}

    /**
     * Convert command payload to a serializable array.
     *
     * @return array{page_id: int}
     */
    public function toArray(): array
    {
        return [
            'page_id' => $this->pageId,
        ];
    }
}

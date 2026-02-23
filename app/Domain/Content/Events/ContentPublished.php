<?php

declare(strict_types=1);

namespace App\Domain\Content\Events;

use App\Contracts\Events\BaseDomainEvent;
use App\Domain\Content\Page;

/**
 * Domain event fired when a page is published.
 */
class ContentPublished extends BaseDomainEvent
{
    /**
     * @param Page $page Published page aggregate.
     */
    public function __construct(
        public Page $page
    ) {
        parent::__construct();
    }

    /**
     * @return array{page_id: int|string|null, slug: string|null, status: string|null}
     */
    public function getPayload(): array
    {
        return [
            'page_id' => $this->page->id,
            'slug' => $this->page->slug,
            'status' => $this->page->status,
        ];
    }
}


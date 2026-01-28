<?php

declare(strict_types=1);

namespace App\Domain\Content\Events;

use App\Contracts\Events\BaseDomainEvent;
use App\Domain\Content\Page;

class ContentPublished extends BaseDomainEvent
{
    public function __construct(
        public Page $page
    ) {
        parent::__construct();
    }

    public function getPayload(): array
    {
        return [
            'page_id' => $this->page->id,
            'slug' => $this->page->slug,
            'status' => $this->page->status,
        ];
    }
}

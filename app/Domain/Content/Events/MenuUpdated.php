<?php

declare(strict_types=1);

namespace App\Domain\Content\Events;

use App\Contracts\Events\BaseDomainEvent;
use App\Domain\Content\Menu;

class MenuUpdated extends BaseDomainEvent
{
    public function __construct(
        public Menu $menu
    ) {
        parent::__construct();
    }

    public function getPayload(): array
    {
        return [
            'menu_id' => $this->menu->id,
            'name' => $this->menu->name,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Content\Events;

use App\Contracts\Events\BaseDomainEvent;
use App\Domain\Content\Menu;

/**
 * Domain event fired when menu metadata or structure changes.
 */
class MenuUpdated extends BaseDomainEvent
{
    /**
     * @param Menu $menu Updated menu aggregate.
     */
    public function __construct(
        public Menu $menu
    ) {
        parent::__construct();
    }

    /**
     * @return array{menu_id: int|string|null, name: string|null}
     */
    public function getPayload(): array
    {
        return [
            'menu_id' => $this->menu->id,
            'name' => $this->menu->name,
        ];
    }
}

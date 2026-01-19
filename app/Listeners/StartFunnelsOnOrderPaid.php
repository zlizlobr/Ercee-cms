<?php

namespace App\Listeners;

use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Funnel\Funnel;
use App\Domain\Funnel\Services\FunnelStarter;

class StartFunnelsOnOrderPaid
{
    public function __construct(
        protected FunnelStarter $funnelStarter
    ) {}

    public function handle(OrderPaid $event): void
    {
        $this->funnelStarter->startByTrigger(
            Funnel::TRIGGER_ORDER_PAID,
            $event->subscriber
        );
    }
}

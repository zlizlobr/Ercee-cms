<?php

namespace App\Listeners;

use App\Application\Funnel\Commands\StartFunnelCommand;
use App\Application\Funnel\StartFunnelHandler;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Funnel\Funnel;

class StartFunnelsOnOrderPaid
{
    public function __construct(
        protected StartFunnelHandler $startFunnelHandler
    ) {}

    public function handle(OrderPaid $event): void
    {
        $command = new StartFunnelCommand(
            trigger: Funnel::TRIGGER_ORDER_PAID,
            subscriberId: $event->subscriber->id,
        );

        $this->startFunnelHandler->handle($command);
    }
}

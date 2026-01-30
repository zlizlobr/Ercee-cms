<?php

declare(strict_types=1);

namespace Modules\Funnel\Listeners;

use Modules\Commerce\Domain\Events\OrderPaid;
use Modules\Funnel\Domain\Funnel;
use Modules\Funnel\Domain\Services\FunnelStarter;

class StartFunnelOnOrderPaid
{
    public function __construct(
        private FunnelStarter $funnelStarter
    ) {}

    public function handle(OrderPaid $event): void
    {
        $this->funnelStarter->startByTrigger(
            Funnel::TRIGGER_ORDER_PAID,
            $event->subscriber
        );
    }
}

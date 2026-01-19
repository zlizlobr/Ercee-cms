<?php

namespace App\Listeners;

use App\Application\Funnel\Commands\StartFunnelCommand;
use App\Application\Funnel\StartFunnelHandler;
use App\Domain\Form\Events\ContractCreated;
use App\Domain\Funnel\Funnel;

class StartFunnelsOnContractCreated
{
    public function __construct(
        protected StartFunnelHandler $startFunnelHandler
    ) {}

    public function handle(ContractCreated $event): void
    {
        $command = new StartFunnelCommand(
            trigger: Funnel::TRIGGER_CONTRACT_CREATED,
            subscriberId: $event->subscriber->id,
        );

        $this->startFunnelHandler->handle($command);
    }
}

<?php

namespace App\Listeners;

use App\Domain\Form\Events\ContractCreated;
use App\Domain\Funnel\Funnel;
use App\Domain\Funnel\Services\FunnelStarter;

class StartFunnelsOnContractCreated
{
    public function __construct(
        protected FunnelStarter $funnelStarter
    ) {}

    public function handle(ContractCreated $event): void
    {
        $this->funnelStarter->startByTrigger(
            Funnel::TRIGGER_CONTRACT_CREATED,
            $event->subscriber
        );
    }
}

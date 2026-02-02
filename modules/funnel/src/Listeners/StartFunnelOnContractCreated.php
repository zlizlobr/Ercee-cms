<?php

declare(strict_types=1);

namespace Modules\Funnel\Listeners;

use Modules\Forms\Domain\Events\ContractCreated;
use Modules\Funnel\Domain\Funnel;
use Modules\Funnel\Domain\Services\FunnelStarter;

class StartFunnelOnContractCreated
{
    public function __construct(
        private FunnelStarter $funnelStarter
    ) {}

    public function handle(ContractCreated $event): void
    {
        $this->funnelStarter->startByTrigger(
            Funnel::TRIGGER_CONTRACT_CREATED,
            $event->subscriber
        );
    }
}

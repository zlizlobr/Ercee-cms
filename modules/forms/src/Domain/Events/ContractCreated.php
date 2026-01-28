<?php

namespace Modules\Forms\Domain\Events;

use App\Domain\Subscriber\Subscriber;
use Modules\Forms\Domain\Contract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Contract $contract,
        public Subscriber $subscriber
    ) {}
}

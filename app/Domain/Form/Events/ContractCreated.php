<?php

namespace App\Domain\Form\Events;

use App\Domain\Form\Contract;
use App\Domain\Subscriber\Subscriber;
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

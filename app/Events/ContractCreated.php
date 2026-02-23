<?php

namespace App\Events;

use App\Domain\Subscriber\Subscriber;
use Modules\Forms\Domain\Contract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Publishes context about a newly created contract for downstream listeners.
 */
class ContractCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Contract $contract Newly created contract entity.
     * @param Subscriber|null $subscriber Related subscriber when available.
     */
    public function __construct(
        public Contract $contract,
        public ?Subscriber $subscriber
    ) {}
}

<?php

namespace Modules\Commerce\Domain\Events;

use App\Domain\Subscriber\Subscriber;
use Modules\Commerce\Domain\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public Subscriber $subscriber
    ) {}
}

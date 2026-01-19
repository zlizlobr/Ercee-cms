<?php

namespace App\Domain\Commerce\Events;

use App\Domain\Commerce\Order;
use App\Domain\Subscriber\Subscriber;
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

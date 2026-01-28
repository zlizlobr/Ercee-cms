<?php

namespace Modules\Commerce\Domain\Contracts;

use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\PaymentResult;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order): string;

    public function handleWebhook(Request $request): PaymentResult;

    public function getGatewayName(): string;
}

<?php

namespace App\Domain\Commerce\Contracts;

use App\Domain\Commerce\Order;
use App\Domain\Commerce\PaymentResult;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Create a payment session and return redirect URL.
     */
    public function createPayment(Order $order): string;

    /**
     * Handle webhook callback from payment provider.
     */
    public function handleWebhook(Request $request): PaymentResult;

    /**
     * Get the gateway identifier.
     */
    public function getGatewayName(): string;
}

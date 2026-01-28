<?php

namespace App\Domain\Commerce\Contracts;

use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface as ModulePaymentGatewayInterface;

interface PaymentGatewayInterface extends ModulePaymentGatewayInterface
{
    // Alias for backwards compatibility - use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface instead
}

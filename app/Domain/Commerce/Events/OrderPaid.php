<?php

namespace App\Domain\Commerce\Events;

use Modules\Commerce\Domain\Events\OrderPaid as ModuleOrderPaid;

class OrderPaid extends ModuleOrderPaid
{
    // Alias for backwards compatibility - use Modules\Commerce\Domain\Events\OrderPaid instead
}

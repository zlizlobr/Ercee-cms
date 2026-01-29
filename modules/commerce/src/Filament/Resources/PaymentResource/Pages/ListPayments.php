<?php

namespace Modules\Commerce\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Commerce\Filament\Resources\PaymentResource;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;
}

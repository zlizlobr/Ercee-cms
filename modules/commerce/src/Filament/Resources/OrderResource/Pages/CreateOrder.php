<?php

namespace Modules\Commerce\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Commerce\Filament\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}

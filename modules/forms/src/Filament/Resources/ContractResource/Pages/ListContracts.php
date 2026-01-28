<?php

namespace Modules\Forms\Filament\Resources\ContractResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Forms\Filament\Resources\ContractResource;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

<?php

namespace Modules\Forms\Filament\Resources\ContractResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Forms\Filament\Resources\ContractResource;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

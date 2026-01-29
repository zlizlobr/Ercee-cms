<?php

namespace Modules\Commerce\Filament\Resources\AttributeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Commerce\Filament\Resources\AttributeResource;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

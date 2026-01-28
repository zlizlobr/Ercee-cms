<?php

namespace Modules\Forms\Filament\Resources\FormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Forms\Filament\Resources\FormResource;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

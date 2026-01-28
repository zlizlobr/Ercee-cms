<?php

namespace Modules\Forms\Filament\Resources\FormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Forms\Filament\Resources\FormResource;

class ListForms extends ListRecords
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

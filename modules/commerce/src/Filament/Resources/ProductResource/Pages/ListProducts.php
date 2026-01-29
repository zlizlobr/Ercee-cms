<?php

namespace Modules\Commerce\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Commerce\Filament\Resources\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

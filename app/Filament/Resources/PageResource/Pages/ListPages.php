<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Lists records for the corresponding Filament resource.
 */
class ListPages extends ListRecords
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}



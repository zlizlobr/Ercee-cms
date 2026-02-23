<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Lists records for the corresponding Filament resource.
 */
class ListMedia extends ListRecords
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}



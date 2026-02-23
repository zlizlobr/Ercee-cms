<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edits an existing record in the corresponding Filament resource.
 */
class EditMedia extends EditRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}



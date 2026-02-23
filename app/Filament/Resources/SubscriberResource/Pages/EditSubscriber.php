<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edits an existing record in the corresponding Filament resource.
 */
class EditSubscriber extends EditRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}



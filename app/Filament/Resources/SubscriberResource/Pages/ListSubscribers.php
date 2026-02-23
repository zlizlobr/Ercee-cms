<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Lists records for the corresponding Filament resource.
 */
class ListSubscribers extends ListRecords
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}



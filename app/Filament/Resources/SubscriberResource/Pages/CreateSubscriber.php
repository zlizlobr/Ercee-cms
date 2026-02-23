<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new record for the corresponding Filament resource.
 */
class CreateSubscriber extends CreateRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = SubscriberResource::class;
}



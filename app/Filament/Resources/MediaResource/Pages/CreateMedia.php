<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new record for the corresponding Filament resource.
 */
class CreateMedia extends CreateRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = MediaResource::class;
}



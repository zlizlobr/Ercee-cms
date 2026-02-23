<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new record for the corresponding Filament resource.
 */
class CreatePage extends CreateRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = PageResource::class;
}



<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new menu record.
 *
 * @extends CreateRecord<\App\Domain\Content\Menu>
 */
class CreateMenu extends CreateRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = MenuResource::class;
}



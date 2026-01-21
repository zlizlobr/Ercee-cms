<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new menu record.
 *
 * @extends CreateRecord<\App\Domain\Content\Menu>
 */
class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;
}

<?php

namespace App\Filament\Resources\NavigationResource\Pages;

use App\Filament\Resources\NavigationResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new navigation record.
 *
 * @extends CreateRecord<\App\Domain\Content\Navigation>
 */
class CreateNavigation extends CreateRecord
{
    protected static string $resource = NavigationResource::class;
}

<?php

namespace App\Filament\Resources\NavigationResource\Pages;

use App\Filament\Resources\NavigationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Lists navigation records in the admin panel.
 *
 * @extends ListRecords<\App\Domain\Content\Navigation>
 */
class ListNavigations extends ListRecords
{
    protected static string $resource = NavigationResource::class;

    /**
     * Header actions for the navigation list.
     *
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

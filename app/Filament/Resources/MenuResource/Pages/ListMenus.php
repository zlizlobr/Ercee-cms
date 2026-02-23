<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Lists menu records in the admin panel.
 *
 * @extends ListRecords<\App\Domain\Content\Menu>
 */
class ListMenus extends ListRecords
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = MenuResource::class;

    /**
     * Header actions for the menu list.
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



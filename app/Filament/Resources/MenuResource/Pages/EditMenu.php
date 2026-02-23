<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edits an existing menu record.
 *
 * @extends EditRecord<\App\Domain\Content\Menu>
 */
class EditMenu extends EditRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = MenuResource::class;

    /**
     * Header actions for the edit menu page.
     *
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}



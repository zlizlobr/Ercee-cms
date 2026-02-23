<?php

namespace App\Filament\Resources\NavigationResource\Pages;

use App\Filament\Resources\NavigationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edits an existing navigation record.
 *
 * @extends EditRecord<\App\Domain\Content\Navigation>
 */
class EditNavigation extends EditRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = NavigationResource::class;

    /**
     * Header actions for the edit navigation page.
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



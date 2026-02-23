<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edits an existing record in the corresponding Filament resource.
 */
class EditPage extends EditRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label(__('admin.actions.preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('admin.pages.preview', ['page' => $this->record]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}



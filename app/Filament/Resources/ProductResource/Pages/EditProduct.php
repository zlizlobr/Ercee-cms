<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label(__('admin.actions.preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('admin.products.preview', ['product' => $this->record]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace Modules\Funnel\Filament\Resources\FunnelResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Funnel\Filament\Resources\FunnelResource;

class EditFunnel extends EditRecord
{
    protected static string $resource = FunnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

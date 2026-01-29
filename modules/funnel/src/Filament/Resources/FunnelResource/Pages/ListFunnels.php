<?php

namespace Modules\Funnel\Filament\Resources\FunnelResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Funnel\Filament\Resources\FunnelResource;

class ListFunnels extends ListRecords
{
    protected static string $resource = FunnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

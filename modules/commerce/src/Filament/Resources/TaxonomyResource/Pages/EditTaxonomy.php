<?php

namespace Modules\Commerce\Filament\Resources\TaxonomyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Commerce\Filament\Resources\TaxonomyResource;

class EditTaxonomy extends EditRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

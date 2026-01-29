<?php

namespace Modules\Commerce\Filament\Resources\ProductReviewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Commerce\Filament\Resources\ProductReviewResource;

class ListProductReviews extends ListRecords
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace Modules\Commerce\Filament\Resources\ProductReviewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Commerce\Filament\Resources\ProductReviewResource;

class EditProductReview extends EditRecord
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

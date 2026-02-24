<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Domain\Content\Page;
use App\Filament\Resources\PageResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creates a new record for the corresponding Filament resource.
 */
class CreatePage extends CreateRecord
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = PageResource::class;

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getPublishFormAction(),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getPublishFormAction(): Action
    {
        return Action::make('publish')
            ->label(__('admin.actions.publish'))
            ->color('success')
            ->action('publish')
            ->visible(fn (): bool => ($this->data['status'] ?? Page::STATUS_DRAFT) !== Page::STATUS_PUBLISHED);
    }

    public function publish(): void
    {
        $this->data['status'] = Page::STATUS_PUBLISHED;

        $this->create();
    }
}

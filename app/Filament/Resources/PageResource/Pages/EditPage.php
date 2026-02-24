<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Domain\Content\Page;
use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
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

    /**
     * @return array<Actions\Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getPublishFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getPublishFormAction(): Actions\Action
    {
        return Actions\Action::make('publish')
            ->label(__('admin.actions.publish'))
            ->color('success')
            ->action('publish')
            ->visible(fn (): bool => ($this->data['status'] ?? $this->record->status) !== Page::STATUS_PUBLISHED);
    }

    public function publish(): void
    {
        $this->data['status'] = Page::STATUS_PUBLISHED;

        $this->save();
    }
}

<?php

namespace App\Filament\Components;

use App\Domain\Media\MediaLibrary;
use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Collection;

class MediaPicker extends Field
{
    protected string $view = 'filament.components.media-picker';

    protected bool|Closure $isMultiple = false;

    protected array|Closure $acceptedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (MediaPicker $component, $state): void {
            if (blank($state)) {
                $component->state(null);
                return;
            }

            if ($this->isMultiple()) {
                $component->state(is_array($state) ? $state : [$state]);
            } else {
                $component->state(is_array($state) ? ($state[0] ?? null) : $state);
            }
        });

        $this->dehydrateStateUsing(function ($state) {
            if (blank($state)) {
                return null;
            }

            return $this->isMultiple() ? array_values(array_filter((array) $state)) : $state;
        });

        $this->registerActions([
            Action::make('selectMedia')
                ->label(__('Select Media'))
                ->icon('heroicon-o-photo')
                ->color('gray')
                ->modalHeading(__('Select Media'))
                ->modalWidth('5xl')
                ->modalContent(fn () => view('filament.components.media-picker-modal', [
                    'media' => $this->getMediaItems(),
                    'selected' => $this->getState(),
                    'multiple' => $this->isMultiple(),
                ]))
                ->action(function (array $data, MediaPicker $component): void {
                    // Action handled via Livewire
                }),

            Action::make('clearMedia')
                ->label(__('Clear'))
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn (MediaPicker $component) => $component->state(null)),
        ]);
    }

    public function multiple(bool|Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function acceptedFileTypes(array|Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        return (array) $this->evaluate($this->acceptedFileTypes);
    }

    public function getMediaItems(): Collection
    {
        return MediaLibrary::with('media')
            ->latest()
            ->take(50)
            ->get();
    }

    public function getSelectedMedia(): ?MediaLibrary
    {
        $state = $this->getState();

        if (blank($state)) {
            return null;
        }

        $uuid = is_array($state) ? ($state[0] ?? null) : $state;

        if (! $uuid) {
            return null;
        }

        return MediaLibrary::whereHas('media', function ($query) use ($uuid) {
            $query->where('uuid', $uuid);
        })->with('media')->first();
    }

    public function getSelectedMediaCollection(): Collection
    {
        $state = $this->getState();

        if (blank($state)) {
            return collect();
        }

        $uuids = (array) $state;

        return MediaLibrary::whereHas('media', function ($query) use ($uuids) {
            $query->whereIn('uuid', $uuids);
        })->with('media')->get();
    }
}

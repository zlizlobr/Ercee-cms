<?php

namespace App\Filament\Components;

use App\Domain\Media\MediaLibrary;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Filament field for selecting or uploading media library items.
 */
class MediaPicker extends Field
{
    /**
     * @var string Blade view identifier used to render this Filament component.
     */
    protected string $view = 'filament.components.media-picker';

    /**
     * @var bool|Closure Flag or evaluated closure that controls multiple selection mode.
     */
    protected bool|Closure $isMultiple = false;

    /**
     * @var array|Closure Allowed MIME types for uploaded files in this picker.
     */
    protected array|Closure $acceptedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    /**
     * @var int|Closure Maximum upload size in kilobytes accepted by the picker.
     */
    protected int|Closure $maxFileSize = 10240; // 10MB

    /**
     * Configure the field and register actions.
     */
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

        $this->afterStateUpdated(function (MediaPicker $component, $state): void {
            $livewire = $component->getLivewire();
            $record = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;

            Log::info('MediaPicker state updated', [
                'state_path' => $component->getStatePath(),
                'state' => $state,
                'is_multiple' => $component->isMultiple(),
                'livewire' => $livewire ? $livewire::class : null,
                'record_id' => $record?->getKey(),
            ]);
        });

        $this->dehydrateStateUsing(function ($state) {
            if (blank($state)) {
                return null;
            }

            return $this->isMultiple() ? array_values(array_filter((array) $state)) : $state;
        });

        $this->registerActions([
            Action::make('uploadMedia')
                ->label(__('Upload New'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading(__('Upload New Media'))
                ->modalWidth('lg')
                ->form(fn (): array => [
                    Forms\Components\FileUpload::make('file')
                        ->label(__('File'))
                        ->image()
                        ->imageEditor()
                        ->required()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                        ->maxSize(10240)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private'),
                    Forms\Components\TextInput::make('title')
                        ->label(__('Title'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('alt_text')
                        ->label(__('Alt Text'))
                        ->maxLength(255),
                ])
                ->action(function (array $data, MediaPicker $component): void {
                    $fileData = $data['file'] ?? null;

                    if (! $fileData) {
                        return;
                    }

                    $fileValue = is_array($fileData) ? ($fileData[0] ?? null) : $fileData;

                    if (! $fileValue) {
                        return;
                    }

                    $fileName = basename($fileValue);

                    $possiblePaths = [
                        storage_path('app/private/livewire-tmp/' . $fileName),
                        storage_path('app/livewire-tmp/' . $fileName),
                        storage_path('app/public/livewire-tmp/' . $fileName),
                        storage_path('app/' . $fileValue),
                    ];

                    $filePath = null;
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            $filePath = $path;
                            break;
                        }
                    }

                    if (! $filePath) {
                        return;
                    }

                    $mediaItem = MediaLibrary::create([
                        'title' => $data['title'] ?? pathinfo($fileName, PATHINFO_FILENAME),
                        'alt_text' => $data['alt_text'] ?? null,
                    ]);

                    $media = $mediaItem
                        ->addMedia($filePath)
                        ->toMediaCollection('default');

                    if ($component->isMultiple()) {
                        $currentState = (array) $component->getState();
                        $currentState[] = $media->uuid;
                        $component->state($currentState);
                    } else {
                        $component->state($media->uuid);
                    }
                }),

            Action::make('clearMedia')
                ->label(__('Clear'))
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (MediaPicker $component) => filled($component->getState()))
                ->action(fn (MediaPicker $component) => $component->state(null)),
        ]);
    }

    /**
     * Enable or disable multi-select mode.
     */
    public function multiple(bool|Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    /**
     * Determine if multi-select mode is active.
     */
    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    /**
     * Set accepted MIME types for uploads.
     *
     * @param array<int, string>|Closure $types
     */
    public function acceptedFileTypes(array|Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    /**
     * Get accepted MIME types for uploads.
     *
     * @return array<int, string>
     */
    public function getAcceptedFileTypes(): array
    {
        return (array) $this->evaluate($this->acceptedFileTypes);
    }

    /**
     * Set the maximum upload size in kilobytes.
     */
    public function maxFileSize(int|Closure $size): static
    {
        $this->maxFileSize = $size;

        return $this;
    }

    /**
     * Get the maximum upload size in kilobytes.
     */
    public function getMaxFileSize(): int
    {
        return (int) $this->evaluate($this->maxFileSize);
    }

    /**
     * Get the most recent media items for the picker list.
     *
     * @return Collection<int, MediaLibrary>
     */
    public function getMediaItems(): Collection
    {
        return MediaLibrary::with('media')
            ->latest()
            ->take(50)
            ->get();
    }

    /**
     * Get the first selected media item, if any.
     */
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

    /**
     * Get all selected media items.
     *
     * @return Collection<int, MediaLibrary>
     */
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



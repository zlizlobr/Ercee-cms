<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            selectMedia(uuid) {
                @if($isMultiple())
                    if (!Array.isArray(this.state)) this.state = [];
                    if (this.state.includes(uuid)) {
                        this.state = this.state.filter(id => id !== uuid);
                    } else {
                        this.state.push(uuid);
                    }
                @else
                    this.state = uuid;
                @endif
                $dispatch('close-modal', { id: '{{ $getId() }}-media-modal' });
            }
        }"
        class="space-y-2"
    >
        {{-- Selected Media Preview --}}
        @php
            $selectedMedia = $isMultiple() ? $getSelectedMediaCollection() : ($getSelectedMedia() ? collect([$getSelectedMedia()]) : collect());
        @endphp

        @if($selectedMedia->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach($selectedMedia as $mediaItem)
                    @php
                        $media = $mediaItem->getFirstMedia('default');
                    @endphp
                    @if($media)
                        <div class="relative group">
                            <img
                                src="{{ $media->getUrl('thumb') }}"
                                alt="{{ $mediaItem->alt_text }}"
                                class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                            >
                            <button
                                type="button"
                                wire:click="$set('{{ $getStatePath() }}', null)"
                                class="absolute -top-2 -right-2 bg-danger-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                <x-heroicon-m-x-mark class="w-3 h-3" />
                            </button>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-photo"
                x-on:click="$dispatch('open-modal', { id: '{{ $getId() }}-media-modal' })"
            >
                {{ $selectedMedia->isEmpty() ? __('Select Media') : __('Change Media') }}
            </x-filament::button>

            @if($selectedMedia->isNotEmpty())
                <x-filament::button
                    type="button"
                    color="danger"
                    icon="heroicon-o-x-mark"
                    wire:click="$set('{{ $getStatePath() }}', null)"
                >
                    {{ __('Clear') }}
                </x-filament::button>
            @endif
        </div>

        {{-- Media Selection Modal --}}
        <x-filament::modal id="{{ $getId() }}-media-modal" width="5xl">
            <x-slot name="heading">
                {{ __('Select Media') }}
            </x-slot>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 max-h-[60vh] overflow-y-auto p-2">
                @foreach($getMediaItems() as $mediaItem)
                    @php
                        $media = $mediaItem->getFirstMedia('default');
                        $uuid = $media?->uuid;
                        $isSelected = $isMultiple()
                            ? in_array($uuid, (array) $getState())
                            : $getState() === $uuid;
                    @endphp
                    @if($media)
                        <button
                            type="button"
                            x-on:click="selectMedia('{{ $uuid }}')"
                            @class([
                                'relative aspect-square rounded-lg overflow-hidden border-2 transition-all hover:scale-105',
                                'border-primary-500 ring-2 ring-primary-500' => $isSelected,
                                'border-gray-200 dark:border-gray-700 hover:border-gray-300' => !$isSelected,
                            ])
                        >
                            <img
                                src="{{ $media->getUrl('thumb') }}"
                                alt="{{ $mediaItem->alt_text }}"
                                class="w-full h-full object-cover"
                            >
                            @if($isSelected)
                                <div class="absolute inset-0 bg-primary-500/20 flex items-center justify-center">
                                    <x-heroicon-s-check-circle class="w-8 h-8 text-primary-500" />
                                </div>
                            @endif
                            <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs p-1 truncate">
                                {{ $mediaItem->title ?: $media->file_name }}
                            </div>
                        </button>
                    @endif
                @endforeach
            </div>

            <x-slot name="footer">
                <x-filament::button
                    color="gray"
                    x-on:click="$dispatch('close-modal', { id: '{{ $getId() }}-media-modal' })"
                >
                    {{ __('Close') }}
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>
</x-dynamic-component>

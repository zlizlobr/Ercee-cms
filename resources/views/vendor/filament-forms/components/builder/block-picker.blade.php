@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'statePath',
    'trigger',
    'width' => null,
])

@php
    $grouped = collect($blocks)->groupBy(
        fn ($block) => \App\Filament\Blocks\BlockRegistry::getGroupForBlock($block->getName())
            ?? __('admin.page.block_groups.content')
    );
@endphp

<div
    x-data="{ isOpen: false }"
    x-on:keydown.escape.window="isOpen = false"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <div
        x-on:click="isOpen = true"
        {{ $trigger->attributes->class(['fi-dropdown-trigger flex cursor-pointer']) }}
    >
        {{ $trigger }}
    </div>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="isOpen"
            x-transition.opacity
            class="fixed inset-x-0 bottom-0 top-16 z-[200] flex items-center justify-center p-3 sm:p-6"
            x-on:click.self="isOpen = false"
        >
            <div class="absolute inset-0 bg-gray-950/40"></div>

            <div
                class="fi-fo-builder-block-picker-modal relative w-[min(92vw,70rem)] max-h-[min(76vh,52rem)] overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10"
                x-on:click.stop
            >
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('admin.page.actions.add_block') }}
                    </p>

                    <button
                        type="button"
                        class="inline-flex items-center rounded-md px-2 py-1 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/10 dark:hover:text-gray-100"
                        x-on:click="isOpen = false"
                    >
                        Zavrit
                    </button>
                </div>

                <div class="overflow-y-auto p-2 sm:p-3">
                    @foreach ($grouped as $groupLabel => $groupBlocks)
                        <x-filament::dropdown.header
                            class="px-3 pt-2 pb-1 text-xs font-semibold uppercase tracking-wider border-b border-gray-200 dark:border-gray-700"
                        >
                            {{ $groupLabel }}
                        </x-filament::dropdown.header>

                        <x-filament::grid
                            :default="$columns['default'] ?? 1"
                            :sm="$columns['sm'] ?? null"
                            :md="$columns['md'] ?? null"
                            :lg="$columns['lg'] ?? null"
                            :xl="$columns['xl'] ?? null"
                            :two-xl="$columns['2xl'] ?? null"
                            direction="column"
                        >
                            @foreach ($groupBlocks as $block)
                                @php
                                    $wireClickActionArguments = ['block' => $block->getName()];

                                    if (filled($afterItem)) {
                                        $wireClickActionArguments['afterItem'] = $afterItem;
                                    }

                                    $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                                    $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
                                @endphp

                                <x-filament::dropdown.list.item
                                    :icon="$block->getIcon()"
                                    x-on:click="isOpen = false"
                                    :wire:click="$wireClickAction"
                                >
                                    {{ $block->getLabel() }}
                                </x-filament::dropdown.list.item>
                            @endforeach
                        </x-filament::grid>
                    @endforeach
                </div>
            </div>
        </div>
    </template>
</div>

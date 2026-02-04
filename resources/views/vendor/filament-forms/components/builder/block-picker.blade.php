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

<x-filament::dropdown
    :width="$width"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($grouped as $groupLabel => $groupBlocks)
            <li class="fi-dropdown-header px-3 pt-2 pb-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                      style="border-bottom: 1px solid rgb(229 231 235); display: block; padding-bottom: 0.25rem;">
                    {{ $groupLabel }}
                </span>
            </li>

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
                        x-on:click="close"
                        :wire:click="$wireClickAction"
                    >
                        {{ $block->getLabel() }}
                    </x-filament::dropdown.list.item>
                @endforeach
            </x-filament::grid>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>

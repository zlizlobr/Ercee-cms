<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the documentation search block.
 */
class DocumentationSearchBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 20;

    /**
     * @var string Group key used to place the block into a picker section.
     */
    public static string $group = 'layout';
    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_DOCUMENTATION_SEARCH)
            ->label(__('admin.page.blocks.documentation_search'))
            ->icon('heroicon-o-magnifying-glass')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('placeholder')
                    ->label(__('admin.page.fields.placeholder'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('button_label')
                    ->label(__('admin.page.fields.button_label'))
                    ->maxLength(80),
                Forms\Components\Repeater::make('quick_links')
                    ->label(__('admin.page.fields.quick_links'))
                    ->schema([
                    Forms\Components\TextInput::make('label')
                        ->label(__('admin.page.fields.label'))
                        ->required()
                        ->maxLength(80),
                    Forms\Components\TextInput::make('anchor')
                        ->label(__('admin.page.fields.anchor'))
                        ->required()
                        ->maxLength(80)
                        ->placeholder(__('admin.page.fields.anchor_placeholder')),
                    ])
                    ->minItems(0),
            ]);
    }
}



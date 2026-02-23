<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the map placeholder block.
 */
class MapPlaceholderBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 65;

    /**
     * @var string Group key used to place the block into a picker section.
     */
    public static string $group = 'layout';
    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_MAP_PLACEHOLDER)
            ->label(__('admin.page.blocks.map_placeholder'))
            ->icon('heroicon-o-map')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.title'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.page.fields.description'))
                    ->rows(3)
                    ->maxLength(600)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('note')
                    ->label(__('admin.page.fields.note'))
                    ->maxLength(200)
                    ->columnSpanFull(),
            ]);
    }
}



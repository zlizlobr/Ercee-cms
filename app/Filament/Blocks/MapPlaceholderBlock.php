<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class MapPlaceholderBlock extends BaseBlock
{
    public static int $order = 65;

    public static string $group = 'layout';
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

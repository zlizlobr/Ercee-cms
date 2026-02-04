<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class StatsCardsBlock extends BaseBlock
{
    public static int $order = 50;

    public static string $group = 'data';
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_STATS_CARDS)
            ->label(__('admin.page.blocks.stats_cards'))
            ->icon('heroicon-o-chart-bar')
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
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
                    ->schema([
                    Forms\Components\TextInput::make('value')
                        ->label(__('admin.page.fields.value'))
                        ->required()
                        ->maxLength(40),
                    Forms\Components\TextInput::make('label')
                        ->label(__('admin.page.fields.label'))
                        ->required()
                        ->maxLength(120),
                    IconPicker::make()->field(),
                    ])
                    ->defaultItems(3)
                    ->minItems(1),
            ]);
    }
}

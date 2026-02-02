<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FacilityStatsBlock extends BaseBlock
{
    public static int $order = 30;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_FACILITY_STATS)
            ->label(__('admin.page.blocks.facility_stats'))
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
                Forms\Components\Repeater::make('stats')
                    ->label(__('admin.page.fields.stats'))
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

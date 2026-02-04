<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class StatsShowcaseBlock extends BaseBlock
{
    public static int $order = 85;

    public static string $group = 'data';
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_STATS_SHOWCASE)
            ->label(__('admin.page.blocks.stats_showcase'))
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
                MediaPicker::make('background_media_uuid')
                    ->label(__('admin.page.fields.background_media_uuid'))
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
                Forms\Components\Repeater::make('logos')
                    ->label(__('admin.page.fields.logos'))
                    ->schema([
                    Forms\Components\TextInput::make('label')
                        ->label(__('admin.page.fields.label'))
                        ->required()
                        ->maxLength(80),
                    ])
                    ->minItems(0),
            ]);
    }
}

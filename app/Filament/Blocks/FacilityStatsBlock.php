<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the facility stats block.
 */
class FacilityStatsBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 30;

    /**
     * @var string Group key used to place the block into a picker section.
     */
    public static string $group = 'data';
    /**
     * Build the block schema.
     */
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



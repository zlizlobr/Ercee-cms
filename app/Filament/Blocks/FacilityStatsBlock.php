<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
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
                    Forms\Components\Select::make('icon_key')
                        ->label(__('admin.page.fields.icon_key'))
                        ->options(['default' => 'Default', 'check' => 'Check', 'star' => 'Star', 'shield' => 'Shield', 'user' => 'User', 'mail' => 'Mail', 'phone' => 'Phone', 'building' => 'Building', 'briefcase' => 'Briefcase', 'calendar' => 'Calendar', 'file-text' => 'File text', 'message-square' => 'Message', 'globe' => 'Globe', 'map-pin' => 'Map pin', 'info' => 'Info', 'check-circle' => 'Check circle', 'chat' => 'Chat', 'cog' => 'Settings', 'support' => 'Support', 'academic' => 'Academic cap'])
                        ->searchable()
                        ->preload()
                        ->placeholder('Select icon...'),
                    ])
                    ->defaultItems(3)
                    ->minItems(1),
            ]);
    }
}

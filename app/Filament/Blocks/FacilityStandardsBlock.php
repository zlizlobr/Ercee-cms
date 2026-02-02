<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FacilityStandardsBlock extends BaseBlock
{
    public static int $order = 70;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_FACILITY_STANDARDS)
            ->label(__('admin.page.blocks.facility_standards'))
            ->icon('heroicon-o-shield-check')
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
                    IconPicker::make()->field(),
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160),
                    Forms\Components\TextInput::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->required()
                        ->maxLength(200),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }
}

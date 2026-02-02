<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class UseCaseTabsBlock extends BaseBlock
{
    public static int $order = 60;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_USE_CASE_TABS)
            ->label(__('admin.page.blocks.use_case_tabs'))
            ->icon('heroicon-o-rectangle-stack')
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
                    Forms\Components\TextInput::make('industry')
                        ->label(__('admin.page.fields.industry'))
                        ->required()
                        ->maxLength(160)
                        ->columnSpanFull(),
                    IconPicker::make()->field(),
                    MediaPicker::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('challenge')
                        ->label(__('admin.page.fields.challenge'))
                        ->rows(3)
                        ->maxLength(600)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('solution')
                        ->label(__('admin.page.fields.solution'))
                        ->rows(3)
                        ->maxLength(600)
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('results')
                        ->label(__('admin.page.fields.results'))
                        ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label(__('admin.page.fields.text'))
                            ->required()
                            ->maxLength(160),
                        ])
                        ->minItems(0),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['industry'] ?? null),
            ]);
    }
}

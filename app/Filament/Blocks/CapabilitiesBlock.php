<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Support\FormIconRegistry;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class CapabilitiesBlock extends BaseBlock
{
    public static int $order = 70;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_CAPABILITIES)
            ->label(__('admin.page.blocks.capabilities'))
            ->icon('heroicon-o-squares-2x2')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.labels.title'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
                    ->minItems(1)
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.labels.title'))
                            ->required()
                            ->maxLength(160)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('admin.labels.description'))
                            ->rows(3)
                            ->maxLength(400)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('icon')
                            ->label(__('admin.page.fields.icon'))
                            ->options(FormIconRegistry::options())
                            ->searchable()
                            ->preload()
                            ->placeholder(__('admin.page.fields.icon_placeholder')),
                        Forms\Components\Repeater::make('features')
                            ->label(__('admin.page.fields.features'))
                            ->schema([
                                Forms\Components\TextInput::make('text')
                                    ->label(__('admin.page.fields.feature'))
                                    ->required()
                                    ->maxLength(160),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

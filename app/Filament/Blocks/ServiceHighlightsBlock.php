<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\LinkPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ServiceHighlightsBlock extends BaseBlock
{
    public static int $order = 70;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_SERVICE_HIGHLIGHTS)
            ->label(__('admin.page.blocks.service_highlights'))
            ->icon('heroicon-o-briefcase')
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
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.labels.description'))
                    ->rows(3)
                    ->maxLength(400)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('more_info_label')
                    ->label(__('admin.page.fields.more_info_label'))
                    ->maxLength(80)
                    ->helperText(__('admin.page.fields.more_info_label_helper'))
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('services')
                    ->label(__('admin.page.fields.services'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.labels.title'))
                            ->required()
                            ->maxLength(160)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('admin.labels.description'))
                            ->required()
                            ->rows(3)
                            ->maxLength(400)
                            ->columnSpanFull(),
                        IconPicker::make()->field(),
                        ...LinkPicker::make('link')->fields(),
                    ])
                    ->defaultItems(4)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta.label')
                    ->label(__('admin.page.fields.cta_label'))
                    ->maxLength(80),
                ...LinkPicker::make('cta.link')->fields(),
            ]);
    }
}

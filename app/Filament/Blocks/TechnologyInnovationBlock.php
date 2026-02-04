<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\LinkPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TechnologyInnovationBlock extends BaseBlock
{
    public static int $order = 70;

    public static string $group = 'features';
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_TECHNOLOGY_INNOVATION)
            ->label(__('admin.page.blocks.technology_innovation'))
            ->icon('heroicon-o-cpu-chip')
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
                MediaPicker::make('image_media_uuid')
                    ->label(__('admin.page.fields.image_media_uuid'))
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
                    ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->rows(3)
                        ->maxLength(400)
                        ->columnSpanFull(),
                    IconPicker::make()->field(),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta.label')
                    ->label(__('admin.page.fields.cta.label'))
                    ->maxLength(80),
                ...LinkPicker::make('cta.link')->fields(),
            ]);
    }
}

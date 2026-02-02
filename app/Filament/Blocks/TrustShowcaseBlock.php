<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\LinkPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TrustShowcaseBlock extends BaseBlock
{
    public static int $order = 75;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_TRUST_SHOWCASE)
            ->label(__('admin.page.blocks.trust_showcase'))
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
                Forms\Components\Repeater::make('cards')
                    ->label(__('admin.page.fields.cards'))
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
                Forms\Components\TextInput::make('cta_title')
                    ->label(__('admin.page.fields.cta_title'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cta_description')
                    ->label(__('admin.page.fields.cta_description'))
                    ->rows(2)
                    ->maxLength(400)
                    ->columnSpanFull(),
                MediaPicker::make('cta_background_media_uuid')
                    ->label(__('admin.page.fields.cta_background_media_uuid'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cta_button.label')
                    ->label(__('admin.page.fields.cta_button.label'))
                    ->maxLength(80),
                ...LinkPicker::make('cta_button.link')->fields(),
            ]);
    }
}

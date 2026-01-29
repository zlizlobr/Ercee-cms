<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class DocumentationHeroBlock extends BaseBlock
{
    public static int $order = 10;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_DOCUMENTATION_HERO)
            ->label(__('admin.page.blocks.documentation_hero'))
            ->icon('heroicon-o-book-open')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.title'))
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.page.fields.description'))
                    ->rows(3)
                    ->maxLength(600)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('background_media_uuid')
                    ->label(__('admin.page.fields.background_media_uuid'))
                    ->helperText(__('admin.page.fields.media_uuid_helper'))
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('badges')
                    ->label(__('admin.page.fields.badges'))
                    ->schema([
                    Forms\Components\TextInput::make('text')
                        ->label(__('admin.page.fields.text'))
                        ->required()
                        ->maxLength(80),
                    ])
                    ->minItems(0),
            ]);
    }
}

<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class DocCategoriesBlock extends BaseBlock
{
    public static int $order = 30;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_DOC_CATEGORIES)
            ->label(__('admin.page.blocks.doc_categories'))
            ->icon('heroicon-o-folder-open')
            ->columns(2)
            ->schema([
                Forms\Components\Repeater::make('categories')
                    ->label(__('admin.page.fields.categories'))
                    ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160)
                        ->columnSpanFull(),
                    IconPicker::make()->field(),
                    MediaPicker::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('docs')
                        ->label(__('admin.page.fields.docs'))
                        ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.page.fields.title'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('admin.page.fields.description'))
                            ->rows(2)
                            ->maxLength(400)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('type')
                            ->label(__('admin.page.fields.type'))
                            ->maxLength(20),
                        Forms\Components\TextInput::make('size')
                            ->label(__('admin.page.fields.size'))
                            ->maxLength(20),
                        Forms\Components\TextInput::make('file_url')
                            ->label(__('admin.page.fields.file_url'))
                            ->placeholder(__('admin.page.fields.file_url_placeholder'))
                            ->columnSpanFull(),
                        ])
                        ->minItems(1)
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }
}

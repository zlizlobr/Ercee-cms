<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ImageGridBlock extends BaseBlock
{
    public static int $order = 60;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_IMAGE_GRID)
            ->label(__('admin.page.blocks.image_grid'))
            ->icon('heroicon-o-squares-plus')
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
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->maxLength(160)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->rows(2)
                        ->maxLength(300)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->helperText('Media UUID (MediaPicker in CMS).')
                        ->columnSpanFull(),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta.label')
                    ->label(__('admin.page.fields.cta.label'))
                    ->maxLength(80),
                Forms\Components\Select::make('cta.link.page_id')
                    ->label(__('admin.page.fields.cta.link.page_id'))
                    ->options([])
                    ->placeholder('Select a page...'),
                Forms\Components\TextInput::make('cta.link.url')
                    ->label(__('admin.page.fields.cta.link.url'))
                    ->placeholder('/page, #section, https://...'),
                Forms\Components\TextInput::make('cta.link.anchor')
                    ->label(__('admin.page.fields.cta.link.anchor'))
                    ->placeholder('section-id'),
            ]);
    }
}

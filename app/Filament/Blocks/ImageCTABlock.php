<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ImageCTABlock extends BaseBlock
{
    public static int $order = 80;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_IMAGE_CTA)
            ->label(__('admin.page.blocks.image_cta'))
            ->icon('heroicon-o-megaphone')
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
                    ->helperText('Media UUID (MediaPicker in CMS).')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('primary.label')
                    ->label(__('admin.page.fields.primary.label'))
                    ->maxLength(80),
                Forms\Components\Select::make('primary.link.page_id')
                    ->label(__('admin.page.fields.primary.link.page_id'))
                    ->options([])
                    ->placeholder('Select a page...'),
                Forms\Components\TextInput::make('primary.link.url')
                    ->label(__('admin.page.fields.primary.link.url'))
                    ->placeholder('/page, #section, https://...'),
                Forms\Components\TextInput::make('primary.link.anchor')
                    ->label(__('admin.page.fields.primary.link.anchor'))
                    ->placeholder('section-id'),
                Forms\Components\TextInput::make('secondary.label')
                    ->label(__('admin.page.fields.secondary.label'))
                    ->maxLength(80),
                Forms\Components\Select::make('secondary.link.page_id')
                    ->label(__('admin.page.fields.secondary.link.page_id'))
                    ->options([])
                    ->placeholder('Select a page...'),
                Forms\Components\TextInput::make('secondary.link.url')
                    ->label(__('admin.page.fields.secondary.link.url'))
                    ->placeholder('/page, #section, https://...'),
                Forms\Components\TextInput::make('secondary.link.anchor')
                    ->label(__('admin.page.fields.secondary.link.anchor'))
                    ->placeholder('section-id'),
            ]);
    }
}

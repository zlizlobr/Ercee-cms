<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\LinkPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ImageCTABlock extends BaseBlock
{
    public static int $order = 80;

    public static string $group = 'cta';
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
                MediaPicker::make('background_media_uuid')
                    ->label(__('admin.page.fields.background_image'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('primary.label')
                    ->label(__('admin.page.fields.primary.label'))
                    ->maxLength(80),
                ...LinkPicker::make('primary.link')->fields(),
                Forms\Components\TextInput::make('secondary.label')
                    ->label(__('admin.page.fields.secondary.label'))
                    ->maxLength(80),
                ...LinkPicker::make('secondary.link')->fields(),
            ]);
    }
}

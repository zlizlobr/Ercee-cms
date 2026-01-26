<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Filament block schema for a hero block using media UUIDs.
 */
class HeroBlock extends BaseBlock
{
    public static int $order = 10;

    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_HERO)
            ->label(__('admin.page.blocks.hero'))
            ->icon('heroicon-o-star')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label(__('admin.page.fields.heading'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('subheading')
                    ->label(__('admin.page.fields.subheading'))
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull(),
                MediaPicker::make('background_media_uuid')
                    ->label(__('admin.page.fields.background_image'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('button_text')
                    ->label(__('admin.page.fields.button_text'))
                    ->maxLength(100),
                Forms\Components\TextInput::make('button_url')
                    ->label(__('admin.page.fields.button_url'))
                    ->url()
                    ->maxLength(255),
            ]);
    }
}

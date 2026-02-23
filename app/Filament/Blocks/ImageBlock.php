<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Filament block schema for an image block using media UUIDs.
 */
class ImageBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 30;

    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_IMAGE)
            ->label(__('admin.page.blocks.image'))
            ->icon('heroicon-o-photo')
            ->columns(2)
            ->schema([
                MediaPicker::make('media_uuid')
                    ->label(__('admin.labels.image'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('alt')
                    ->label(__('admin.page.fields.alt'))
                    ->maxLength(255)
                    ->helperText(__('admin.page.fields.override_media_alt_text_helper')),
                Forms\Components\TextInput::make('caption')
                    ->label(__('admin.page.fields.caption'))
                    ->maxLength(255),
            ]);
    }
}



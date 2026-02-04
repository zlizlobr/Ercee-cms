<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\LinkPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Filament block schema for a hero block using media UUIDs.
 */
class HeroBlock extends BaseBlock
{
    public static int $order = 10;

    public static string $group = 'hero';

    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_HERO)
            ->label(__('admin.page.blocks.hero'))
            ->icon('heroicon-o-sparkles')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.labels.title'))
                    ->required()
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.labels.description'))
                    ->rows(3)
                    ->maxLength(500)
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
                Forms\Components\Repeater::make('stats')
                    ->label(__('admin.page.fields.stats'))
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label(__('admin.page.fields.stat_value'))
                            ->required()
                            ->maxLength(40),
                        Forms\Components\TextInput::make('label')
                            ->label(__('admin.page.fields.stat_label'))
                            ->required()
                            ->maxLength(120),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

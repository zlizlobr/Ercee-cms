<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the testimonials block.
 */
class TestimonialsBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 60;

    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_TESTIMONIALS)
            ->label(__('admin.page.blocks.testimonials'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.labels.title'))
                    ->maxLength(120)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(120)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.labels.description'))
                    ->rows(3)
                    ->maxLength(400)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('testimonials')
                    ->label(__('admin.page.fields.testimonials'))
                    ->minItems(1)
                    ->schema([
                        Forms\Components\Textarea::make('quote')
                            ->label(__('admin.page.fields.quote'))
                            ->rows(3)
                            ->required()
                            ->maxLength(600)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('admin.labels.name'))
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('role')
                            ->label(__('admin.page.fields.role'))
                            ->required()
                            ->maxLength(160),
                        Forms\Components\TextInput::make('rating')
                            ->label(__('admin.page.fields.rating'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.5)
                            ->default(5),
                        MediaPicker::make('media_uuid')
                            ->label(__('admin.page.fields.photo'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}



<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the capabilities detailed block.
 */
class CapabilitiesDetailedBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 70;

    /**
     * @var string Group key used to place the block into a picker section.
     */
    public static string $group = 'features';
    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_CAPABILITIES_DETAILED)
            ->label(__('admin.page.blocks.capabilities_detailed'))
            ->icon('heroicon-o-squares-2x2')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.title'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
                    ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->rows(3)
                        ->maxLength(400)
                        ->columnSpanFull(),
                    IconPicker::make()->field(),
                    MediaPicker::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('features')
                        ->label(__('admin.page.fields.features'))
                        ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label(__('admin.page.fields.text'))
                            ->required()
                            ->maxLength(160),
                        ])
                        ->minItems(0),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }
}



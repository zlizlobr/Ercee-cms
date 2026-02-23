<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use App\Filament\Components\LinkPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the support cards block.
 */
class SupportCardsBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 50;

    /**
     * @var string Group key used to place the block into a picker section.
     */
    public static string $group = 'data';
    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_SUPPORT_CARDS)
            ->label(__('admin.page.blocks.support_cards'))
            ->icon('heroicon-o-lifebuoy')
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
                Forms\Components\Repeater::make('cards')
                    ->label(__('admin.page.fields.cards'))
                    ->schema([
                    IconPicker::make()->field(),
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160),
                    Forms\Components\Textarea::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->rows(3)
                        ->maxLength(400),
                    Forms\Components\TextInput::make('link_label')
                        ->label(__('admin.page.fields.link_label'))
                        ->maxLength(80),
                    ...LinkPicker::make('link')->fields(),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }
}



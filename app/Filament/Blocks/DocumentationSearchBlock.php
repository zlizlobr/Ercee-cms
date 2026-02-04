<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class DocumentationSearchBlock extends BaseBlock
{
    public static int $order = 20;

    public static string $group = 'layout';
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_DOCUMENTATION_SEARCH)
            ->label(__('admin.page.blocks.documentation_search'))
            ->icon('heroicon-o-magnifying-glass')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('placeholder')
                    ->label(__('admin.page.fields.placeholder'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('button_label')
                    ->label(__('admin.page.fields.button_label'))
                    ->maxLength(80),
                Forms\Components\Repeater::make('quick_links')
                    ->label(__('admin.page.fields.quick_links'))
                    ->schema([
                    Forms\Components\TextInput::make('label')
                        ->label(__('admin.page.fields.label'))
                        ->required()
                        ->maxLength(80),
                    Forms\Components\TextInput::make('anchor')
                        ->label(__('admin.page.fields.anchor'))
                        ->required()
                        ->maxLength(80)
                        ->placeholder(__('admin.page.fields.anchor_placeholder')),
                    ])
                    ->minItems(0),
            ]);
    }
}

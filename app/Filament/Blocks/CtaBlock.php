<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class CtaBlock extends BaseBlock
{
    public static int $order = 40;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_CTA)
            ->label(__('admin.page.blocks.cta'))
            ->icon('heroicon-o-cursor-arrow-rays')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.cta_title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.labels.description'))
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('button_text')
                    ->label(__('admin.page.fields.button_text'))
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('button_url')
                    ->label(__('admin.page.fields.button_url'))
                    ->url()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('style')
                    ->label(__('admin.page.fields.style'))
                    ->options([
                        'primary' => __('admin.styles.primary'),
                        'secondary' => __('admin.styles.secondary'),
                        'outline' => __('admin.styles.outline'),
                    ])
                    ->default('primary'),
            ]);
    }
}

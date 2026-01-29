<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FAQBlock extends BaseBlock
{
    public static int $order = 40;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_FAQ)
            ->label(__('admin.page.blocks.faq'))
            ->icon('heroicon-o-question-mark-circle')
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
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
                    ->schema([
                    Forms\Components\TextInput::make('question')
                        ->label(__('admin.page.fields.question'))
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('answer')
                        ->label(__('admin.page.fields.answer'))
                        ->rows(3)
                        ->maxLength(800)
                        ->columnSpanFull(),
                    ])
                    ->defaultItems(4)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
            ]);
    }
}

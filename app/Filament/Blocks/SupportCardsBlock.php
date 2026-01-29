<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class SupportCardsBlock extends BaseBlock
{
    public static int $order = 50;

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
                    Forms\Components\Select::make('icon_key')
                        ->label(__('admin.page.fields.icon_key'))
                        ->options(['default' => 'Default', 'check' => 'Check', 'star' => 'Star', 'shield' => 'Shield', 'user' => 'User', 'mail' => 'Mail', 'phone' => 'Phone', 'building' => 'Building', 'briefcase' => 'Briefcase', 'calendar' => 'Calendar', 'file-text' => 'File text', 'message-square' => 'Message', 'globe' => 'Globe', 'map-pin' => 'Map pin', 'info' => 'Info', 'check-circle' => 'Check circle', 'chat' => 'Chat', 'cog' => 'Settings', 'support' => 'Support', 'academic' => 'Academic cap'])
                        ->searchable()
                        ->preload()
                        ->placeholder('Select icon...'),
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
                    Forms\Components\Select::make('link.page_id')
                        ->label(__('admin.page.fields.link.page_id'))
                        ->options([])
                        ->placeholder('Select a page...'),
                    Forms\Components\TextInput::make('link.url')
                        ->label(__('admin.page.fields.link.url'))
                        ->placeholder('/page, #section, https://...'),
                    Forms\Components\TextInput::make('link.anchor')
                        ->label(__('admin.page.fields.link.anchor'))
                        ->placeholder('section-id'),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }
}

<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TrustShowcaseBlock extends BaseBlock
{
    public static int $order = 75;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_TRUST_SHOWCASE)
            ->label(__('admin.page.blocks.trust_showcase'))
            ->icon('heroicon-o-shield-check')
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
                    Forms\Components\TextInput::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->required()
                        ->maxLength(200),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta_title')
                    ->label(__('admin.page.fields.cta_title'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cta_description')
                    ->label(__('admin.page.fields.cta_description'))
                    ->rows(2)
                    ->maxLength(400)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cta_background_media_uuid')
                    ->label(__('admin.page.fields.cta_background_media_uuid'))
                    ->helperText('Media UUID (MediaPicker in CMS).')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cta_button.label')
                    ->label(__('admin.page.fields.cta_button.label'))
                    ->maxLength(80),
                Forms\Components\Select::make('cta_button.link.page_id')
                    ->label(__('admin.page.fields.cta_button.link.page_id'))
                    ->options([])
                    ->placeholder('Select a page...'),
                Forms\Components\TextInput::make('cta_button.link.url')
                    ->label(__('admin.page.fields.cta_button.link.url'))
                    ->placeholder('/page, #section, https://...'),
                Forms\Components\TextInput::make('cta_button.link.anchor')
                    ->label(__('admin.page.fields.cta_button.link.anchor'))
                    ->placeholder('section-id'),
            ]);
    }
}

<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TechnologyInnovationBlock extends BaseBlock
{
    public static int $order = 70;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_TECHNOLOGY_INNOVATION)
            ->label(__('admin.page.blocks.technology_innovation'))
            ->icon('heroicon-o-cpu-chip')
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
                Forms\Components\TextInput::make('image_media_uuid')
                    ->label(__('admin.page.fields.image_media_uuid'))
                    ->helperText(__('admin.page.fields.media_uuid_helper'))
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
                    Forms\Components\Select::make('icon_key')
                        ->label(__('admin.page.fields.icon_key'))
                        ->options(['default' => 'Default', 'check' => 'Check', 'star' => 'Star', 'shield' => 'Shield', 'user' => 'User', 'mail' => 'Mail', 'phone' => 'Phone', 'building' => 'Building', 'briefcase' => 'Briefcase', 'calendar' => 'Calendar', 'file-text' => 'File text', 'message-square' => 'Message', 'globe' => 'Globe', 'map-pin' => 'Map pin', 'info' => 'Info', 'check-circle' => 'Check circle', 'chat' => 'Chat', 'cog' => 'Settings', 'support' => 'Support', 'academic' => 'Academic cap'])
                        ->searchable()
                        ->preload()
                        ->placeholder(__('admin.page.fields.icon_placeholder')),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta.label')
                    ->label(__('admin.page.fields.cta.label'))
                    ->maxLength(80),
                Forms\Components\Select::make('cta.link.page_id')
                    ->label(__('admin.page.fields.cta.link.page_id'))
                    ->options([])
                    ->placeholder(__('admin.page.fields.button_page_placeholder')),
                Forms\Components\TextInput::make('cta.link.url')
                    ->label(__('admin.page.fields.cta.link.url'))
                    ->placeholder(__('admin.page.fields.button_url_placeholder')),
                Forms\Components\TextInput::make('cta.link.anchor')
                    ->label(__('admin.page.fields.cta.link.anchor'))
                    ->placeholder(__('admin.page.fields.anchor_placeholder')),
            ]);
    }
}

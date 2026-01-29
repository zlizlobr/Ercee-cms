<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FacilityStandardsBlock extends BaseBlock
{
    public static int $order = 70;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_FACILITY_STANDARDS)
            ->label(__('admin.page.blocks.facility_standards'))
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
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
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
            ]);
    }
}

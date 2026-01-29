<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ProcessWorkflowBlock extends BaseBlock
{
    public static int $order = 75;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_PROCESS_WORKFLOW)
            ->label(__('admin.page.blocks.process_workflow'))
            ->icon('heroicon-o-arrow-path')
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
                Forms\Components\Repeater::make('steps')
                    ->label(__('admin.page.fields.steps'))
                    ->schema([
                    Forms\Components\TextInput::make('number')
                        ->label(__('admin.page.fields.number'))
                        ->required()
                        ->maxLength(4),
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
                    Forms\Components\TextInput::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->helperText(__('admin.page.fields.media_uuid_helper'))
                        ->columnSpanFull(),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\Repeater::make('benefits')
                    ->label(__('admin.page.fields.benefits'))
                    ->schema([
                    Forms\Components\Select::make('icon_key')
                        ->label(__('admin.page.fields.icon_key'))
                        ->options(['default' => 'Default', 'check' => 'Check', 'star' => 'Star', 'shield' => 'Shield', 'user' => 'User', 'mail' => 'Mail', 'phone' => 'Phone', 'building' => 'Building', 'briefcase' => 'Briefcase', 'calendar' => 'Calendar', 'file-text' => 'File text', 'message-square' => 'Message', 'globe' => 'Globe', 'map-pin' => 'Map pin', 'info' => 'Info', 'check-circle' => 'Check circle', 'chat' => 'Chat', 'cog' => 'Settings', 'support' => 'Support', 'academic' => 'Academic cap'])
                        ->searchable()
                        ->preload()
                        ->placeholder(__('admin.page.fields.icon_placeholder')),
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160),
                    Forms\Components\TextInput::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->required()
                        ->maxLength(200),
                    ])
                    ->minItems(0),
            ]);
    }
}

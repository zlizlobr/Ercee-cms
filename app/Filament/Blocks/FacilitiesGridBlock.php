<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FacilitiesGridBlock extends BaseBlock
{
    public static int $order = 60;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_FACILITIES_GRID)
            ->label(__('admin.page.blocks.facilities_grid'))
            ->icon('heroicon-o-building-office-2')
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
                    Forms\Components\TextInput::make('name')
                        ->label(__('admin.page.fields.name'))
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('location')
                        ->label(__('admin.page.fields.location'))
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('address')
                        ->label(__('admin.page.fields.address'))
                        ->maxLength(240)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('type')
                        ->label(__('admin.page.fields.type'))
                        ->maxLength(120),
                    Forms\Components\TextInput::make('size')
                        ->label(__('admin.page.fields.size'))
                        ->maxLength(120),
                    Forms\Components\Select::make('icon_key')
                        ->label(__('admin.page.fields.icon_key'))
                        ->options(['default' => 'Default', 'check' => 'Check', 'star' => 'Star', 'shield' => 'Shield', 'user' => 'User', 'mail' => 'Mail', 'phone' => 'Phone', 'building' => 'Building', 'briefcase' => 'Briefcase', 'calendar' => 'Calendar', 'file-text' => 'File text', 'message-square' => 'Message', 'globe' => 'Globe', 'map-pin' => 'Map pin', 'info' => 'Info', 'check-circle' => 'Check circle', 'chat' => 'Chat', 'cog' => 'Settings', 'support' => 'Support', 'academic' => 'Academic cap'])
                        ->searchable()
                        ->preload()
                        ->placeholder('Select icon...'),
                    Forms\Components\TextInput::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->helperText('Media UUID (MediaPicker in CMS).')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('admin.page.fields.phone'))
                        ->maxLength(120),
                    Forms\Components\TextInput::make('email')
                        ->label(__('admin.page.fields.email'))
                        ->maxLength(160),
                    Forms\Components\TextInput::make('manager')
                        ->label(__('admin.page.fields.manager'))
                        ->maxLength(160),
                    Forms\Components\TextInput::make('hours')
                        ->label(__('admin.page.fields.hours'))
                        ->maxLength(160),
                    Forms\Components\Repeater::make('features')
                        ->label(__('admin.page.fields.features'))
                        ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label(__('admin.page.fields.text'))
                            ->required()
                            ->maxLength(160),
                        ])
                        ->minItems(0),
                    Forms\Components\Repeater::make('certifications')
                        ->label(__('admin.page.fields.certifications'))
                        ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label(__('admin.page.fields.text'))
                            ->required()
                            ->maxLength(120),
                        ])
                        ->minItems(0),
                    ])
                    ->defaultItems(2)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
            ]);
    }
}

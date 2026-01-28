<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ServiceHighlightsBlock extends BaseBlock
{
    public static int $order = 70;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_SERVICE_HIGHLIGHTS)
            ->label(__('admin.page.blocks.service_highlights'))
            ->icon('heroicon-o-briefcase')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.labels.title'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('admin.page.fields.subtitle'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.labels.description'))
                    ->rows(3)
                    ->maxLength(400)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('more_info_label')
                    ->label(__('admin.page.fields.more_info_label'))
                    ->maxLength(80)
                    ->helperText('Fallback: home.services.moreInfo')
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('services')
                    ->label(__('admin.page.fields.services'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.labels.title'))
                            ->required()
                            ->maxLength(160)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('admin.labels.description'))
                            ->required()
                            ->rows(3)
                            ->maxLength(400)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('icon_key')
                            ->label(__('admin.page.fields.icon'))
                            ->options([
                                'default' => 'Default',
                                'check' => 'Check',
                                'star' => 'Star',
                                'shield' => 'Shield',
                                'user' => 'User',
                                'mail' => 'Mail',
                                'phone' => 'Phone',
                                'building' => 'Building',
                                'briefcase' => 'Briefcase',
                                'calendar' => 'Calendar',
                                'file-text' => 'File text',
                                'message-square' => 'Message',
                                'globe' => 'Globe',
                                'map-pin' => 'Map pin',
                                'info' => 'Info',
                                'check-circle' => 'Check circle',
                                'chat' => 'Chat',
                                'cog' => 'Settings',
                                'support' => 'Support',
                                'academic' => 'Academic cap',
                            ])
                            ->searchable()
                            ->preload()
                            ->placeholder(__('admin.page.fields.icon_placeholder')),
                        Forms\Components\Select::make('link.page_id')
                            ->label(__('admin.page.fields.service_page'))
                            ->options(fn () => Page::all()->mapWithKeys(
                                fn ($page) => [$page->id => $page->getLocalizedTitle()]
                            ))
                            ->searchable()
                            ->placeholder(__('admin.page.fields.button_page_placeholder'))
                            ->helperText(__('admin.page.fields.button_page_helper')),
                        Forms\Components\TextInput::make('link.url')
                            ->label(__('admin.page.fields.service_url'))
                            ->placeholder(__('admin.page.fields.button_url_placeholder'))
                            ->helperText(__('admin.page.fields.button_url_helper')),
                        Forms\Components\TextInput::make('link.anchor')
                            ->label(__('admin.page.fields.service_anchor'))
                            ->placeholder('section-id'),
                    ])
                    ->defaultItems(4)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta.label')
                    ->label(__('admin.page.fields.cta_label'))
                    ->maxLength(80),
                Forms\Components\Select::make('cta.link.page_id')
                    ->label(__('admin.page.fields.cta_page'))
                    ->options(fn () => Page::all()->mapWithKeys(
                        fn ($page) => [$page->id => $page->getLocalizedTitle()]
                    ))
                    ->searchable()
                    ->placeholder(__('admin.page.fields.button_page_placeholder'))
                    ->helperText(__('admin.page.fields.button_page_helper')),
                Forms\Components\TextInput::make('cta.link.url')
                    ->label(__('admin.page.fields.cta_url'))
                    ->placeholder(__('admin.page.fields.button_url_placeholder'))
                    ->helperText(__('admin.page.fields.button_url_helper')),
                Forms\Components\TextInput::make('cta.link.anchor')
                    ->label(__('admin.page.fields.cta_anchor'))
                    ->placeholder('section-id'),
            ]);
    }
}

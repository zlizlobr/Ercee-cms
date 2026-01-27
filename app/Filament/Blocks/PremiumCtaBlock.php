<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class PremiumCtaBlock extends BaseBlock
{
    public static int $order = 80;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_PREMIUM_CTA)
            ->label(__('admin.page.blocks.premium_cta'))
            ->icon('heroicon-o-megaphone')
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
                MediaPicker::make('background_media_uuid')
                    ->label(__('admin.page.fields.background_image'))
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('buttons')
                    ->label(__('admin.page.fields.buttons'))
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label(__('admin.page.fields.button_label'))
                            ->required()
                            ->maxLength(80),
                        Forms\Components\Select::make('page_id')
                            ->label(__('admin.page.fields.button_page'))
                            ->options(fn () => Page::all()->mapWithKeys(fn ($page) => [$page->id => $page->getLocalizedTitle()]))
                            ->searchable()
                            ->placeholder(__('admin.page.fields.button_page_placeholder'))
                            ->helperText(__('admin.page.fields.button_page_helper')),
                        Forms\Components\TextInput::make('url')
                            ->label(__('admin.page.fields.button_url'))
                            ->placeholder(__('admin.page.fields.button_url_placeholder'))
                            ->helperText(__('admin.page.fields.button_url_helper')),
                        Forms\Components\Select::make('style')
                            ->label(__('admin.page.fields.button_style'))
                            ->options([
                                'primary' => __('admin.styles.primary'),
                                'secondary' => __('admin.styles.secondary'),
                            ])
                            ->default('primary'),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('stats')
                    ->label(__('admin.page.fields.stats'))
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label(__('admin.page.fields.stat_value'))
                            ->required()
                            ->maxLength(40),
                        Forms\Components\TextInput::make('label')
                            ->label(__('admin.page.fields.stat_label'))
                            ->required()
                            ->maxLength(120),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

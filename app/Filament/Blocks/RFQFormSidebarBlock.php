<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\IconPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class RFQFormSidebarBlock extends BaseBlock
{
    public static int $order = 40;

    public static string $group = 'cta';
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_RFQ_FORM_SIDEBAR)
            ->label(__('admin.page.blocks.rfq_form_sidebar'))
            ->icon('heroicon-o-document-text')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('form_id')
                    ->label(__('admin.page.fields.form_id'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sidebar_title')
                    ->label(__('admin.page.fields.sidebar_title'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('contact_items')
                    ->label(__('admin.page.fields.contact_items'))
                    ->schema([
                    IconPicker::make()->field(),
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160),
                    Forms\Components\TextInput::make('value')
                        ->label(__('admin.page.fields.value'))
                        ->required()
                        ->maxLength(160),
                    Forms\Components\TextInput::make('helper')
                        ->label(__('admin.page.fields.helper'))
                        ->maxLength(160),
                    ])
                    ->minItems(0)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\Repeater::make('steps')
                    ->label(__('admin.page.fields.steps'))
                    ->schema([
                    Forms\Components\TextInput::make('step')
                        ->label(__('admin.page.fields.step'))
                        ->required()
                        ->maxLength(10),
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->required()
                        ->maxLength(160),
                    Forms\Components\TextInput::make('description')
                        ->label(__('admin.page.fields.description'))
                        ->required()
                        ->maxLength(200),
                    ])
                    ->minItems(0)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\Repeater::make('trust_items')
                    ->label(__('admin.page.fields.trust_items'))
                    ->schema([
                    IconPicker::make()->field(),
                    Forms\Components\TextInput::make('text')
                        ->label(__('admin.page.fields.text'))
                        ->required()
                        ->maxLength(200),
                    ])
                    ->minItems(0)
                    ->itemLabel(fn (array $state): ?string => $state['text'] ?? null),
            ]);
    }
}

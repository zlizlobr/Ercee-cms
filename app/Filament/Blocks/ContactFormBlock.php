<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ContactFormBlock extends BaseBlock
{
    public static int $order = 60;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_CONTACT_FORM)
            ->label(__('admin.page.blocks.contact_form'))
            ->icon('heroicon-o-envelope')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('form_id')
                    ->label(__('admin.page.fields.form_id'))
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.title')),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.page.fields.description'))
                    ->rows(3)
                    ->maxLength(500),
                Forms\Components\TextInput::make('success_title')
                    ->label(__('admin.page.fields.success_title'))
                    ->default('Dekujeme za zpravu!'),
                Forms\Components\Textarea::make('success_message')
                    ->label(__('admin.page.fields.success_message'))
                    ->rows(3)
                    ->maxLength(500)
                    ->default('Ozveme se vam do 24 hodin.'),
                Forms\Components\TextInput::make('submit_label')
                    ->label(__('admin.page.fields.submit_label'))
                    ->default('Odeslat zpravu'),
            ]);
    }
}

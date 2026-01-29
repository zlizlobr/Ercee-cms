<?php

namespace Modules\Forms\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Blocks\BaseBlock;
use Modules\Forms\Domain\Form;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FormEmbedBlock extends BaseBlock
{
    public static int $order = 50;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_FORM_EMBED)
            ->label(__('admin.page.blocks.form_embed'))
            ->icon('heroicon-o-clipboard-document-list')
            ->schema([
                Forms\Components\Select::make('form_id')
                    ->label(__('admin.page.fields.form_id'))
                    ->options(fn () => Form::active()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.form_title'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('admin.labels.description'))
                    ->rows(2)
                    ->maxLength(500),
            ]);
    }
}

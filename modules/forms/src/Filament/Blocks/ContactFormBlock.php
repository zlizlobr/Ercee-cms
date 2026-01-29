<?php

namespace Modules\Forms\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Blocks\BaseBlock;
use Modules\Forms\Domain\Form;
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
            ->schema([
                Forms\Components\Select::make('form_id')
                    ->label(__('admin.page.fields.form_id'))
                    ->options(fn () => Form::active()->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
            ]);
    }
}

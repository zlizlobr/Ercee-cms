<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Domain\Media\RichEditorMediaHandler;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

/**
 * Defines the Filament schema for the text block.
 */
class TextBlock extends BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 20;

    /**
     * Build the block schema.
     */
    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_TEXT)
            ->label(__('admin.page.blocks.text'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label(__('admin.page.fields.heading'))
                    ->maxLength(255),
                Forms\Components\RichEditor::make('body')
                    ->label(__('admin.page.fields.body'))
                    ->required()
                    ->fileAttachmentsDisk('media')
                    ->fileAttachmentsDirectory('richtext')
                    ->fileAttachmentsVisibility('private')
                    ->saveUploadedFileAttachmentsUsing(
                        fn ($file) => app(RichEditorMediaHandler::class)->handleUpload($file)
                    )
                    ->columnSpanFull(),
            ]);
    }
}



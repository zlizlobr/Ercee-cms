<?php

namespace App\Filament\Resources;

use App\Domain\Content\Page;
use App\Filament\Resources\PageResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Info')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content Blocks')
                    ->schema([
                        Forms\Components\Repeater::make('content.blocks')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options(Page::blockTypes())
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('data', [])),

                                Forms\Components\Hidden::make('position'),

                                // Text block fields
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('data.heading')
                                        ->label('Heading'),
                                    Forms\Components\RichEditor::make('data.body')
                                        ->label('Body Text')
                                        ->columnSpanFull(),
                                ])
                                    ->visible(fn (Get $get): bool => $get('type') === Page::BLOCK_TYPE_TEXT),

                                // Image block fields
                                Forms\Components\Group::make([
                                    Forms\Components\FileUpload::make('data.image')
                                        ->label('Image')
                                        ->image()
                                        ->directory('pages/images'),
                                    Forms\Components\TextInput::make('data.alt')
                                        ->label('Alt Text'),
                                    Forms\Components\TextInput::make('data.caption')
                                        ->label('Caption'),
                                ])
                                    ->visible(fn (Get $get): bool => $get('type') === Page::BLOCK_TYPE_IMAGE),

                                // CTA block fields
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('data.title')
                                        ->label('CTA Title'),
                                    Forms\Components\Textarea::make('data.description')
                                        ->label('Description')
                                        ->rows(2),
                                    Forms\Components\TextInput::make('data.button_text')
                                        ->label('Button Text'),
                                    Forms\Components\TextInput::make('data.button_url')
                                        ->label('Button URL')
                                        ->url(),
                                    Forms\Components\Select::make('data.style')
                                        ->label('Style')
                                        ->options([
                                            'primary' => 'Primary',
                                            'secondary' => 'Secondary',
                                            'outline' => 'Outline',
                                        ])
                                        ->default('primary'),
                                ])
                                    ->visible(fn (Get $get): bool => $get('type') === Page::BLOCK_TYPE_CTA),

                                // Form embed block fields
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('data.form_id')
                                        ->label('Form ID'),
                                    Forms\Components\TextInput::make('data.title')
                                        ->label('Form Title'),
                                    Forms\Components\Textarea::make('data.description')
                                        ->label('Description')
                                        ->rows(2),
                                ])
                                    ->visible(fn (Get $get): bool => $get('type') === Page::BLOCK_TYPE_FORM_EMBED),
                            ])
                            ->itemLabel(fn (array $state): ?string => Page::blockTypes()[$state['type'] ?? ''] ?? 'New Block')
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->cloneable()
                            ->defaultItems(0)
                            ->afterStateUpdated(function (Forms\Set $set, ?array $state) {
                                if ($state) {
                                    $blocks = collect($state)->values()->map(function ($block, $index) {
                                        $block['position'] = $index;

                                        return $block;
                                    })->toArray();
                                    $set('content.blocks', $blocks);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_meta.title')
                            ->label('SEO Title')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters'),
                        Forms\Components\Textarea::make('seo_meta.description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText('Recommended: 150-160 characters'),
                        Forms\Components\Fieldset::make('Open Graph')
                            ->schema([
                                Forms\Components\TextInput::make('seo_meta.open_graph.title')
                                    ->label('OG Title'),
                                Forms\Components\Textarea::make('seo_meta.open_graph.description')
                                    ->label('OG Description')
                                    ->rows(2),
                                Forms\Components\FileUpload::make('seo_meta.open_graph.image')
                                    ->label('OG Image')
                                    ->image()
                                    ->directory('pages/og'),
                            ]),
                    ])
                    ->collapsed(),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                Page::STATUS_DRAFT => 'Draft',
                                Page::STATUS_PUBLISHED => 'Published',
                                Page::STATUS_ARCHIVED => 'Archived',
                            ])
                            ->default(Page::STATUS_DRAFT)
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Domain\Content\Page;
use App\Filament\Blocks\BlockRegistry;
use App\Filament\Resources\PageResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.page.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.page.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.page.navigation');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.page.sections.info'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.labels.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get) {
                                if (filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug($state ?? ''));
                            }),
                    ]),

                Forms\Components\Grid::make(['default' => 1, 'lg' => 5])
                    ->schema([
                        Forms\Components\Section::make(__('admin.page.sections.content_blocks'))
                            ->schema([
                                Builder::make('content')
                                    ->blocks(BlockRegistry::all())
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->blockNumbers(false)
                                    ->addActionLabel(__('admin.page.actions.add_block')),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 4]),

                        Forms\Components\Section::make(__('admin.page.sections.publishing'))
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label(__('admin.labels.status'))
                                    ->options([
                                        Page::STATUS_DRAFT => __('admin.statuses.draft'),
                                        Page::STATUS_PUBLISHED => __('admin.statuses.published'),
                                        Page::STATUS_ARCHIVED => __('admin.statuses.archived'),
                                    ])
                                    ->default(Page::STATUS_DRAFT)
                                    ->required(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label(__('admin.labels.published_at')),
                            ])
                            ->columns(2)
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),

                Forms\Components\Section::make(__('admin.page.sections.seo'))
                    ->schema([

                        Forms\Components\TextInput::make('seo_meta.title')
                            ->label(__('admin.page.seo.title'))
                            ->maxLength(60)
                            ->helperText(__('admin.page.seo.title_helper')),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('admin.labels.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('seo_meta.description')
                            ->label(__('admin.page.seo.description'))
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText(__('admin.page.seo.description_helper')),
                        Forms\Components\Fieldset::make('Open Graph')
                            ->schema([
                                Forms\Components\TextInput::make('seo_meta.open_graph.title')
                                    ->label(__('admin.page.seo.og_title')),
                                Forms\Components\Textarea::make('seo_meta.open_graph.description')
                                    ->label(__('admin.page.seo.og_description'))
                                    ->rows(2),
                                Forms\Components\FileUpload::make('seo_meta.open_graph.image')
                                    ->label(__('admin.page.seo.og_image'))
                                    ->image()
                                    ->directory('pages/og'),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('admin.labels.title'))
                    ->formatStateUsing(fn (Page $record): string => $record->getLocalizedTitle())
                    ->searchable(query: function (EloquentBuilder $query, string $search): EloquentBuilder {
                        return $query->where('title', 'like', "%{$search}%");
                    }),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('admin.labels.slug'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('admin.labels.published_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin.labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('admin.labels.status'))
                    ->options([
                        'draft' => __('admin.statuses.draft'),
                        'published' => __('admin.statuses.published'),
                        'archived' => __('admin.statuses.archived'),
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

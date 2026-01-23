<?php

namespace App\Filament\Resources;

use App\Domain\Commerce\Product;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                // Title section at top
                Forms\Components\Section::make(__('admin.product.sections.info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('admin.labels.name'))
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

                // Main grid: Content (4 cols) + Sidebar (1 col)
                Forms\Components\Grid::make(['default' => 1, 'lg' => 5])
                    ->schema([
                        // Content area (left side)
                        Forms\Components\Section::make(__('admin.product.sections.product_data'))
                            ->schema([
                                Forms\Components\Tabs::make('ProductTabs')
                                    ->tabs([
                                        // Description tab
                                        Forms\Components\Tabs\Tab::make(__('admin.product.tabs.description'))
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                Forms\Components\Textarea::make('data.short_description')
                                                    ->label(__('admin.product.fields.short_description'))
                                                    ->rows(3)
                                                    ->helperText(__('admin.product.short_description_helper')),
                                                Forms\Components\RichEditor::make('data.description')
                                                    ->label(__('admin.product.fields.description'))
                                                    ->toolbarButtons([
                                                        'bold',
                                                        'italic',
                                                        'underline',
                                                        'strike',
                                                        'link',
                                                        'orderedList',
                                                        'bulletList',
                                                        'h2',
                                                        'h3',
                                                        'blockquote',
                                                        'redo',
                                                        'undo',
                                                    ])
                                                    ->helperText(__('admin.product.description_helper')),
                                            ]),

                                        // Pricing & Stock tab
                                        Forms\Components\Tabs\Tab::make(__('admin.product.tabs.pricing'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->label(__('admin.labels.type'))
                                                    ->options(Product::TYPES)
                                                    ->default(Product::TYPE_SIMPLE)
                                                    ->required()
                                                    ->live()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('price')
                                                    ->label(__('admin.labels.price'))
                                                    ->numeric()
                                                    ->default(0)
                                                    ->step(1 / (10 ** config('commerce.currency.decimals')))
                                                    ->prefix(config('commerce.currency.code'))
                                                    ->helperText(fn (Forms\Get $get) => $get('type') === Product::TYPE_VARIABLE
                                                        ? __('admin.product.price_variable_helper')
                                                        : __('admin.product.price_helper', ['currency' => config('commerce.currency.code')]))
                                                    ->disabled(fn (Forms\Get $get) => $get('type') === Product::TYPE_VARIABLE)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),

                                        // Attributes tab
                                        Forms\Components\Tabs\Tab::make(__('admin.product.tabs.attributes'))
                                            ->icon('heroicon-o-adjustments-horizontal')
                                            ->schema([
                                                Forms\Components\Select::make('attributeValues')
                                                    ->relationship('attributeValues', 'value')
                                                    ->multiple()
                                                    ->preload()
                                                    ->searchable()
                                                    ->label(__('admin.labels.attributes'))
                                                    ->helperText(__('admin.product.attributes_helper')),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 4]),

                        // Sidebar (right side)
                        Forms\Components\Grid::make(1)
                            ->schema([
                                // Status section
                                Forms\Components\Section::make(__('admin.product.sections.status'))
                                    ->schema([
                                        Forms\Components\Toggle::make('active')
                                            ->label(__('admin.labels.active'))
                                            ->default(true)
                                            ->helperText(__('admin.product.active_helper')),
                                    ]),

                                // Taxonomies section
                                Forms\Components\Section::make(__('admin.product.sections.taxonomies'))
                                    ->schema([
                                        Forms\Components\Select::make('categories')
                                            ->label(__('admin.labels.categories'))
                                            ->relationship('categories', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')->required(),
                                                Forms\Components\Hidden::make('type')->default('category'),
                                            ]),
                                        Forms\Components\Select::make('tags')
                                            ->label(__('admin.labels.tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')->required(),
                                                Forms\Components\Hidden::make('type')->default('tag'),
                                            ]),
                                        Forms\Components\Select::make('brands')
                                            ->label(__('admin.labels.brands'))
                                            ->relationship('brands', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')->required(),
                                                Forms\Components\Hidden::make('type')->default('brand'),
                                            ]),
                                    ]),

                                // Media section
                                Forms\Components\Section::make(__('admin.product.sections.media'))
                                    ->schema([
                                        Forms\Components\FileUpload::make('attachment')
                                            ->disk('public')
                                            ->directory('products/thumbnails')
                                            ->image()
                                            ->imageEditor()
                                            ->label(__('admin.labels.main_image')),
                                        Forms\Components\FileUpload::make('gallery')
                                            ->disk('public')
                                            ->directory('products/gallery')
                                            ->multiple()
                                            ->reorderable()
                                            ->image()
                                            ->maxParallelUploads(2)
                                            ->label(__('admin.labels.gallery')),
                                    ]),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),

                // SEO section (collapsed at bottom)
                Forms\Components\Section::make(__('admin.product.sections.seo'))
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label(__('admin.labels.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText(__('admin.product.slug_helper')),
                        Forms\Components\TextInput::make('data.seo.title')
                            ->label(__('admin.page.seo.title'))
                            ->maxLength(60)
                            ->helperText(__('admin.page.seo.title_helper')),
                        Forms\Components\Textarea::make('data.seo.description')
                            ->label(__('admin.page.seo.description'))
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText(__('admin.page.seo.description_helper')),
                        Forms\Components\Fieldset::make('Open Graph')
                            ->schema([
                                Forms\Components\TextInput::make('data.seo.og_title')
                                    ->label(__('admin.page.seo.og_title')),
                                Forms\Components\Textarea::make('data.seo.og_description')
                                    ->label(__('admin.page.seo.og_description'))
                                    ->rows(2),
                                Forms\Components\FileUpload::make('data.seo.og_image')
                                    ->label(__('admin.page.seo.og_image'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('products/og'),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('attachment')
                    ->height(50)
                    ->circular()
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Product::TYPE_SIMPLE => 'gray',
                        Product::TYPE_VIRTUAL => 'info',
                        Product::TYPE_VARIABLE => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->money(config('commerce.currency.code'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants')
                    ->visible(fn () => true),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->counts('reviews')
                    ->label('Reviews'),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active'),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Product::TYPES),
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
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

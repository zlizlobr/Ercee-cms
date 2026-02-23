<?php

namespace App\Filament\Resources;

use App\Domain\Content\Navigation;
use App\Filament\Components\LinkPicker;
use App\Filament\Resources\NavigationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Filament resource for managing navigation items.
 *
 * @extends resource<Navigation>
 */
class NavigationResource extends Resource
{
    /**
     * @var ?string Eloquent model class managed by this Filament resource.
     */
    protected static ?string $model = Navigation::class;

    /**
     * @var ?string Heroicon name shown for this resource in admin navigation.
     */
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    /**
     * @var ?string Navigation section label used for grouping this resource.
     */
    protected static ?string $navigationGroup = 'Content';

    /**
     * @var ?int Numeric sort order for this resource inside navigation groups.
     */
    protected static ?int $navigationSort = 2;

    /**
     * @var bool Flag that controls automatic sidebar registration for the resource.
     */
    protected static bool $shouldRegisterNavigation = false;

    /**
     * Build the navigation form schema.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
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
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent')
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (Root Item)'),
                        ...LinkPicker::make()
                            ->withoutAnchor()
                            ->withTarget()
                            ->fields(),
                        Forms\Components\TextInput::make('position')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Build the navigation table.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent')
                    ->placeholder('Root'),
                Tables\Columns\TextColumn::make('page.slug')
                    ->label('Linked Page')
                    ->formatStateUsing(fn ($record) => $record->page?->getLocalizedTitle())
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('position')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent')
                    ->relationship('parent', 'title')
                    ->placeholder('Root Items Only')
                    ->query(fn ($query) => $query->whereNull('parent_id')),
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

    /**
     * Register navigation relation managers.
     *
     * @return array<int, class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Register navigation resource pages.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigations::route('/'),
            'create' => Pages\CreateNavigation::route('/create'),
            'edit' => Pages\EditNavigation::route('/{record}/edit'),
        ];
    }
}



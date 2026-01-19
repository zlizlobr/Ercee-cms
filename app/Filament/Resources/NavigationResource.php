<?php

namespace App\Filament\Resources;

use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Filament\Resources\NavigationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NavigationResource extends Resource
{
    protected static ?string $model = Navigation::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

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
                        Forms\Components\Select::make('page_id')
                            ->label('Link to Page')
                            ->options(Page::pluck('title', 'id'))
                            ->searchable()
                            ->placeholder('Select a page...')
                            ->helperText('Or use custom URL below'),
                        Forms\Components\TextInput::make('url')
                            ->label('Custom URL')
                            ->url()
                            ->placeholder('https://...')
                            ->helperText('Overrides linked page if set'),
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
                Tables\Columns\TextColumn::make('page.title')
                    ->label('Linked Page')
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigations::route('/'),
            'create' => Pages\CreateNavigation::route('/create'),
            'edit' => Pages\EditNavigation::route('/{record}/edit'),
        ];
    }
}

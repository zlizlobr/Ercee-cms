<?php

namespace App\Filament\Resources;

use App\Domain\Media\MediaLibrary;
use App\Filament\Resources\MediaResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

/**
 * Defines the Filament resource configuration for admin record management.
 */
class MediaResource extends Resource
{
    /**
     * @var ?string Eloquent model class managed by this Filament resource.
     */
    protected static ?string $model = MediaLibrary::class;

    /**
     * @var ?string Heroicon name shown for this resource in admin navigation.
     */
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    /**
     * @var ?string Navigation section label used for grouping this resource.
     */
    protected static ?string $navigationGroup = 'Obsah';

    /**
     * @var ?int Numeric sort order for this resource inside navigation groups.
     */
    protected static ?int $navigationSort = 2;

    /**
     * @var ?string Singular resource label shown across Filament screens.
     */
    protected static ?string $modelLabel = 'Media';

    /**
     * @var ?string Plural resource label shown across Filament screens.
     */
    protected static ?string $pluralModelLabel = 'Media';

    /**
     * Build the form schema for this resource page.
     * @param Form $form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('default')
                            ->disk('media')
                            ->image()
                            ->imageEditor()
                            ->responsiveImages()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/webp',
                                'image/svg+xml',
                            ])
                            ->maxSize(10240)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255)
                            ->helperText('Describe the image for accessibility'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('focal_point.x')
                                    ->label('Focal Point X')
                                    ->numeric()
                                    ->default(50)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('focal_point.y')
                                    ->label('Focal Point Y')
                                    ->numeric()
                                    ->default(50)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%'),
                            ]),

                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->separator(','),
                    ]),
            ]);
    }

    /**
     * Build the table definition for this resource page.
     * @param Table $table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('media')
                    ->collection('default')
                    ->conversion('thumb')
                    ->circular()
                    ->height(60),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('alt_text')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('media.file_name')
                    ->label('File')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('media.size')
                    ->label('Size')
                    ->formatStateUsing(fn($state) => $state ? number_format($state / 1024, 1) . ' KB' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('media.mime_type')
                    ->label('Type')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Define relation managers for this Filament resource.
     * @return array<int, string>
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Define page routes for this Filament resource.
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}



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

class MediaResource extends Resource
{
    protected static ?string $model = MediaLibrary::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Obsah';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Media';

    protected static ?string $pluralModelLabel = 'Media';

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}

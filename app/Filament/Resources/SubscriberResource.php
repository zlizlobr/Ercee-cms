<?php

namespace App\Filament\Resources;

use App\Domain\Subscriber\Subscriber;
use App\Filament\Resources\SubscriberResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Defines the Filament resource configuration for admin record management.
 */
class SubscriberResource extends Resource
{
    /**
     * @var ?string Eloquent model class managed by this Filament resource.
     */
    protected static ?string $model = Subscriber::class;

    /**
     * @var ?string Heroicon name shown for this resource in admin navigation.
     */
    protected static ?string $navigationIcon = 'heroicon-o-users';

    /**
     * @var ?string Navigation section label used for grouping this resource.
     */
    protected static ?string $navigationGroup = 'Marketing';

    /**
     * @var ?int Numeric sort order for this resource inside navigation groups.
     */
    protected static ?int $navigationSort = 10;

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    /**
     * Get the navigation badge value.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Build the form schema for this resource page.
     * @param Form $form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'unsubscribed' => 'Unsubscribed',
                        'bounced' => 'Bounced',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\TextInput::make('source')
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'unsubscribed' => 'warning',
                        'bounced' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'unsubscribed' => 'Unsubscribed',
                        'bounced' => 'Bounced',
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
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}



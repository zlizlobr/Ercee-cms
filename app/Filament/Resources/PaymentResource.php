<?php

namespace App\Filament\Resources;

use App\Domain\Commerce\Payment;
use App\Filament\Resources\PaymentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Order #{$record->id} - {$record->email}")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('gateway')
                            ->options([
                                Payment::GATEWAY_STRIPE => 'Stripe',
                                Payment::GATEWAY_GOPAY => 'GoPay',
                                Payment::GATEWAY_COMGATE => 'Comgate',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                Payment::STATUS_PENDING => 'Pending',
                                Payment::STATUS_PAID => 'Paid',
                                Payment::STATUS_FAILED => 'Failed',
                            ])
                            ->required()
                            ->default(Payment::STATUS_PENDING),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Payment #')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order #')
                    ->sortable()
                    ->url(fn (Payment $record): string => OrderResource::getUrl('view', ['record' => $record->order_id])),
                Tables\Columns\TextColumn::make('order.email')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gateway')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Payment::GATEWAY_STRIPE => 'indigo',
                        Payment::GATEWAY_GOPAY => 'success',
                        Payment::GATEWAY_COMGATE => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->copyable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Payment::STATUS_PENDING => 'warning',
                        Payment::STATUS_PAID => 'success',
                        Payment::STATUS_FAILED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Payment::STATUS_PENDING => 'Pending',
                        Payment::STATUS_PAID => 'Paid',
                        Payment::STATUS_FAILED => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('gateway')
                    ->options([
                        Payment::GATEWAY_STRIPE => 'Stripe',
                        Payment::GATEWAY_GOPAY => 'GoPay',
                        Payment::GATEWAY_COMGATE => 'Comgate',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Payment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('Payment #'),
                        Infolists\Components\TextEntry::make('order.id')
                            ->label('Order #'),
                        Infolists\Components\TextEntry::make('order.email')
                            ->label('Customer Email'),
                        Infolists\Components\TextEntry::make('gateway')
                            ->badge(),
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                Payment::STATUS_PENDING => 'warning',
                                Payment::STATUS_PAID => 'success',
                                Payment::STATUS_FAILED => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Gateway Payload')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('payload')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
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
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}

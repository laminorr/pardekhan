<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTransactionResource\Pages;
use App\Models\WalletTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'مدیریت تراکنش‌ها';
    protected static string|\UnitEnum|null $navigationGroup = 'مالی';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'تراکنش';
    protected static ?string $pluralModelLabel = 'تراکنش‌ها';

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d H:i'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('عضو')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'recharge' => 'شارژ', 'payment' => 'پرداخت',
                        'refund' => 'بازگشت', 'adjustment' => 'اصلاح', default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'recharge', 'refund' => 'success',
                        'payment' => 'gray', 'adjustment' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ت')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('موجودی پس از')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state) . ' ت' : '-'),
                Tables\Columns\TextColumn::make('description')
                    ->label('توضیح')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('کد پیگیری')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع تراکنش')
                    ->options([
                        'recharge' => 'شارژ', 'payment' => 'پرداخت',
                        'refund' => 'بازگشت', 'adjustment' => 'اصلاح',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalletTransactions::route('/'),
        ];
    }
}

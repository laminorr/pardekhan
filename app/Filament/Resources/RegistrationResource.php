<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'ثبت‌نام‌ها';
    protected static ?string $modelLabel = 'ثبت‌نام';
    protected static ?string $pluralModelLabel = 'ثبت‌نام‌ها';
    protected static string|\UnitEnum|null $navigationGroup = 'دورهمی‌ها';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.first_name')
                    ->label('عضو')
                    ->formatStateUsing(fn ($record) => $record->member->first_name . ' ' . $record->member->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('member.phone')
                    ->label('موبایل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('دورهمی')
                    ->limit(25),
                Tables\Columns\TextColumn::make('final_price')
                    ->label('مبلغ')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ت'),
                Tables\Columns\TextColumn::make('payment.method')
                    ->label('روش')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'wallet'       => 'کیف پول',
                        'card_to_card' => 'کارت به کارت',
                        'gateway'      => 'درگاه',
                        default        => $state,
                    }),
                Tables\Columns\TextColumn::make('payment.tracking_number')
                    ->label('شماره پیگیری')
                    ->placeholder('—')
                    ->copyable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'  => 'در انتظار بررسی',
                        'verified' => 'تایید شده',
                        'rejected' => 'رد شده',
                        default    => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    }),
                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('حضور')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'registered'         => 'ثبت‌نام',
                        'attended'           => 'حاضر',
                        'cancelled_by_user'  => 'انصراف',
                        'absent'             => 'غایب',
                        'cancelled_by_admin' => 'لغو مدیریتی',
                        default              => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'attended'   => 'success',
                        'absent', 'cancelled_by_admin' => 'danger',
                        'cancelled_by_user' => 'warning',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('registered_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('registered_at', 'desc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('دورهمی')
                    ->relationship('event', 'title'),
                SelectFilter::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->options([
                        'pending'  => 'در انتظار بررسی',
                        'verified' => 'تایید شده',
                        'rejected' => 'رد شده',
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('verify_payment')
                    ->label('تایید پرداخت')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Registration $r) => $r->payment_status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Registration $r) {
                        $r->update(['payment_status' => 'verified']);
                        if ($r->payment) {
                            $r->payment->update([
                                'status' => 'verified',
                                'verified_at' => now(),
                                'verified_by' => auth()->id(),
                            ]);
                        }
                        \Filament\Notifications\Notification::make()->success()->title('پرداخت تایید شد')->send();
                    }),
                \Filament\Actions\Action::make('reject_payment')
                    ->label('رد پرداخت')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Registration $r) => $r->payment_status === 'pending')
                    ->requiresConfirmation()
                    ->modalDescription('ثبت‌نام لغو و امتیاز منفی ثبت می‌شود.')
                    ->action(function (Registration $r) {
                        $r->update([
                            'payment_status' => 'rejected',
                            'attendance_status' => 'cancelled_by_admin',
                        ]);
                        if ($r->payment) {
                            $r->payment->update(['status' => 'rejected']);
                        }
                        // امتیاز منفی
                        app(\App\Services\ScoreService::class)->addByKey($r->member, 'invalid_payment');
                        \Filament\Notifications\Notification::make()->danger()->title('پرداخت رد شد')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
        ];
    }
}

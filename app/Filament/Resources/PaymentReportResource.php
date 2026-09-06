<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReportResource\Pages;
use App\Models\PaymentReport;
use App\Services\WalletService;
use BackedEnum;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PaymentReportResource extends Resource
{
    protected static ?string $model = PaymentReport::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'گزارش‌های پرداخت';
    protected static string|\UnitEnum|null $navigationGroup = 'مالی';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'گزارش پرداخت';
    protected static ?string $pluralModelLabel = 'گزارش‌های پرداخت';

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PaymentReport::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return PaymentReport::pending()->count() > 0 ? 'warning' : null;
    }

    protected static function statusColor(string $state): string
    {
        return match ($state) {
            PaymentReport::STATUS_PENDING  => 'warning',
            PaymentReport::STATUS_APPROVED => 'success',
            PaymentReport::STATUS_REJECTED => 'danger',
            default                        => 'gray',
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('جزئیات گزارش پرداخت')->schema([
                Forms\Components\Placeholder::make('member_name')
                    ->label('عضو')
                    ->content(fn ($record) => $record?->member?->full_name ?? '—'),
                Forms\Components\Placeholder::make('amount_display')
                    ->label('مبلغ واریزی')
                    ->content(fn ($record) => $record ? fa(number_format($record->amount)) . ' تومان' : '—'),
                Forms\Components\Placeholder::make('tracking_number')
                    ->label('شماره پیگیری')
                    ->content(fn ($record) => $record?->tracking_number ?? '—'),
                Forms\Components\Placeholder::make('status_display')
                    ->label('وضعیت')
                    ->content(fn ($record) => $record?->statusLabel() ?? '—'),
                Forms\Components\Placeholder::make('created_display')
                    ->label('تاریخ ثبت')
                    ->content(fn ($record) => $record ? pdate($record->created_at, 'Y/m/d H:i') : '—'),
                Forms\Components\Placeholder::make('reviewer_display')
                    ->label('بررسی‌کننده')
                    ->content(fn ($record) => $record?->reviewer?->name ?? '—')
                    ->visible(fn ($record) => (bool) $record?->reviewed_at),
                Forms\Components\Placeholder::make('reviewed_display')
                    ->label('تاریخ بررسی')
                    ->content(fn ($record) => $record?->reviewed_at ? pdate($record->reviewed_at, 'Y/m/d H:i') : '—')
                    ->visible(fn ($record) => (bool) $record?->reviewed_at),
            ]),
        ]);
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ')
                    ->formatStateUsing(fn ($state) => fa(number_format($state)) . ' ت')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('شماره پیگیری')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PaymentReport::STATUS_LABELS[$state] ?? $state)
                    ->color(fn ($state) => static::statusColor($state)),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('بررسی‌کننده')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('تاریخ بررسی')
                    ->formatStateUsing(fn ($state) => $state ? pdate($state, 'Y/m/d H:i') : '—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(PaymentReport::STATUS_LABELS)
                    ->default(PaymentReport::STATUS_PENDING),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('approve')
                    ->label('تأیید و شارژ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأیید و شارژ کیف پول')
                    ->modalDescription('کیف پولِ عضو به مبلغ این گزارش شارژ می‌شود. این عمل قابل بازگشت نیست.')
                    ->visible(fn (PaymentReport $record) => $record->isPending())
                    ->action(function (PaymentReport $record) {
                        DB::transaction(function () use ($record) {
                            // قفل ردیف + بازبینی وضعیت درون تراکنش → جلوگیری از تأییدِ دوباره / شارژِ مضاعف
                            $fresh = PaymentReport::lockForUpdate()->find($record->id);

                            if (! $fresh || $fresh->status !== PaymentReport::STATUS_PENDING) {
                                Notification::make()
                                    ->warning()
                                    ->title('این گزارش قبلاً بررسی شده است')
                                    ->send();
                                return;
                            }

                            $txn = app(WalletService::class)->recharge(
                                $fresh->member,
                                (int) $fresh->amount,
                                'شارژ کارت‌به‌کارت — پیگیری: ' . $fresh->tracking_number,
                            );

                            $fresh->update([
                                'status'                => PaymentReport::STATUS_APPROVED,
                                'reviewed_by'           => auth()->id(),
                                'reviewed_at'           => now(),
                                'wallet_transaction_id' => $txn->id,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('کیف پول شارژ شد')
                                ->body($fresh->member->full_name . ' — ' . fa(number_format($fresh->amount)) . ' تومان')
                                ->send();
                        });
                    }),

                \Filament\Actions\Action::make('reject')
                    ->label('رد')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('رد گزارش پرداخت')
                    ->modalDescription('این گزارش رد می‌شود و هیچ شارژی انجام نخواهد شد.')
                    ->visible(fn (PaymentReport $record) => $record->isPending())
                    ->action(function (PaymentReport $record) {
                        DB::transaction(function () use ($record) {
                            $fresh = PaymentReport::lockForUpdate()->find($record->id);

                            if (! $fresh || $fresh->status !== PaymentReport::STATUS_PENDING) {
                                Notification::make()
                                    ->warning()
                                    ->title('این گزارش قبلاً بررسی شده است')
                                    ->send();
                                return;
                            }

                            $fresh->update([
                                'status'      => PaymentReport::STATUS_REJECTED,
                                'reviewed_by' => auth()->id(),
                                'reviewed_at' => now(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('گزارش رد شد')
                                ->send();
                        });
                    }),

                \Filament\Actions\ViewAction::make()
                    ->label('مشاهده')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('بستن'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentReports::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\WalletTransactionResource\Pages;

use App\Filament\Resources\WalletTransactionResource;
use App\Models\Member;
use App\Services\WalletService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListWalletTransactions extends ListRecords
{
    protected static string $resource = WalletTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manual_charge')
                ->label('شارژ دستی کیف پول')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('member_id')
                        ->label('عضو')
                        ->options(fn () => Member::where('status', 'approved')
                            ->get()
                            ->mapWithKeys(fn ($m) => [$m->id => $m->full_name . ' — ' . $m->phone]))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('مبلغ (تومان)')
                        ->numeric()
                        ->required()
                        ->minValue(1000),
                    Forms\Components\TextInput::make('description')
                        ->label('توضیح')
                        ->default('شارژ دستی توسط مدیریت')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $member = Member::find($data['member_id']);
                    app(WalletService::class)->recharge(
                        $member,
                        (int) $data['amount'],
                        $data['description']
                    );
                    Notification::make()
                        ->success()
                        ->title('کیف پول شارژ شد')
                        ->body("{$member->full_name} — " . number_format($data['amount']) . ' تومان')
                        ->send();
                }),
        ];
    }
}

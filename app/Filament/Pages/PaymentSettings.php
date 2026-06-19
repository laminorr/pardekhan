<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'تنظیمات پرداخت';
    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'تنظیمات پرداخت';

    protected string $view = 'filament.pages.payment-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'card_number'          => Setting::get('card_number'),
            'card_holder'          => Setting::get('card_holder'),
            'card_to_card_enabled' => Setting::get('card_to_card_enabled') === '1',
            'gateway_enabled'      => Setting::get('gateway_enabled') === '1',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('کارت به کارت')->schema([
                Forms\Components\TextInput::make('card_number')
                    ->label('شماره کارت')
                    ->placeholder('6037xxxxxxxxxxxx'),
                Forms\Components\TextInput::make('card_holder')
                    ->label('نام صاحب کارت'),
                Forms\Components\Toggle::make('card_to_card_enabled')
                    ->label('کارت به کارت فعال باشد'),
            ]),
            \Filament\Schemas\Components\Section::make('درگاه بانکی')->schema([
                Forms\Components\Toggle::make('gateway_enabled')
                    ->label('درگاه بانکی فعال باشد')
                    ->helperText('فعلاً درگاه متصل نشده است'),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('card_number', $data['card_number'] ?? '');
        Setting::set('card_holder', $data['card_holder'] ?? '');
        Setting::set('card_to_card_enabled', $data['card_to_card_enabled'] ? '1' : '0');
        Setting::set('gateway_enabled', $data['gateway_enabled'] ? '1' : '0');

        Notification::make()->success()->title('تنظیمات ذخیره شد')->send();
    }
}

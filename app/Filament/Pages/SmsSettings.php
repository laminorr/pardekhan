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

class SmsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationLabel = 'تنظیمات پیامک';
    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'تنظیمات پیامک';

    protected string $view = 'filament.pages.sms-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'sms_enabled'         => Setting::get('sms_enabled') === '1',
            'sms_api_key'         => Setting::get('sms_api_key'),
            'sms_sender'          => Setting::get('sms_sender'),
            'sms_pattern_otp'     => Setting::get('sms_pattern_otp'),
            'sms_pattern_event'   => Setting::get('sms_pattern_event'),
            'sms_pattern_general' => Setting::get('sms_pattern_general'),
            'sms_pattern_waitlist' => Setting::get('sms_pattern_waitlist'),
            'sms_pattern_reminder' => Setting::get('sms_pattern_reminder'),
            'sms_pattern_feedback' => Setting::get('sms_pattern_feedback'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('اتصال به پنل IPPanel (مدیرپیامک)')
                ->description('برای دریافت API key به پنل مدیرپیامک، بخش وب‌سرویس مراجعه کنید')
                ->schema([
                    Forms\Components\Toggle::make('sms_enabled')
                        ->label('ارسال پیامک فعال باشد')
                        ->helperText('اگر خاموش باشد، پیامک‌ها فقط در لاگ ثبت می‌شوند'),
                    Forms\Components\TextInput::make('sms_api_key')
                        ->label('API Key')
                        ->password()
                        ->revealable()
                        ->placeholder('کلید API از پنل مدیرپیامک'),
                    Forms\Components\TextInput::make('sms_sender')
                        ->label('شماره خط فرستنده')
                        ->placeholder('مثلاً: +983000505'),
                ]),

            \Filament\Schemas\Components\Section::make('کد پترن‌ها')
                ->description('کد پترن‌هایی که در پنل مدیرپیامک ساخته‌اید را اینجا وارد کنید')
                ->schema([
                    Forms\Components\TextInput::make('sms_pattern_otp')
                        ->label('پترن کد تایید (OTP)')
                        ->placeholder('کد پترن')
                        ->helperText('متغیر: code'),
                    Forms\Components\TextInput::make('sms_pattern_event')
                        ->label('پترن دورهمی جدید')
                        ->placeholder('کد پترن')
                        ->helperText('بدون متغیر یا با متغیر name'),
                    Forms\Components\TextInput::make('sms_pattern_general')
                        ->label('پترن پیام عمومی')
                        ->placeholder('کد پترن')
                        ->helperText('وقتی ادمین به اعضا پیام می‌دهد · متغیر: name'),
                    Forms\Components\TextInput::make('sms_pattern_waitlist')
                        ->label('پترن اطلاع لیست انتظار')
                        ->placeholder('کد پترن')
                        ->helperText('وقتی ظرفیت دورهمی باز می‌شود · متغیر: name, event'),
                    Forms\Components\TextInput::make('sms_pattern_reminder')
                        ->label('پترن یادآوری دورهمی')
                        ->placeholder('کد پترن')
                        ->helperText('۲۴ ساعت قبل از دورهمی · متغیر: name, event'),
                    Forms\Components\TextInput::make('sms_pattern_feedback')
                        ->label('پترن درخواست بازخورد')
                        ->placeholder('کد پترن')
                        ->helperText('بعد از پایان دورهمی · متغیر: name, event'),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('sms_enabled', $data['sms_enabled'] ? '1' : '0');
        Setting::set('sms_api_key', $data['sms_api_key'] ?? '');
        Setting::set('sms_sender', $data['sms_sender'] ?? '');
        Setting::set('sms_pattern_otp', $data['sms_pattern_otp'] ?? '');
        Setting::set('sms_pattern_event', $data['sms_pattern_event'] ?? '');
        Setting::set('sms_pattern_general', $data['sms_pattern_general'] ?? '');
        Setting::set('sms_pattern_waitlist', $data['sms_pattern_waitlist'] ?? '');
        Setting::set('sms_pattern_reminder', $data['sms_pattern_reminder'] ?? '');
        Setting::set('sms_pattern_feedback', $data['sms_pattern_feedback'] ?? '');

        Notification::make()->success()->title('تنظیمات پیامک ذخیره شد')->send();
    }

    public function sendTest(): void
    {
        $admin = auth()->user();
        // برای تست، به یه شماره دلخواه ارسال نمی‌کنیم چون شماره ادمین نداریم
        Notification::make()
            ->info()
            ->title('برای تست، یک ثبت‌نام جدید انجام دهید و لاگ را بررسی کنید')
            ->send();
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\ActivitySimulation\ActivitySimulationManager;
use App\Services\ActivitySimulation\OnlineActivityEngine;
use App\Services\ActivitySimulation\WeeklyMovieWatchingEngine;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ActivitySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'آمار';
    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 99;
    protected static ?string $title = 'آمار';

    protected string $view = 'filament.pages.activity-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $keys = config('activity_simulation.setting_keys');
        $defaults = config('activity_simulation.defaults');

        $this->form->fill([
            'online_midnight' => (int) Setting::get($keys['online']['midnight'], $defaults['online']['midnight']),
            'online_morning'  => (int) Setting::get($keys['online']['morning'], $defaults['online']['morning']),
            'online_noon'     => (int) Setting::get($keys['online']['noon'], $defaults['online']['noon']),
            'online_evening'  => (int) Setting::get($keys['online']['evening'], $defaults['online']['evening']),
            'online_night'    => (int) Setting::get($keys['online']['night'], $defaults['online']['night']),

            'watching_midnight' => (int) Setting::get($keys['watching']['midnight'], $defaults['watching']['midnight']),
            'watching_morning'  => (int) Setting::get($keys['watching']['morning'], $defaults['watching']['morning']),
            'watching_noon'     => (int) Setting::get($keys['watching']['noon'], $defaults['watching']['noon']),
            'watching_evening'  => (int) Setting::get($keys['watching']['evening'], $defaults['watching']['evening']),
            'watching_night'    => (int) Setting::get($keys['watching']['night'], $defaults['watching']['night']),

            'tolerance' => (int) Setting::get($keys['tolerance'], $defaults['tolerance']),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('کاربران آنلاین')
                ->schema([
                    Forms\Components\TextInput::make('online_midnight')
                        ->label('نصف‌شب')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('online_morning')
                        ->label('صبح')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('online_noon')
                        ->label('ظهر')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('online_evening')
                        ->label('عصر')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('online_night')
                        ->label('شب')->numeric()->minValue(0)->required(),
                ]),

            \Filament\Schemas\Components\Section::make('در حال دیدن فیلم هفته')
                ->schema([
                    Forms\Components\TextInput::make('watching_midnight')
                        ->label('نصف‌شب')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('watching_morning')
                        ->label('صبح')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('watching_noon')
                        ->label('ظهر')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('watching_evening')
                        ->label('عصر')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('watching_night')
                        ->label('شب')->numeric()->minValue(0)->required(),
                ]),

            \Filament\Schemas\Components\Section::make('دامنه تغییرات')
                ->schema([
                    Forms\Components\TextInput::make('tolerance')
                        ->label('دامنه تغییر طبیعی')
                        ->numeric()->minValue(0)->required()
                        ->helperText('حداکثر فاصله‌ای که آمار می‌تواند به‌صورت طبیعی از مقدار پایه فاصله بگیرد.'),
                ]),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previewToday')
                ->label('پیش‌نمایش امروز')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->modalHeading('پیش‌نمایشِ آمارِ امروز (فقط‌خواندنی)')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('بستن')
                ->modalContent(fn (): View => view('filament.pages.activity-preview', [
                    'rows' => $this->buildTodayPreview(),
                ])),
        ];
    }

    /**
     * مسیرِ امروزِ هر دو موتور (با لنگرهای ذخیره‌شده) را هر ۳۰ دقیقه نمونه می‌گیرد.
     * فقط‌خواندنی — چیزی ذخیره نمی‌شود و پنلِ اعضا را تحتِ‌تأثیر نمی‌گذارد.
     */
    protected function buildTodayPreview(): array
    {
        $tz      = config('activity_simulation.timezone');
        $dateStr = CarbonImmutable::now($tz)->format('Y-m-d');

        $online   = app(OnlineActivityEngine::class)->getTrajectory(CarbonImmutable::parse($dateStr, $tz));
        $watching = app(WeeklyMovieWatchingEngine::class)->getTrajectory(CarbonImmutable::parse($dateStr, $tz));
        $manager  = app(ActivitySimulationManager::class);

        $rows = [];
        for ($m = 0; $m < 1440; $m += 30) {
            $rows[] = [
                'time'     => sprintf('%02d:%02d', intdiv($m, 60), $m % 60),
                'online'   => $online[$m],
                'watching' => $manager->applyRatioGuard($online[$m], $watching[$m]),
            ];
        }

        return $rows;
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $keys = config('activity_simulation.setting_keys');

        Setting::set($keys['online']['midnight'], (int) $data['online_midnight']);
        Setting::set($keys['online']['morning'], (int) $data['online_morning']);
        Setting::set($keys['online']['noon'], (int) $data['online_noon']);
        Setting::set($keys['online']['evening'], (int) $data['online_evening']);
        Setting::set($keys['online']['night'], (int) $data['online_night']);

        Setting::set($keys['watching']['midnight'], (int) $data['watching_midnight']);
        Setting::set($keys['watching']['morning'], (int) $data['watching_morning']);
        Setting::set($keys['watching']['noon'], (int) $data['watching_noon']);
        Setting::set($keys['watching']['evening'], (int) $data['watching_evening']);
        Setting::set($keys['watching']['night'], (int) $data['watching_night']);

        Setting::set($keys['tolerance'], (int) $data['tolerance']);

        Notification::make()->success()->title('تنظیمات آمار ذخیره شد')->send();
    }
}

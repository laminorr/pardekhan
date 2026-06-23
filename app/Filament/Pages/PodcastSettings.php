<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\PodcastService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class PodcastSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationLabel = 'تنظیمات پادکست';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 6;
    protected static ?string $title = 'تنظیمات پادکست';

    protected string $view = 'filament.pages.podcast-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'podcast_rss_url' => Setting::get('podcast_rss_url'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('فید پادکست (RSS)')
                ->description('آدرس RSS پادکست را از Acast کپی کنید (دکمه Share Links → RSS Feed). قسمت‌ها به‌صورت خودکار خوانده می‌شوند.')
                ->schema([
                    Forms\Components\TextInput::make('podcast_rss_url')
                        ->label('آدرس RSS')
                        ->placeholder('https://feeds.acast.com/public/shows/uncertainty')
                        ->url(),
                ]),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('به‌روزرسانی قسمت‌ها')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    PodcastService::clearCache();
                    Notification::make()->success()->title('کش پاک شد؛ قسمت‌های جدید خوانده می‌شوند')->send();
                }),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::set('podcast_rss_url', $data['podcast_rss_url'] ?? '');
        PodcastService::clearCache();

        Notification::make()->success()->title('تنظیمات پادکست ذخیره شد')->send();
    }
}

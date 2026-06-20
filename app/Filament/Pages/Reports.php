<?php

namespace App\Filament\Pages;

use App\Models\Event;
use BackedEnum;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'گزارش‌ها';
    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'گزارش‌گیری';

    protected string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public function getEventsProperty()
    {
        return Event::orderBy('starts_at', 'desc')->get();
    }
}

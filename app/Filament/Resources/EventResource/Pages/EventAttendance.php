<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Registration;
use App\Services\ScoreService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class EventAttendance extends Page
{
    protected static string $resource = EventResource::class;

    protected string $view = 'filament.resources.event-resource.pages.event-attendance';

    public $record;

    public function mount($record): void
    {
        $this->record = \App\Models\Event::findOrFail($record);
    }

    public function getTitle(): string|Htmlable
    {
        return 'حضور و غیاب: ' . $this->record->title;
    }

    public function getRegistrationsProperty()
    {
        return Registration::where('event_id', $this->record->id)
            ->whereIn('attendance_status', ['registered', 'attended', 'absent'])
            ->with('member')
            ->get();
    }

    // ثبت دستی حضور
    public function markAttended($registrationId): void
    {
        $reg = Registration::find($registrationId);
        if (! $reg || $reg->attendance_status === 'attended') return;

        $reg->update(['attendance_status' => 'attended']);
        app(ScoreService::class)->addByKey($reg->member, 'attendance');

        // اگه بلیت داشت، استفاده‌شده کن
        if ($reg->ticket ?? false) {
            $reg->ticket->update(['status' => 'used', 'used_at' => now(), 'checked_by' => auth()->id()]);
        }

        Notification::make()->success()->title('حضور ثبت شد')->send();
    }

    // ثبت غیبت
    public function markAbsent($registrationId): void
    {
        $reg = Registration::find($registrationId);
        if (! $reg || $reg->attendance_status === 'absent') return;

        $reg->update(['attendance_status' => 'absent']);
        app(ScoreService::class)->addByKey($reg->member, 'absence');

        Notification::make()->warning()->title('غیبت ثبت شد')->send();
    }

    // بازگرداندن به حالت ثبت‌نام
    public function markRegistered($registrationId): void
    {
        $reg = Registration::find($registrationId);
        if (! $reg) return;

        $reg->update(['attendance_status' => 'registered']);
        Notification::make()->success()->title('به حالت ثبت‌نام بازگشت')->send();
    }
}

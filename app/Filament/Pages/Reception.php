<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use BackedEnum;
use Filament\Pages\Page;

class Reception extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'پذیرش';
    protected static string|\UnitEnum|null $navigationGroup = 'دورهمی‌ها';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'پذیرش و ثبت حضور';

    protected string $view = 'filament.pages.reception';

    // نتیجه اسکن
    public ?array $result = null;
    public ?string $manualCode = null;
    public ?int $selectedEvent = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isReception() || $user?->isEventManager();
    }

    public function getTodayEventsProperty()
    {
        return Event::whereDate('starts_at', '>=', now()->subDay())
            ->whereDate('starts_at', '<=', now()->addDay())
            ->whereIn('status', ['active', 'full', 'closed', 'completed'])
            ->orderBy('starts_at')
            ->get();
    }

    // ثبت حضور با کد
    public function checkIn(string $code): void
    {
        $code = trim($code);
        if (empty($code)) {
            $this->result = ['status' => 'error', 'message' => 'کد وارد نشده است'];
            return;
        }

        $service = app(TicketService::class);
        $res = $service->checkIn($code, auth()->id());

        if ($res['ok']) {
            $ticket = $res['ticket'];
            $this->result = [
                'status'  => 'success',
                'message' => $res['message'],
                'name'    => $ticket->member->full_name,
                'phone'   => substr($ticket->member->phone, -4),
                'event'   => $ticket->event->title,
                'avatar'  => $ticket->member->avatar && $ticket->member->avatar_approved
                    ? \Illuminate\Support\Facades\Storage::url($ticket->member->avatar) : null,
            ];
        } else {
            $ticket = $res['ticket'] ?? null;
            $this->result = [
                'status'  => $res['status'],
                'message' => $res['message'],
                'name'    => $ticket?->member?->full_name,
                'event'   => $ticket?->event?->title,
                'avatar'  => $ticket && $ticket->member->avatar && $ticket->member->avatar_approved
                    ? \Illuminate\Support\Facades\Storage::url($ticket->member->avatar) : null,
            ];
        }

        $this->manualCode = null;
    }

    public function submitManual(): void
    {
        if ($this->manualCode) {
            $this->checkIn($this->manualCode);
        }
    }

    public function clearResult(): void
    {
        $this->result = null;
    }
}

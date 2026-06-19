<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Services\TicketService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class Reception extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'پذیرش';
    protected static string|\UnitEnum|null $navigationGroup = 'دورهمی‌ها';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'پذیرش و ثبت حضور';

    protected string $view = 'filament.pages.reception';

    public ?array $preview = null;   // پیش‌نمایش قبل از تایید
    public ?array $result = null;    // نتیجه نهایی
    public ?string $manualCode = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isReception() || $user?->isEventManager();
    }

    // مرحله ۱: فقط نمایش اطلاعات بلیت (بدون ثبت)
    public function lookup(string $code): void
    {
        $code = trim($code);
        if (empty($code)) return;

        $ticket = Ticket::where('code', $code)->with(['member', 'event'])->first();

        if (! $ticket) {
            $this->result = ['status' => 'not_found', 'message' => 'بلیت یافت نشد'];
            $this->preview = null;
            return;
        }

        $avatar = $ticket->member->avatar && $ticket->member->avatar_approved
            ? Storage::url($ticket->member->avatar) : null;

        // اگه قبلاً استفاده شده، مستقیم نتیجه نشون بده
        if ($ticket->status === 'used') {
            $this->result = [
                'status'  => 'already_used',
                'message' => 'این بلیت قبلاً استفاده شده است',
                'name'    => $ticket->member->full_name,
                'event'   => $ticket->event->title,
                'avatar'  => $avatar,
                'used_at' => $ticket->used_at?->format('H:i'),
            ];
            $this->preview = null;
            return;
        }

        if ($ticket->status === 'cancelled') {
            $this->result = [
                'status'  => 'cancelled',
                'message' => 'این بلیت باطل شده است',
                'name'    => $ticket->member->full_name,
                'event'   => $ticket->event->title,
                'avatar'  => $avatar,
            ];
            $this->preview = null;
            return;
        }

        // بلیت معتبر — پیش‌نمایش برای تایید
        $this->preview = [
            'code'   => $ticket->code,
            'name'   => $ticket->member->full_name,
            'phone'  => substr($ticket->member->phone, -4),
            'event'  => $ticket->event->title,
            'avatar' => $avatar,
        ];
        $this->result = null;
    }

    // مرحله ۲: تایید نهایی و ثبت حضور
    public function confirm(): void
    {
        if (! $this->preview) return;

        $res = app(TicketService::class)->checkIn($this->preview['code'], auth()->id());

        if ($res['ok']) {
            $ticket = $res['ticket'];
            $this->result = [
                'status'  => 'success',
                'message' => 'حضور ثبت شد',
                'name'    => $ticket->member->full_name,
                'phone'   => substr($ticket->member->phone, -4),
                'event'   => $ticket->event->title,
                'avatar'  => $this->preview['avatar'],
            ];
        } else {
            $this->result = ['status' => $res['status'], 'message' => $res['message']];
        }

        $this->preview = null;
        $this->manualCode = null;
    }

    public function submitManual(): void
    {
        if ($this->manualCode) {
            $this->lookup($this->manualCode);
        }
    }

    public function reset_scan(): void
    {
        $this->preview = null;
        $this->result = null;
        $this->manualCode = null;
    }
}

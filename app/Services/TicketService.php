<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\Ticket;

class TicketService
{
    /**
     * صدور بلیت برای یک ثبت‌نام
     */
    public function issue(Registration $registration): Ticket
    {
        // اگه بلیت قبلاً صادر شده، همون رو برگردون
        $existing = Ticket::where('registration_id', $registration->id)->first();
        if ($existing) {
            return $existing;
        }

        return Ticket::create([
            'registration_id' => $registration->id,
            'member_id'       => $registration->member_id,
            'event_id'        => $registration->event_id,
            'code'            => Ticket::generateCode(),
            'status'          => 'active',
        ]);
    }

    /**
     * اسکن و ثبت حضور
     */
    public function checkIn(string $code, int $adminId): array
    {
        $ticket = Ticket::where('code', $code)->with(['member', 'event'])->first();

        if (! $ticket) {
            return ['ok' => false, 'status' => 'not_found', 'message' => 'بلیت یافت نشد'];
        }

        if ($ticket->status === 'cancelled') {
            return ['ok' => false, 'status' => 'cancelled', 'message' => 'این بلیت باطل شده است', 'ticket' => $ticket];
        }

        if ($ticket->status === 'used') {
            return [
                'ok' => false,
                'status' => 'already_used',
                'message' => 'این بلیت قبلاً در ' . $ticket->used_at->format('H:i') . ' استفاده شده است',
                'ticket' => $ticket,
            ];
        }

        // ثبت حضور
        $ticket->update([
            'status'     => 'used',
            'used_at'    => now(),
            'checked_by' => $adminId,
        ]);

        // وضعیت حضور در ثبت‌نام
        $ticket->registration->update(['attendance_status' => 'attended']);

        // امتیاز حضور موفق
        app(ScoreService::class)->addByKey($ticket->member, 'attendance');

        return ['ok' => true, 'status' => 'success', 'message' => 'حضور ثبت شد', 'ticket' => $ticket];
    }
}

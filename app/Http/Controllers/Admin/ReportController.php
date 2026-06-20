<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Member;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // محافظت: فقط ادمین لاگین‌شده
    public function __construct()
    {
        // دسترسی از طریق پنل Filament که خودش auth داره
    }

    private function csvResponse(string $filename, array $headers, \Closure $rows): StreamedResponse
    {
        return response()->stream(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            // BOM برای نمایش درست فارسی در Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, $headers);
            $rows($out);
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // گزارش اعضا
    public function members()
    {
        abort_unless(auth()->check() && auth()->user()->isSuperAdmin(), 403);

        return $this->csvResponse(
            'members-' . now()->format('Y-m-d') . '.csv',
            ['نام', 'نام خانوادگی', 'موبایل', 'وضعیت', 'لایه', 'امتیاز', 'کیف پول', 'شهر', 'تاریخ عضویت'],
            function ($out) {
                Member::with('layer')->chunk(100, function ($members) use ($out) {
                    foreach ($members as $m) {
                        fputcsv($out, [
                            $m->first_name,
                            $m->last_name,
                            $m->phone,
                            $this->statusLabel($m->status),
                            $m->layer?->name ?? '-',
                            $m->score,
                            $m->wallet_balance,
                            $m->city ?? '-',
                            $m->created_at->format('Y-m-d'),
                        ]);
                    }
                });
            }
        );
    }

    // گزارش ثبت‌نام‌های یک دورهمی
    public function eventRegistrations(Event $event)
    {
        abort_unless(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isEventManager()), 403);

        return $this->csvResponse(
            'registrations-' . $event->id . '-' . now()->format('Y-m-d') . '.csv',
            ['نام', 'موبایل', 'مبلغ', 'روش پرداخت', 'وضعیت پرداخت', 'وضعیت حضور', 'شماره پیگیری', 'تاریخ ثبت‌نام'],
            function ($out) use ($event) {
                $event->registrations()->with('member', 'payment')->chunk(100, function ($regs) use ($out) {
                    foreach ($regs as $r) {
                        fputcsv($out, [
                            $r->member->first_name . ' ' . $r->member->last_name,
                            $r->member->phone,
                            $r->final_price,
                            $r->payment?->method === 'wallet' ? 'کیف پول' : ($r->payment?->method === 'card_to_card' ? 'کارت به کارت' : '-'),
                            $this->paymentStatusLabel($r->payment_status),
                            $this->attendanceLabel($r->attendance_status),
                            $r->payment?->tracking_number ?? '-',
                            $r->registered_at?->format('Y-m-d H:i') ?? '-',
                        ]);
                    }
                });
            }
        );
    }

    // گزارش مالی (تراکنش‌های کیف پول)
    public function financial()
    {
        abort_unless(auth()->check() && auth()->user()->isSuperAdmin(), 403);

        return $this->csvResponse(
            'financial-' . now()->format('Y-m-d') . '.csv',
            ['عضو', 'موبایل', 'نوع', 'مبلغ', 'موجودی بعد', 'کد پیگیری', 'توضیح', 'تاریخ'],
            function ($out) {
                WalletTransaction::with('member')->latest()->chunk(100, function ($txns) use ($out) {
                    foreach ($txns as $t) {
                        fputcsv($out, [
                            $t->member->first_name . ' ' . $t->member->last_name,
                            $t->member->phone,
                            $this->txnTypeLabel($t->type),
                            $t->amount,
                            $t->balance_after,
                            $t->tracking_code,
                            $t->description ?? '-',
                            $t->created_at->format('Y-m-d H:i'),
                        ]);
                    }
                });
            }
        );
    }

    // ── Label helpers ──
    private function statusLabel($s): string
    {
        return match ($s) {
            'approved' => 'تایید شده', 'rejected' => 'رد شده', 'suspended' => 'تعلیق',
            'pending_review' => 'در انتظار بررسی', 'needs_more_info' => 'نیاز به اطلاعات',
            'otp_pending' => 'در انتظار OTP', 'questionnaire_pending' => 'در انتظار فرم',
            default => $s,
        };
    }
    private function paymentStatusLabel($s): string
    {
        return match ($s) {
            'verified' => 'تایید شده', 'rejected' => 'رد شده', 'pending' => 'در انتظار',
            default => $s,
        };
    }
    private function attendanceLabel($s): string
    {
        return match ($s) {
            'attended' => 'حاضر', 'absent' => 'غایب', 'registered' => 'ثبت‌نام',
            'cancelled_by_user' => 'انصراف', 'cancelled_by_admin' => 'لغو مدیریتی',
            default => $s,
        };
    }
    private function txnTypeLabel($s): string
    {
        return match ($s) {
            'recharge' => 'شارژ', 'payment' => 'پرداخت', 'refund' => 'بازگشت', 'adjustment' => 'اصلاح',
            default => $s,
        };
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Feedback;
use App\Models\Member;
use App\Models\Payment;
use App\Models\QuestionnaireAnswer;
use App\Models\ScoreLog;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberDossierController extends Controller
{
    /**
     * ساخت و دانلود پروندهٔ اکسل چندصفحه‌ای برای یک عضو.
     * فقط مدیر اصلی (super_admin) دسترسی دارد.
     */
    public function export(Member $member): BinaryFileResponse
    {
        abort_unless(auth()->check() && auth()->user()->isSuperAdmin(), 403);

        $member->loadMissing('layer');

        $tmpPath = tempnam(sys_get_temp_dir(), 'dossier_').'.xlsx';

        $writer = new Writer;
        $writer->openToFile($tmpPath);

        $this->sheetGeneral($writer, $member);
        $this->sheetQuestionnaire($writer, $member);
        $this->sheetRegistrations($writer, $member);
        $this->sheetConversations($writer, $member);
        $this->sheetScoreLogs($writer, $member);
        $this->sheetWallet($writer, $member);
        $this->sheetPayments($writer, $member);

        $writer->close();

        $filename = $this->safeFilename($member);

        return response()
            ->download($tmpPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    // ── Sheets ─────────────────────────────────────────

    private function sheetGeneral(Writer $writer, Member $member): void
    {
        // صفحهٔ اول از قبل باز است
        $writer->getCurrentSheet()->setName('اطلاعات کلی');

        $writer->addRow(Row::fromValues(['عنوان', 'مقدار']));

        $age = null;
        if ($member->birth_date) {
            try {
                $age = (int) $member->birth_date->diffInYears(now());
            } catch (\Throwable $e) {
                $age = null;
            }
        }

        $rows = [
            ['نام', $member->first_name],
            ['نام خانوادگی', $member->last_name],
            ['موبایل', fa($member->phone)],
            ['تاریخ تولد', $member->birth_date ? pdate($member->birth_date, 'Y/m/d') : '—'],
            ['سن', $age !== null ? fa($age) : '—'],
            ['شهر', $member->city ?? '—'],
            ['شغل', $member->job ?? '—'],
            ['تحصیلات', $member->education ?? '—'],
            ['بیوگرافی', $member->bio ?? '—'],
            ['تاریخ عضویت', pdate($member->created_at, 'Y/m/d')],
            ['لایهٔ فعلی', $member->layer?->name ?? '—'],
            ['امتیاز', fa($member->score)],
            ['آخرین بازدید', $member->last_seen_at ? pdate($member->last_seen_at, 'Y/m/d H:i') : '—'],
            ['روزهای سپری‌شده از عضویت', fa($member->daysSinceJoin())],
            ['روزهای سپری‌شده از آخرین بازدید', fa($member->daysSinceSeen())],
            ['وضعیت عضویت', $this->statusLabel($member->status)],
            ['موجودی کیف پول (تومان)', fanum($member->wallet_balance)],
        ];

        foreach ($rows as $r) {
            $writer->addRow(Row::fromValues($r));
        }
    }

    private function sheetQuestionnaire(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('پرسشنامه عضویت');

        $writer->addRow(Row::fromValues(['سوال', 'پاسخ']));

        $answers = QuestionnaireAnswer::with('question')
            ->where('member_id', $member->id)
            ->get();

        if ($answers->isEmpty()) {
            $writer->addRow(Row::fromValues(['—', 'پاسخی ثبت نشده است']));

            return;
        }

        foreach ($answers as $answer) {
            $writer->addRow(Row::fromValues([
                $answer->question->question ?? 'سوال',
                (string) $answer->answer,
            ]));
        }
    }

    private function sheetRegistrations(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('دورهمی‌ها');

        $writer->addRow(Row::fromValues([
            'دورهمی', 'تاریخ', 'وضعیت حضور', 'مبلغ نهایی (تومان)', 'امتیاز بازخورد', 'نظر بازخورد',
        ]));

        $regs = $member->registrations()->with('event')->orderByDesc('registered_at')->get();

        if ($regs->isEmpty()) {
            $writer->addRow(Row::fromValues(['—', '—', '—', '', '', '']));

            return;
        }

        // بازخوردهای عضو بر اساس event_id
        $feedbacks = Feedback::where('member_id', $member->id)->get()->keyBy('event_id');

        foreach ($regs as $r) {
            $fb = $feedbacks->get($r->event_id);
            $writer->addRow(Row::fromValues([
                $r->event?->title ?? '—',
                $r->event?->starts_at ? pdate($r->event->starts_at, 'Y/m/d H:i') : '—',
                $this->attendanceLabel($r->attendance_status),
                (int) $r->final_price,
                $fb ? fa($fb->rating) : '',
                $fb?->comment ?? '',
            ]));
        }
    }

    private function sheetConversations(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('گفتگوها');

        $writer->addRow(Row::fromValues(['موضوع', 'تاریخ', 'فرستنده', 'متن']));

        $conversations = Conversation::where('member_id', $member->id)
            ->with('messages')
            ->orderBy('created_at')
            ->get();

        $hasAny = false;
        foreach ($conversations as $conv) {
            foreach ($conv->messages as $msg) {
                $hasAny = true;
                $writer->addRow(Row::fromValues([
                    $conv->subject ?? '—',
                    pdate($msg->created_at, 'Y/m/d H:i'),
                    $msg->sender_type === 'admin' ? 'مدیریت' : 'عضو',
                    (string) $msg->body,
                ]));
            }
        }

        if (! $hasAny) {
            $writer->addRow(Row::fromValues(['—', '—', '—', 'گفتگویی ثبت نشده است']));
        }
    }

    private function sheetScoreLogs(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('امتیازها');

        $writer->addRow(Row::fromValues(['تاریخ', 'تغییر امتیاز', 'دلیل', 'امتیاز بعد']));

        $logs = ScoreLog::where('member_id', $member->id)->orderByDesc('created_at')->get();

        if ($logs->isEmpty()) {
            $writer->addRow(Row::fromValues(['—', '', '—', '']));

            return;
        }

        foreach ($logs as $log) {
            $writer->addRow(Row::fromValues([
                pdate($log->created_at, 'Y/m/d H:i'),
                (int) $log->points,
                (string) $log->reason_label,
                (int) $log->score_after,
            ]));
        }
    }

    private function sheetWallet(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('کیف پول');

        $writer->addRow(Row::fromValues([
            'تاریخ', 'نوع', 'مبلغ (تومان)', 'موجودی بعد', 'کد پیگیری', 'توضیح',
        ]));

        $txns = $member->walletTransactions()->get();

        if ($txns->isEmpty()) {
            $writer->addRow(Row::fromValues(['—', '—', '', '', '—', '']));

            return;
        }

        foreach ($txns as $t) {
            $writer->addRow(Row::fromValues([
                pdate($t->created_at, 'Y/m/d H:i'),
                $t->typeLabel(),
                (int) $t->amount,
                (int) $t->balance_after,
                (string) ($t->tracking_code ?? '—'),
                (string) ($t->description ?? ''),
            ]));
        }
    }

    private function sheetPayments(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('پرداخت‌ها');

        $writer->addRow(Row::fromValues([
            'تاریخ', 'روش', 'مبلغ (تومان)', 'وضعیت', 'شماره پیگیری', 'دورهمی',
        ]));

        $payments = Payment::where('member_id', $member->id)
            ->with('event')
            ->orderByDesc('created_at')
            ->get();

        if ($payments->isEmpty()) {
            $writer->addRow(Row::fromValues(['—', '—', '', '—', '—', '—']));

            return;
        }

        foreach ($payments as $p) {
            $writer->addRow(Row::fromValues([
                pdate($p->created_at, 'Y/m/d H:i'),
                $p->methodLabel(),
                (int) $p->amount,
                $this->paymentStatusLabel($p->status),
                (string) ($p->tracking_number ?? '—'),
                $p->event?->title ?? '—',
            ]));
        }
    }

    // ── Helpers ────────────────────────────────────────

    private function safeFilename(Member $member): string
    {
        $name = trim(($member->first_name ?? '').' '.($member->last_name ?? ''));
        // حذف کاراکترهای غیرمجاز در نام فایل
        $name = preg_replace('/[\\/\\\\:\\*\\?"<>\\|]+/u', '', $name);
        $name = trim($name);

        if ($name === '') {
            $name = 'عضو-'.$member->id;
        }

        return 'پرونده-'.$name.'.xlsx';
    }

    private function statusLabel($s): string
    {
        return match ($s) {
            'approved' => 'تایید شده',
            'rejected' => 'رد شده',
            'suspended' => 'تعلیق شده',
            'pending_review' => 'در انتظار بررسی',
            'needs_more_info' => 'نیاز به اطلاعات بیشتر',
            'otp_pending' => 'در انتظار OTP',
            'questionnaire_pending' => 'در انتظار فرم',
            default => (string) $s,
        };
    }

    private function attendanceLabel($s): string
    {
        return match ($s) {
            'attended' => 'حاضر',
            'absent' => 'غایب',
            'registered' => 'ثبت‌نام',
            'cancelled_by_user' => 'انصراف',
            'cancelled_by_admin' => 'لغو مدیریتی',
            default => (string) $s,
        };
    }

    private function paymentStatusLabel($s): string
    {
        return match ($s) {
            'verified' => 'تایید شده',
            'rejected' => 'رد شده',
            'pending' => 'در انتظار',
            default => (string) $s,
        };
    }
}

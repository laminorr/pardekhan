<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\DailyMood;
use App\Models\Feedback;
use App\Models\Member;
use App\Models\Payment;
use App\Models\QuestionnaireAnswer;
use App\Models\ScoreLog;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberDossierController extends Controller
{
    /** رنگ سبز کاج پردخان برای سرستون‌ها */
    private const HEADER_BG = '2F5D50';

    /** رنگ ردیف‌های زبرا (خیلی روشن) */
    private const ZEBRA_BG = 'F4F6F5';

    /** رنگ خطوط جدول */
    private const BORDER_COLOR = 'B7C2BD';

    private ?Style $headerStyle = null;

    private ?Style $normalStyle = null;

    private ?Style $zebraStyle = null;

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
        $this->sheetDailyMoods($writer, $member);

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
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('اطلاعات کلی');
        $this->styleSheet($sheet, [30, 55]);

        $this->addHeaderRow($writer, ['عنوان', 'مقدار']);

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

        $i = 0;
        foreach ($rows as $r) {
            $this->addDataRow($writer, $r, $i);
        }
    }

    private function sheetQuestionnaire(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('پرسشنامه عضویت');
        $this->styleSheet($sheet, [45, 60]);

        $this->addHeaderRow($writer, ['سوال', 'پاسخ']);

        $answers = QuestionnaireAnswer::with('question')
            ->where('member_id', $member->id)
            ->get();

        if ($answers->isEmpty()) {
            $i = 0;
            $this->addDataRow($writer, ['—', 'پاسخی ثبت نشده است'], $i);

            return;
        }

        $i = 0;
        foreach ($answers as $answer) {
            $this->addDataRow($writer, [
                $answer->question->question ?? 'سوال',
                (string) $answer->answer,
            ], $i);
        }
    }

    private function sheetRegistrations(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('دورهمی‌ها');
        $this->styleSheet($sheet, [32, 18, 16, 18, 14, 55]);

        $this->addHeaderRow($writer, [
            'دورهمی', 'تاریخ', 'وضعیت حضور', 'مبلغ نهایی (تومان)', 'امتیاز بازخورد', 'نظر بازخورد',
        ]);

        $regs = $member->registrations()->with('event')->orderByDesc('registered_at')->get();

        if ($regs->isEmpty()) {
            $i = 0;
            $this->addDataRow($writer, ['—', '—', '—', '', '', ''], $i);

            return;
        }

        // بازخوردهای عضو بر اساس event_id
        $feedbacks = Feedback::where('member_id', $member->id)->get()->keyBy('event_id');

        $i = 0;
        foreach ($regs as $r) {
            $fb = $feedbacks->get($r->event_id);
            $this->addDataRow($writer, [
                $r->event?->title ?? '—',
                $r->event?->starts_at ? pdate($r->event->starts_at, 'Y/m/d H:i') : '—',
                $this->attendanceLabel($r->attendance_status),
                (int) $r->final_price,
                $fb ? fa($fb->rating) : '',
                $fb?->comment ?? '',
            ], $i);
        }
    }

    private function sheetConversations(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('گفتگوها');
        $this->styleSheet($sheet, [28, 18, 14, 65]);

        $this->addHeaderRow($writer, ['موضوع', 'تاریخ', 'فرستنده', 'متن']);

        $conversations = Conversation::where('member_id', $member->id)
            ->with('messages')
            ->orderBy('created_at')
            ->get();

        $i = 0;
        $hasAny = false;
        foreach ($conversations as $conv) {
            foreach ($conv->messages as $msg) {
                $hasAny = true;
                $this->addDataRow($writer, [
                    $conv->subject ?? '—',
                    pdate($msg->created_at, 'Y/m/d H:i'),
                    $msg->sender_type === 'admin' ? 'مدیریت' : 'عضو',
                    (string) $msg->body,
                ], $i);
            }
        }

        if (! $hasAny) {
            $this->addDataRow($writer, ['—', '—', '—', 'گفتگویی ثبت نشده است'], $i);
        }
    }

    private function sheetScoreLogs(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('امتیازها');
        $this->styleSheet($sheet, [18, 14, 40, 14]);

        $this->addHeaderRow($writer, ['تاریخ', 'تغییر امتیاز', 'دلیل', 'امتیاز بعد']);

        $logs = ScoreLog::where('member_id', $member->id)->orderByDesc('created_at')->get();

        if ($logs->isEmpty()) {
            $i = 0;
            $this->addDataRow($writer, ['—', '', '—', ''], $i);

            return;
        }

        $i = 0;
        foreach ($logs as $log) {
            $this->addDataRow($writer, [
                pdate($log->created_at, 'Y/m/d H:i'),
                (int) $log->points,
                (string) $log->reason_label,
                (int) $log->score_after,
            ], $i);
        }
    }

    private function sheetWallet(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('کیف پول');
        $this->styleSheet($sheet, [18, 20, 18, 18, 22, 45]);

        $this->addHeaderRow($writer, [
            'تاریخ', 'نوع', 'مبلغ (تومان)', 'موجودی بعد', 'کد پیگیری', 'توضیح',
        ]);

        $txns = $member->walletTransactions()->get();

        if ($txns->isEmpty()) {
            $i = 0;
            $this->addDataRow($writer, ['—', '—', '', '', '—', ''], $i);

            return;
        }

        $i = 0;
        foreach ($txns as $t) {
            $this->addDataRow($writer, [
                pdate($t->created_at, 'Y/m/d H:i'),
                $t->typeLabel(),
                (int) $t->amount,
                (int) $t->balance_after,
                (string) ($t->tracking_code ?? '—'),
                (string) ($t->description ?? ''),
            ], $i);
        }
    }

    private function sheetPayments(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('پرداخت‌ها');
        $this->styleSheet($sheet, [18, 20, 18, 16, 24, 32]);

        $this->addHeaderRow($writer, [
            'تاریخ', 'روش', 'مبلغ (تومان)', 'وضعیت', 'شماره پیگیری', 'دورهمی',
        ]);

        $payments = Payment::where('member_id', $member->id)
            ->with('event')
            ->orderByDesc('created_at')
            ->get();

        if ($payments->isEmpty()) {
            $i = 0;
            $this->addDataRow($writer, ['—', '—', '', '—', '—', '—'], $i);

            return;
        }

        $i = 0;
        foreach ($payments as $p) {
            $this->addDataRow($writer, [
                pdate($p->created_at, 'Y/m/d H:i'),
                $p->methodLabel(),
                (int) $p->amount,
                $this->paymentStatusLabel($p->status),
                (string) ($p->tracking_number ?? '—'),
                $p->event?->title ?? '—',
            ], $i);
        }
    }

    private function sheetDailyMoods(Writer $writer, Member $member): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('حال روزانه');
        $this->styleSheet($sheet, [22, 24]);

        $moods = $member->dailyMoods()->orderByDesc('mood_date')->get();

        // ── خلاصه: چند روز در هر حال بوده (تا امروز) ──
        $this->addHeaderRow($writer, ['حال', 'تعداد روز']);

        $counts = $moods->countBy('mood'); // [mood => count]
        $i = 0;
        foreach (DailyMood::LABELS as $value => $label) {
            $days = (int) ($counts[$value] ?? 0);
            $this->addDataRow($writer, [$label, fa($days).' روز'], $i);
        }

        // فاصله بین خلاصه و ریز روزها
        $writer->addRow(Row::fromValues(['', '']));

        // ── ریز روزها (جدیدترین بالا) ──
        $this->addHeaderRow($writer, ['تاریخ', 'حال']);

        if ($moods->isEmpty()) {
            $j = 0;
            $this->addDataRow($writer, ['—', 'حالی ثبت نشده است'], $j);

            return;
        }

        $j = 0;
        foreach ($moods as $m) {
            $this->addDataRow($writer, [
                pdate($m->mood_date, 'Y/m/d'),
                DailyMood::label($m->mood),
            ], $j);
        }
    }

    // ── Styling helpers ────────────────────────────────

    /**
     * تنظیم راست‌به‌چپ و پهنای ستون‌ها برای یک صفحه.
     *
     * @param  array<int, float|int>  $widths  پهنای ستون‌ها به ترتیب (از ستون ۱)
     */
    private function styleSheet(Sheet $sheet, array $widths): void
    {
        // راست‌به‌چپ کردن نمای صفحه تا متن فارسی درست خوانده شود
        $sheet->setSheetView((new SheetView)->setRightToLeft(true));

        foreach ($widths as $index => $width) {
            $sheet->setColumnWidth((float) $width, $index + 1);
        }
    }

    /**
     * افزودن ردیف سرستون با استایل سبز پررنگ و متن سفید.
     *
     * @param  array<int, mixed>  $values
     */
    private function addHeaderRow(Writer $writer, array $values): void
    {
        $row = Row::fromValues($values, $this->headerStyle());
        $row->setHeight(24);
        $writer->addRow($row);
    }

    /**
     * افزودن یک ردیف داده با کادر و راه‌راه (زبرا) بر اساس شمارندهٔ ردیف.
     *
     * @param  array<int, mixed>  $values
     */
    private function addDataRow(Writer $writer, array $values, int &$rowIndex): void
    {
        $style = ($rowIndex % 2 === 1) ? $this->zebraStyle() : $this->normalStyle();
        $writer->addRow(Row::fromValues($values, $style));
        $rowIndex++;
    }

    private function tableBorder(): Border
    {
        return new Border(
            new BorderPart(Border::TOP, self::BORDER_COLOR, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, self::BORDER_COLOR, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, self::BORDER_COLOR, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::LEFT, self::BORDER_COLOR, Border::WIDTH_THIN, Border::STYLE_SOLID),
        );
    }

    private function headerStyle(): Style
    {
        return $this->headerStyle ??= (new Style)
            ->setFontBold()
            ->setFontSize(12)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor(self::HEADER_BG)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($this->tableBorder());
    }

    private function normalStyle(): Style
    {
        return $this->normalStyle ??= (new Style)
            ->setFontSize(11)
            ->setShouldWrapText()
            ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
            ->setBorder($this->tableBorder());
    }

    private function zebraStyle(): Style
    {
        return $this->zebraStyle ??= (new Style)
            ->setFontSize(11)
            ->setShouldWrapText()
            ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
            ->setBackgroundColor(self::ZEBRA_BG)
            ->setBorder($this->tableBorder());
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

<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function __construct(
        private WalletService $wallet
    ) {}

    /**
     * ثبت‌نام با کیف پول — اتمیک با قفل ظرفیت
     */
    public function registerWithWallet(Member $member, Event $event): array
    {
        return DB::transaction(function () use ($member, $event) {
            // قفل رویداد برای کنترل ظرفیت
            $event = Event::lockForUpdate()->find($event->id);

            $check = $this->canRegister($member, $event);
            if (! $check['ok']) {
                return $check;
            }

            $price = $event->priceForMember($member);

            // چک موجودی کیف پول
            if ($member->wallet_balance < $price) {
                return ['ok' => false, 'message' => 'موجودی کیف پول کافی نیست'];
            }

            // ساخت ثبت‌نام
            $registration = Registration::create([
                'event_id'          => $event->id,
                'member_id'         => $member->id,
                'final_price'       => $price,
                'attendance_status' => 'registered',
                'payment_status'    => 'verified',
            ]);

            // پرداخت از کیف پول
            $payment = Payment::create([
                'member_id'       => $member->id,
                'event_id'        => $event->id,
                'registration_id' => $registration->id,
                'method'          => 'wallet',
                'amount'          => $price,
                'status'          => 'verified',
                'verified_at'     => now(),
            ]);

            $registration->update(['payment_id' => $payment->id]);

            // کسر از کیف پول
            $this->wallet->pay($member, $price, "پرداخت دورهمی: {$event->title}", $event);

            // صدور بلیت (پرداخت کیف پول قطعیه)
            app(TicketService::class)->issue($registration);

            // به‌روزرسانی وضعیت ظرفیت
            $this->updateEventCapacityStatus($event);

            return ['ok' => true, 'registration' => $registration, 'message' => 'ثبت‌نام با موفقیت انجام شد'];
        });
    }

    /**
     * ثبت‌نام با کارت به کارت — بر اساس اعتماد، قطعی میشه ولی پرداخت pending
     */
    public function registerWithCardToCard(Member $member, Event $event, string $trackingNumber): array
    {
        return DB::transaction(function () use ($member, $event, $trackingNumber) {
            $event = Event::lockForUpdate()->find($event->id);

            $check = $this->canRegister($member, $event);
            if (! $check['ok']) {
                return $check;
            }

            $price = $event->priceForMember($member);

            $registration = Registration::create([
                'event_id'          => $event->id,
                'member_id'         => $member->id,
                'final_price'       => $price,
                'attendance_status' => 'registered',
                'payment_status'    => 'pending',
            ]);

            $payment = Payment::create([
                'member_id'       => $member->id,
                'event_id'        => $event->id,
                'registration_id' => $registration->id,
                'method'          => 'card_to_card',
                'amount'          => $price,
                'status'          => 'pending',
                'tracking_number' => $trackingNumber,
            ]);

            $registration->update(['payment_id' => $payment->id]);

            // صدور بلیت (ثبت‌نام بر اساس اعتماد قطعیه)
            app(TicketService::class)->issue($registration);

            $this->updateEventCapacityStatus($event);

            return ['ok' => true, 'registration' => $registration, 'message' => 'ثبت‌نام شما ثبت شد و پس از بررسی پرداخت تایید می‌شود'];
        });
    }

    /**
     * بررسی امکان ثبت‌نام
     */
    public function canRegister(Member $member, Event $event): array
    {
        if ($member->status !== 'approved') {
            return ['ok' => false, 'message' => 'حساب شما هنوز تایید نشده است'];
        }

        // بدهی کیف پول
        if ($member->hasWalletDebt()) {
            return ['ok' => false, 'message' => 'لطفاً به دلیل بدهی، ابتدا کیف پول خود را شارژ کنید'];
        }

        // قبلاً ثبت‌نام کرده؟
        $already = $event->registrations()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['registered', 'attended'])
            ->exists();
        if ($already) {
            return ['ok' => false, 'message' => 'شما قبلاً ثبت‌نام کرده‌اید'];
        }

        // ثبت‌نام باز است؟
        if (! $event->isRegistrationOpen()) {
            return ['ok' => false, 'message' => 'ثبت‌نام این دورهمی باز نیست'];
        }

        // ظرفیت
        if ($event->remainingCapacity() <= 0) {
            return ['ok' => false, 'message' => 'ظرفیت تکمیل شده است'];
        }

        return ['ok' => true];
    }

    /**
     * به‌روزرسانی وضعیت ظرفیت رویداد
     */
    private function updateEventCapacityStatus(Event $event): void
    {
        if ($event->remainingCapacity() <= 0 && $event->status === 'active') {
            $event->update(['status' => 'full']);
        }
    }

    /**
     * انصراف کاربر
     */
    public function cancelByUser(Member $member, Event $event): array
    {
        $registration = $event->registrations()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['registered'])
            ->first();

        if (! $registration) {
            return ['ok' => false, 'message' => 'ثبت‌نام فعالی یافت نشد'];
        }

        $registration->update(['attendance_status' => 'cancelled_by_user']);

        // امتیاز انصراف با اطلاع
        app(ScoreService::class)->addByKey($member, 'cancellation');

        // اگه ظرفیت باز شد و رویداد full بود، active کن
        if ($event->status === 'full' && $event->remainingCapacity() > 0) {
            $event->update(['status' => 'active']);
            // TODO فاز ۶: اطلاع به لیست انتظار
        }

        return ['ok' => true, 'message' => 'انصراف شما ثبت شد. وجه پرداختی بازگردانده نمی‌شود.'];
    }
}

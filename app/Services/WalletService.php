<?php

namespace App\Services;

use App\Models\Member;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * تغییر موجودی کیف پول + ثبت تراکنش (اتمیک)
     */
    public function transact(
        Member $member,
        string $type,        // recharge, payment, refund, adjustment
        int $amount,         // مثبت یا منفی
        ?string $description = null,
        mixed $related = null,
        ?int $adminId = null
    ): WalletTransaction {
        return DB::transaction(function () use ($member, $type, $amount, $description, $related, $adminId) {
            // قفل ردیف member برای جلوگیری از race condition
            $member = Member::lockForUpdate()->find($member->id);

            // جلوگیری از منفی شدن موجودی (برای برداشت‌ها) — پس از قفل بررسی می‌شود
            if ($amount < 0 && ($member->wallet_balance + $amount) < 0) {
                throw new \App\Exceptions\InsufficientBalanceException();
            }

            $newBalance = $member->wallet_balance + $amount;
            $member->update(['wallet_balance' => $newBalance]);

            $data = [
                'member_id'     => $member->id,
                'type'          => $type,
                'amount'        => $amount,
                'balance_after' => $newBalance,
                'tracking_code' => 'WT-' . strtoupper(Str::random(10)),
                'description'   => $description,
                'admin_id'      => $adminId,
            ];

            if ($related) {
                $data['related_type'] = get_class($related);
                $data['related_id']   = $related->id;
            }

            return WalletTransaction::create($data);
        });
    }

    public function recharge(Member $member, int $amount, ?string $description = null): WalletTransaction
    {
        return $this->transact($member, 'recharge', abs($amount), $description ?? 'شارژ کیف پول');
    }

    public function pay(Member $member, int $amount, ?string $description = null, mixed $related = null): WalletTransaction
    {
        return $this->transact($member, 'payment', -abs($amount), $description ?? 'پرداخت', $related);
    }

    public function refund(Member $member, int $amount, ?string $description = null, mixed $related = null): WalletTransaction
    {
        return $this->transact($member, 'refund', abs($amount), $description ?? 'بازگشت وجه', $related);
    }

    public function adjust(Member $member, int $amount, string $description, int $adminId): WalletTransaction
    {
        return $this->transact($member, 'adjustment', $amount, $description, null, $adminId);
    }
}

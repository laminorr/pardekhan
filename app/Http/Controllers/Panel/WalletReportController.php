<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\PaymentReport;
use Illuminate\Http\Request;

class WalletReportController extends Controller
{
    /**
     * ثبتِ «اطلاع پرداختی» توسط عضو (کارت‌به‌کارت).
     * فقط یک گزارشِ در انتظار ساخته می‌شود؛ هیچ شارژی این‌جا انجام نمی‌شود —
     * شارژِ کیف پول پس از تأییدِ مدیریت از طریق WalletService انجام می‌گیرد.
     */
    public function store(Request $request)
    {
        $member = auth('member')->user();

        $validated = $request->validate([
            'amount'          => ['required', 'integer', 'min:1000'],
            'tracking_number' => ['required', 'string', 'max:255'],
        ]);

        PaymentReport::create([
            'member_id'       => $member->id,
            'amount'          => $validated['amount'],
            'tracking_number' => $validated['tracking_number'],
            'status'          => PaymentReport::STATUS_PENDING,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('panel.wallet')->with('payment_report_saved', true);
    }
}

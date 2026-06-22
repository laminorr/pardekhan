<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WalletTransaction;

class WalletController extends Controller
{
    public function index()
    {
        $member = auth('member')->user();

        $transactions = WalletTransaction::where('member_id', $member->id)
            ->latest()
            ->limit(50)
            ->get();

        $cardNumber = Setting::get('card_number');
        $cardHolder = Setting::get('card_holder');

        return view('panel.wallet.index', compact('member', 'transactions', 'cardNumber', 'cardHolder'));
    }
}

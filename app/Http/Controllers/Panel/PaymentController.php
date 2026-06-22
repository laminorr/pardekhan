<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Setting;
use App\Services\RegistrationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private RegistrationService $registration
    ) {}

    // صفحه انتخاب روش پرداخت
    public function checkout(Event $event)
    {
        $member = auth('member')->user();

        // بررسی امکان ثبت‌نام
        $check = $this->registration->canRegister($member, $event);
        if (! $check['ok']) {
            return redirect()->route('panel.events.show', $event)
                ->with('error', $check['message']);
        }

        $price = $event->priceForMember($member);
        $basePrice = $event->base_price;
        $discount = $event->discountForLayer($member->layer);
        $discountAmount = $basePrice - $price;
        $walletBalance = $member->wallet_balance;
        $canUseWallet = $walletBalance >= $price;

        $cardNumber = Setting::get('card_number');
        $cardHolder = Setting::get('card_holder');
        $gatewayEnabled = Setting::get('gateway_enabled') === '1';
        $cardToCardEnabled = Setting::get('card_to_card_enabled') === '1';

        return view('panel.payment.checkout', compact(
            'event', 'price', 'basePrice', 'discount', 'discountAmount',
            'walletBalance', 'canUseWallet',
            'cardNumber', 'cardHolder', 'gatewayEnabled', 'cardToCardEnabled'
        ));
    }

    // پرداخت با کیف پول
    public function payWithWallet(Event $event)
    {
        $member = auth('member')->user();
        $result = $this->registration->registerWithWallet($member, $event);

        if (! $result['ok']) {
            return redirect()->route('panel.events.show', $event)->with('error', $result['message']);
        }

        return redirect()->route('panel.events.show', $event)->with('success', $result['message']);
    }

    // پرداخت کارت به کارت
    public function payWithCardToCard(Request $request, Event $event)
    {
        $request->validate([
            'tracking_number' => ['required', 'string', 'max:50'],
        ], [
            'tracking_number.required' => 'شماره پیگیری الزامی است',
        ]);

        $member = auth('member')->user();
        $result = $this->registration->registerWithCardToCard($member, $event, $request->tracking_number);

        if (! $result['ok']) {
            return redirect()->route('panel.events.show', $event)->with('error', $result['message']);
        }

        return redirect()->route('panel.events.show', $event)->with('success', $result['message']);
    }
}

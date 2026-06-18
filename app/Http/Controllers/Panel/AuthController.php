<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ── نمایش فرم ثبت‌نام ──────────────────────────────
    public function showRegister()
    {
        return view('panel.auth.register');
    }

    // ── ثبت‌نام ─────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'phone'      => ['required', 'string', 'max:15', 'regex:/^09[0-9]{9}$/'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'first_name.required' => 'نام الزامی است',
            'last_name.required'  => 'نام خانوادگی الزامی است',
            'phone.required'      => 'شماره موبایل الزامی است',
            'phone.regex'         => 'شماره موبایل معتبر نیست',
            'password.required'   => 'رمز عبور الزامی است',
            'password.min'        => 'رمز عبور باید حداقل ۸ کاراکتر باشد',
            'password.confirmed'  => 'تکرار رمز عبور مطابقت ندارد',
        ]);

        // چک: اگه قبلاً ثبت‌نام کرده
        $existing = Member::where('phone', $request->phone)->first();

        if ($existing) {
            if ($existing->status === 'rejected') {
                return back()->withErrors(['phone' => 'متأسفانه این شماره در سیستم رد شده است.']);
            }
            return back()->withErrors(['phone' => 'این شماره موبایل قبلاً ثبت‌نام کرده است.']);
        }

        // ساخت عضو جدید
        $member = Member::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'status'     => 'otp_pending',
        ]);

        // ارسال OTP
        $this->sendOtp($member);

        // ذخیره شماره در session برای صفحه OTP
        session(['otp_phone' => $request->phone]);

        return redirect()->route('panel.otp');
    }

    // ── نمایش فرم OTP ───────────────────────────────────
    public function showOtp()
    {
        if (! session('otp_phone')) {
            return redirect()->route('panel.register');
        }
        return view('panel.auth.otp', ['phone' => session('otp_phone')]);
    }

    // ── تایید OTP ────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'کد تایید الزامی است',
            'code.size'     => 'کد تایید باید ۶ رقم باشد',
        ]);

        $phone = session('otp_phone');
        if (! $phone) {
            return redirect()->route('panel.register');
        }

        $member = Member::where('phone', $phone)->first();
        if (! $member) {
            return redirect()->route('panel.register');
        }

        // چک قفل بودن
        if ($member->isOtpLocked()) {
            return back()->withErrors(['code' => 'تعداد تلاش‌های ناموفق زیاد بوده. لطفاً چند دقیقه صبر کنید.']);
        }

        // چک اعتبار کد
        if (! $member->isOtpValid($request->code)) {
            $member->increment('otp_attempts');

            // قفل بعد از ۵ تلاش
            if ($member->otp_attempts >= 5) {
                $member->update(['otp_locked_until' => now()->addMinutes(10)]);
                return back()->withErrors(['code' => 'حساب شما به مدت ۱۰ دقیقه قفل شد.']);
            }

            return back()->withErrors(['code' => 'کد تایید اشتباه یا منقضی شده است.']);
        }

        // تایید موفق
        $member->update([
            'status'       => 'questionnaire_pending',
            'otp_code'     => null,
            'otp_attempts' => 0,
        ]);

        Auth::guard('member')->login($member, true);
        session()->forget('otp_phone');

        return redirect()->route('panel.questionnaire');
    }

    // ── ارسال مجدد OTP ───────────────────────────────────
    public function resendOtp(Request $request)
    {
        $phone = session('otp_phone');
        if (! $phone) {
            return redirect()->route('panel.register');
        }

        $member = Member::where('phone', $phone)->first();
        if (! $member) {
            return redirect()->route('panel.register');
        }

        // محدودیت: حداکثر ۳ بار در ۱۰ دقیقه
        $key = 'otp_resend_' . $phone;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['code' => 'تعداد درخواست OTP زیاد است. لطفاً ۱۰ دقیقه صبر کنید.']);
        }
        RateLimiter::hit($key, 600);

        $this->sendOtp($member);

        return back()->with('success', 'کد جدید ارسال شد.');
    }

    // ── نمایش فرم لاگین ─────────────────────────────────
    public function showLogin()
    {
        return view('panel.auth.login');
    }

    // ── لاگین ────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required'    => 'شماره موبایل الزامی است',
            'password.required' => 'رمز عبور الزامی است',
        ]);

        $member = Member::where('phone', $request->phone)->first();

        if (! $member || ! Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'phone' => 'شماره موبایل یا رمز عبور اشتباه است.',
            ]);
        }

        Auth::guard('member')->login($member, $request->boolean('remember'));

        return redirect()->intended(route('panel.dashboard'));
    }

    // ── خروج ─────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('panel.login');
    }

    // ── ارسال OTP (فعلاً فقط لاگ) ───────────────────────
    private function sendOtp(Member $member): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $member->update([
            'otp_code'        => $code,
            'otp_expires_at'  => now()->addMinutes(5),
            'otp_attempts'    => 0,
            'otp_locked_until'=> null,
        ]);

        // TODO: در فاز ۶ پیامک واقعی اضافه می‌شود
        // فعلاً در لاگ ذخیره می‌شود
        \Illuminate\Support\Facades\Log::info("OTP for {$member->phone}: {$code}");
    }
}

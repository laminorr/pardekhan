<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $member = auth('member')->user();
        return view('panel.profile', compact('member'));
    }

    public function update(Request $request)
    {
        $member = auth('member')->user();

        $request->validate([
            'birth_date' => ['nullable', 'date'],
            'city'       => ['nullable', 'string', 'max:50'],
            'job'        => ['nullable', 'string', 'max:100'],
            'education'  => ['nullable', 'string', 'max:100'],
            'bio'        => ['nullable', 'string', 'max:500'],
            'avatar'     => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
        ], [
            'avatar.image' => 'فایل باید تصویر باشد',
            'avatar.max'   => 'حجم تصویر نباید بیشتر از ۲ مگابایت باشد',
            'avatar.mimes' => 'فرمت تصویر باید jpg یا png باشد',
        ]);

        $data = $request->only(['birth_date', 'city', 'job', 'education', 'bio']);

        // آپلود عکس
        if ($request->hasFile('avatar')) {
            if ($member->avatar) {
                Storage::disk('public')->delete($member->avatar);
            }

            // فشرده‌سازی به زیر ۱۵۰ کیلوبایت (همیشه به jpg تبدیل می‌شود)
            $filename = 'avatars/' . \Illuminate\Support\Str::random(40) . '.jpg';
            $destFull = Storage::disk('public')->path($filename);

            // مطمئن شو پوشه وجود دارد
            if (! is_dir(dirname($destFull))) {
                mkdir(dirname($destFull), 0755, true);
            }

            \App\Services\ImageCompressor::compress(
                $request->file('avatar')->getRealPath(),
                $destFull,
                150,  // حداکثر ۱۵۰ کیلوبایت
                800   // حداکثر ابعاد ۸۰۰ پیکسل
            );

            $data['avatar'] = $filename;
            $data['avatar_approved'] = false; // می‌رود برای تأیید مدیر
        }

        // ذخیره اطلاعات
        $member->update($data);
        $member->refresh();

        // بررسی تکمیل پروفایل بعد از ذخیره
        // شرط: شهر و شغل پر باشند و قبلاً امتیاز نگرفته باشد
        if (! $member->profile_completed && filled($member->city) && filled($member->job)) {
            $member->update(['profile_completed' => true]);
            app(ScoreService::class)->addByKey($member, 'profile_complete');
            return back()->with('success', 'پروفایل شما تکمیل شد و امتیاز دریافت کردید! 🎉');
        }

        return back()->with('success', 'پروفایل با موفقیت به‌روزرسانی شد');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'new_password.required' => 'رمز جدید الزامی است',
            'new_password.min' => 'رمز باید حداقل ۶ کاراکتر باشد',
            'new_password.confirmed' => 'تکرار رمز مطابقت ندارد',
        ]);

        $member = auth('member')->user();
        $member->update(['password' => bcrypt($request->new_password)]);

        return back()->with('success', 'رمز عبور با موفقیت تغییر کرد');
    }
}

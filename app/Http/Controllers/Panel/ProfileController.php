<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ScoreSetting;
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
            // حذف عکس قبلی
            if ($member->avatar) {
                Storage::disk('public')->delete($member->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
            $data['avatar_approved'] = false; // نیاز به تایید مجدد
        }

        // بررسی تکمیل پروفایل و امتیاز یک‌باره
        $wasIncomplete = ! $member->profile_completed;
        $isNowComplete = $member->city && $member->job && ($data['city'] ?? $member->city) && ($data['job'] ?? $member->job);

        $member->update($data);

        // امتیاز تکمیل پروفایل (فقط یک بار)
        if ($wasIncomplete && $isNowComplete) {
            $member->update(['profile_completed' => true]);
            app(ScoreService::class)->addByKey($member, 'profile_complete');
            return back()->with('success', 'پروفایل شما تکمیل شد و امتیاز دریافت کردید!');
        }

        return back()->with('success', 'پروفایل با موفقیت به‌روزرسانی شد');
    }
}

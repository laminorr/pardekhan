<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function show()
    {
        $member = auth('member')->user();

        if ($member->status !== 'questionnaire_pending') {
            return redirect()->route('panel.dashboard');
        }

        return view('panel.questionnaire');
    }

    public function submit(Request $request)
    {
        $member = auth('member')->user();

        if ($member->status !== 'questionnaire_pending') {
            return redirect()->route('panel.dashboard');
        }

        $request->validate([
            'answers' => ['required', 'array'],
        ]);

        // ذخیره جواب‌ها در admin_note موقتاً
        // در آینده جدول جداگانه میسازیم
        $formatted = collect($request->answers)
            ->map(fn ($v, $k) => "سوال $k: $v")
            ->implode("\n");

        $member->update([
            'status'     => 'pending_review',
            'admin_note' => "[پاسخ‌های فرم]\n" . $formatted,
        ]);

        return redirect()->route('panel.dashboard');
    }
}

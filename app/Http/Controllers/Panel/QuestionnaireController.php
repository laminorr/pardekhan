<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function show(Request $request)
    {
        $member = auth('member')->user();

        if ($member->status !== 'questionnaire_pending') {
            return redirect()->route('panel.dashboard');
        }

        $questions = QuestionnaireQuestion::active()->get();

        if ($questions->isEmpty()) {
            // اگه سوالی نبود مستقیم بره pending
            $member->update(['status' => 'pending_review']);
            return redirect()->route('panel.dashboard');
        }

        // سوال فعلی
        $currentStep = (int) $request->query('step', 1);
        $totalSteps  = $questions->count();
        $currentStep = max(1, min($currentStep, $totalSteps));

        $question = $questions[$currentStep - 1];

        // جواب قبلی اگه وجود داشت
        $previousAnswer = QuestionnaireAnswer::where('member_id', $member->id)
            ->where('question_id', $question->id)
            ->value('answer');

        return view('panel.questionnaire', compact(
            'question',
            'currentStep',
            'totalSteps',
            'previousAnswer'
        ));
    }

    public function submit(Request $request)
    {
        $member = auth('member')->user();

        if ($member->status !== 'questionnaire_pending') {
            return redirect()->route('panel.dashboard');
        }

        $questions   = QuestionnaireQuestion::active()->get();
        $totalSteps  = $questions->count();
        $currentStep = (int) $request->input('current_step', 1);

        $question = $questions[$currentStep - 1];

        // validation
        $rules = $question->is_required
            ? ['answer' => ['required', 'string', 'max:500']]
            : ['answer' => ['nullable', 'string', 'max:500']];

        $request->validate($rules, [
            'answer.required' => 'لطفاً به این سوال پاسخ دهید',
            'answer.max'      => 'پاسخ نباید بیشتر از ۵۰۰ کاراکتر باشد',
        ]);

        // ذخیره یا آپدیت جواب
        QuestionnaireAnswer::updateOrCreate(
            ['member_id' => $member->id, 'question_id' => $question->id],
            ['answer' => $request->input('answer', '')]
        );

        // آخرین سوال؟
        if ($currentStep >= $totalSteps) {
            $member->update(['status' => 'pending_review']);
            return redirect()->route('panel.dashboard');
        }

        // سوال بعدی
        return redirect()->route('panel.questionnaire', ['step' => $currentStep + 1]);
    }
}

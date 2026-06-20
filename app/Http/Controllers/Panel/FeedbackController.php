<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // فرم ثبت بازخورد
    public function create(Event $event)
    {
        $member = auth('member')->user();

        // باید در این دورهمی ثبت‌نام کرده باشه
        $registration = $event->registrations()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['attended', 'absent', 'registered'])
            ->first();

        if (! $registration) {
            return redirect()->route('panel.events.my')->with('error', 'شما در این دورهمی شرکت نداشته‌اید');
        }

        // دورهمی باید گذشته باشه
        if ($event->starts_at->isFuture()) {
            return redirect()->route('panel.events.my')->with('error', 'این دورهمی هنوز برگزار نشده است');
        }

        // اگه قبلاً بازخورد داده
        $existing = Feedback::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        if ($existing) {
            return view('panel.feedback.show', compact('event', 'existing'));
        }

        return view('panel.feedback.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $member = auth('member')->user();

        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'لطفاً امتیاز دهید',
        ]);

        // جلوگیری از تکرار
        $existing = Feedback::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->exists();

        if ($existing) {
            return redirect()->route('panel.events.my')->with('error', 'شما قبلاً بازخورد داده‌اید');
        }

        Feedback::create([
            'event_id'  => $event->id,
            'member_id' => $member->id,
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        return redirect()->route('panel.events.my')->with('success', 'بازخورد شما ثبت شد. متشکریم!');
    }
}

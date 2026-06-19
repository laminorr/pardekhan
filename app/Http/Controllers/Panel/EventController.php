<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\RegistrationService;

class EventController extends Controller
{
    public function index()
    {
        $member = auth('member')->user();

        $events = $this->visibleEvents($member);

        return view('panel.events.index', compact('member', 'events'));
    }

    public function show(Event $event)
    {
        $member = auth('member')->user();

        // چک دسترسی
        if (! $this->canView($member, $event)) {
            abort(403);
        }

        $event->load('venue', 'layers');

        $price = $event->priceForMember($member);
        $discount = $event->discountForLayer($member->layer);

        // عکس‌های تایید شده ثبت‌نام‌کنندگان
        $attendeeAvatars = $event->registrations()
            ->whereIn('attendance_status', ['registered', 'attended'])
            ->with('member')
            ->get()
            ->pluck('member')
            ->filter(fn ($m) => $m->avatar && $m->avatar_approved)
            ->take(20);

        $isRegistered = $event->registrations()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['registered', 'attended'])
            ->exists();

        $isWaiting = $event->waitingList()
            ->where('member_id', $member->id)
            ->exists();

        return view('panel.events.show', compact(
            'member', 'event', 'price', 'discount',
            'attendeeAvatars', 'isRegistered', 'isWaiting'
        ));
    }

    // دورهمی‌های قابل مشاهده برای عضو
    private function visibleEvents($member)
    {
        $layerId = $member->layer_id;

        return Event::query()
            ->whereIn('status', ['active', 'full', 'closed'])
            ->where(function ($q) use ($member, $layerId) {
                // لایه مجاز
                if ($layerId) {
                    $q->whereHas('layers', fn ($q) => $q->where('layers.id', $layerId));
                }
                // یا دعوت اختصاصی
                $q->orWhereHas('invitedMembers', fn ($q) => $q->where('members.id', $member->id));
            })
            ->with('venue')
            ->orderBy('starts_at')
            ->get();
    }

    private function canView($member, Event $event): bool
    {
        // دعوت اختصاصی
        if ($event->invitedMembers()->where('members.id', $member->id)->exists()) {
            return true;
        }
        // لایه مجاز
        if ($member->layer_id && $event->layers()->where('layers.id', $member->layer_id)->exists()) {
            return true;
        }
        return false;
    }

    public function joinWaitlist(Event $event)
    {
        $member = auth('member')->user();

        if (! $this->canView($member, $event)) {
            abort(403);
        }

        // اگه قبلاً ثبت‌نام کرده یا در لیست انتظاره، کاری نکن
        $alreadyRegistered = $event->registrations()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['registered', 'attended'])
            ->exists();

        if ($alreadyRegistered) {
            return back();
        }

        \App\Models\WaitingList::firstOrCreate([
            'event_id'  => $event->id,
            'member_id' => $member->id,
        ]);

        return back()->with('success', 'در لیست انتظار ثبت شدید');
    }


    public function cancel(Event $event)
    {
        $member = auth('member')->user();
        $result = app(RegistrationService::class)->cancelByUser($member, $event);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }


    public function myEvents()
    {
        $member = auth('member')->user();

        $registrations = $member->registrations()
            ->with('event.venue')
            ->latest('registered_at')
            ->get();

        $upcoming = $registrations->filter(fn ($r) => $r->event && $r->event->starts_at->isFuture());
        $past = $registrations->filter(fn ($r) => $r->event && $r->event->starts_at->isPast());

        return view('panel.events.my', compact('member', 'upcoming', 'past'));
    }

}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $member = auth('member')->user();

        return match ($member->status) {
            'questionnaire_pending' => redirect()->route('panel.questionnaire'),
            'pending_review'        => view('panel.status.pending'),
            'needs_more_info'       => view('panel.status.needs_more_info', compact('member')),
            'rejected'              => view('panel.status.rejected'),
            'suspended'             => view('panel.status.suspended'),
            'approved'              => view('panel.dashboard', [
                'member' => $member,
                'unreadMessages' => \App\Models\BroadcastRecipient::where('member_id', $member->id)->where('is_read', false)->count()
                    + \App\Models\Conversation::where('member_id', $member->id)->where('member_unread', true)->count(),
            ]),
            default                 => view('panel.status.pending'),
        };
    }
}

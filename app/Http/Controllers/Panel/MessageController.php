<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use App\Models\Conversation;
use App\Services\MessagingService;
use App\Services\ScoreService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessagingService $messaging
    ) {}

    // صندوق پیام — پیام‌های دریافتی + گفتگوها
    public function index()
    {
        $member = auth('member')->user();

        // پیام‌های پخشی دریافتی
        $broadcasts = BroadcastRecipient::where('member_id', $member->id)
            ->with('broadcast.sender')
            ->latest()
            ->get();

        // گفتگوهای فعال
        $conversations = Conversation::where('member_id', $member->id)
            ->with('latestMessage')
            ->latest('last_message_at')
            ->get();

        return view('panel.messages.index', compact('member', 'broadcasts', 'conversations'));
    }

    // نمایش یک پیام پخشی
    public function showBroadcast(BroadcastRecipient $recipient)
    {
        $member = auth('member')->user();
        if ($recipient->member_id !== $member->id) abort(403);

        // علامت خوانده‌شده
        if (! $recipient->is_read) {
            $recipient->update(['is_read' => true, 'read_at' => now()]);
        }

        $recipient->load('broadcast.sender');

        // آیا برای این broadcast قبلاً گفتگو شروع شده؟
        $conversation = Conversation::where('member_id', $member->id)
            ->where('broadcast_id', $recipient->broadcast_id)
            ->first();

        return view('panel.messages.broadcast', compact('member', 'recipient', 'conversation'));
    }

    // پاسخ به یک پیام پخشی (شروع گفتگو)
    public function replyBroadcast(Request $request, BroadcastRecipient $recipient)
    {
        $member = auth('member')->user();
        if ($recipient->member_id !== $member->id) abort(403);

        $broadcast = $recipient->broadcast;
        if (! $broadcast->is_replyable) {
            return back()->with('error', 'این پیام قابل پاسخ نیست');
        }

        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        // گفتگوی موجود یا جدید
        $conversation = Conversation::where('member_id', $member->id)
            ->where('broadcast_id', $broadcast->id)
            ->first();

        if ($conversation) {
            $this->messaging->reply($conversation, 'member', $request->body);
        } else {
            $conversation = $this->messaging->startConversation(
                $member,
                'پاسخ به: ' . $broadcast->subject,
                $request->body,
                $broadcast->id
            );
            // امتیاز پاسخ به پیام تعاملی (یک‌بار)
            app(ScoreService::class)->addByKey($member, 'message_reply');
        }

        return redirect()->route('panel.messages.conversation', $conversation)
            ->with('success', 'پاسخ شما ارسال شد');
    }

    // نمایش یک گفتگو
    public function showConversation(Conversation $conversation)
    {
        $member = auth('member')->user();
        if ($conversation->member_id !== $member->id) abort(403);

        $this->messaging->markReadByMember($conversation);
        $conversation->load('messages.admin');

        return view('panel.messages.conversation', compact('member', 'conversation'));
    }

    // پاسخ در گفتگوی موجود
    public function replyConversation(Request $request, Conversation $conversation)
    {
        $member = auth('member')->user();
        if ($conversation->member_id !== $member->id) abort(403);

        if ($conversation->status === 'closed') {
            return back()->with('error', 'این گفتگو بسته شده است');
        }

        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $this->messaging->reply($conversation, 'member', $request->body);

        return back()->with('success', 'پیام ارسال شد');
    }

    // شروع گفتگوی جدید با ادمین
    public function newConversation()
    {
        return view('panel.messages.new');
    }

    public function storeConversation(Request $request)
    {
        $member = auth('member')->user();

        $request->validate([
            'subject' => ['required', 'string', 'max:100'],
            'body'    => ['required', 'string', 'max:1000'],
        ]);

        $conversation = $this->messaging->startConversation(
            $member,
            $request->subject,
            $request->body
        );

        return redirect()->route('panel.messages.conversation', $conversation)
            ->with('success', 'پیام شما به مدیریت ارسال شد');
    }
}

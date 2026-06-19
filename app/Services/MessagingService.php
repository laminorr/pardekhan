<?php

namespace App\Services;

use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    /**
     * ارسال پیام از ادمین (تکی / لایه / همه)
     */
    public function broadcast(
        string $subject,
        string $body,
        string $audienceType,   // single, layer, all
        ?int $memberId,
        ?int $layerId,
        bool $isReplyable,
        ?int $adminId
    ): Broadcast {
        return DB::transaction(function () use ($subject, $body, $audienceType, $memberId, $layerId, $isReplyable, $adminId) {
            $broadcast = Broadcast::create([
                'subject'       => $subject,
                'body'          => $body,
                'audience_type' => $audienceType,
                'member_id'     => $audienceType === 'single' ? $memberId : null,
                'layer_id'      => $audienceType === 'layer' ? $layerId : null,
                'is_replyable'  => $isReplyable,
                'sent_by'       => $adminId,
            ]);

            // تعیین گیرنده‌ها
            $members = match ($audienceType) {
                'single' => Member::where('id', $memberId)->where('status', 'approved')->get(),
                'layer'  => Member::where('layer_id', $layerId)->where('status', 'approved')->get(),
                'all'    => Member::where('status', 'approved')->get(),
            };

            // ساخت رکورد گیرنده برای هرکس
            foreach ($members as $member) {
                BroadcastRecipient::create([
                    'broadcast_id' => $broadcast->id,
                    'member_id'    => $member->id,
                ]);
            }

            return $broadcast;
        });
    }

    /**
     * مخاطب یه گفتگوی جدید با ادمین شروع می‌کند
     */
    public function startConversation(Member $member, string $subject, string $body, ?int $broadcastId = null): Conversation
    {
        return DB::transaction(function () use ($member, $subject, $body, $broadcastId) {
            $conversation = Conversation::create([
                'member_id'       => $member->id,
                'subject'         => $subject,
                'broadcast_id'    => $broadcastId,
                'status'          => 'open',
                'last_message_at' => now(),
                'admin_unread'    => true,
                'member_unread'   => false,
            ]);

            ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type'     => 'member',
                'body'            => $body,
            ]);

            return $conversation;
        });
    }

    /**
     * افزودن پیام به گفتگوی موجود
     */
    public function reply(Conversation $conversation, string $senderType, string $body, ?int $adminId = null): ConversationMessage
    {
        return DB::transaction(function () use ($conversation, $senderType, $body, $adminId) {
            $message = ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type'     => $senderType,
                'admin_id'        => $adminId,
                'body'            => $body,
            ]);

            $conversation->update([
                'last_message_at' => now(),
                'status'          => 'open',
                'admin_unread'    => $senderType === 'member',
                'member_unread'   => $senderType === 'admin',
            ]);

            // اگه ادمین جواب داد و امتیاز پاسخ تعریف شده، به مخاطب امتیاز نده
            // (امتیاز message_reply مال وقتیه که مخاطب به پیام تعاملی جواب می‌ده)

            return $message;
        });
    }

    public function markReadByMember(Conversation $conversation): void
    {
        $conversation->update(['member_unread' => false]);
    }

    public function markReadByAdmin(Conversation $conversation): void
    {
        $conversation->update(['admin_unread' => false]);
    }
}

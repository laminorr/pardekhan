<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use App\Models\Conversation;
use App\Services\MessagingService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewConversation extends Page
{
    protected static string $resource = ConversationResource::class;
    protected string $view = 'filament.resources.conversation-resource.pages.view-conversation';

    public int $convoId;
    public ?string $replyBody = '';

    public function mount(int|string $record): void
    {
        $this->convoId = (int) $record;
        $conversation = Conversation::findOrFail($this->convoId);
        app(MessagingService::class)->markReadByAdmin($conversation);
    }

    public function getConvoProperty(): Conversation
    {
        return Conversation::with('member', 'messages.admin')->findOrFail($this->convoId);
    }

    public function getTitle(): string|Htmlable
    {
        $m = $this->convo->member;
        return 'گفتگو با ' . $m->first_name . ' ' . $m->last_name;
    }

    public function sendReply(): void
    {
        $body = trim($this->replyBody);
        if (empty($body)) return;

        app(MessagingService::class)->reply($this->convo, 'admin', $body, auth()->id());

        $this->replyBody = '';

        // پاک کردن کش computed property تا پیام جدید نمایش داده شود
        unset($this->convo);

        Notification::make()->success()->title('پاسخ ارسال شد')->send();
    }

    public function toggleStatus(): void
    {
        $conv = $this->convo;
        $conv->update([
            'status' => $conv->status === 'open' ? 'closed' : 'open',
        ]);
        unset($this->convo);
    }
}

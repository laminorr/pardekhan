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

    public int $recordId;
    public ?string $replyBody = '';

    public function mount(int|string $record): void
    {
        $this->recordId = (int) $record;
        $conversation = Conversation::findOrFail($this->recordId);
        app(MessagingService::class)->markReadByAdmin($conversation);
    }

    public function getRecordProperty(): Conversation
    {
        return Conversation::with('member', 'messages.admin')->findOrFail($this->recordId);
    }

    public function getTitle(): string|Htmlable
    {
        $m = $this->record->member;
        return 'گفتگو با ' . $m->first_name . ' ' . $m->last_name;
    }

    public function sendReply(): void
    {
        $body = trim($this->replyBody);
        if (empty($body)) return;

        app(MessagingService::class)->reply($this->record, 'admin', $body, auth()->id());

        $this->replyBody = '';

        Notification::make()->success()->title('پاسخ ارسال شد')->send();
    }

    public function toggleStatus(): void
    {
        $conv = $this->record;
        $conv->update([
            'status' => $conv->status === 'open' ? 'closed' : 'open',
        ]);
    }
}

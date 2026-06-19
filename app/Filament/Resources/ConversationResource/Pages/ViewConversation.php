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

    public Conversation $record;
    public ?string $replyBody = '';

    public function mount($record): void
    {
        $this->record = Conversation::with('member', 'messages')->findOrFail($record);
        // علامت‌گذاری خوانده‌شده توسط ادمین
        app(MessagingService::class)->markReadByAdmin($this->record);
    }

    public function getTitle(): string|Htmlable
    {
        return 'گفتگو با ' . $this->record->member->first_name . ' ' . $this->record->member->last_name;
    }

    public function sendReply(): void
    {
        $body = trim($this->replyBody);
        if (empty($body)) return;

        app(MessagingService::class)->reply($this->record, 'admin', $body, auth()->id());

        $this->replyBody = '';
        $this->record->refresh();
        $this->record->load('messages');

        Notification::make()->success()->title('پاسخ ارسال شد')->send();
    }

    public function toggleStatus(): void
    {
        $this->record->update([
            'status' => $this->record->status === 'open' ? 'closed' : 'open',
        ]);
        $this->record->refresh();
    }
}

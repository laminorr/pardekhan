<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Services\RegistrationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // برگزاری با وجود نرسیدن به حد نصاب
            Action::make('hold_anyway')
                ->label('برگزار می‌شود')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->status === 'needs_decision')
                ->requiresConfirmation()
                ->modalHeading('برگزاری دورهمی')
                ->modalDescription('این دورهمی با وجود نرسیدن به حد نصاب برگزار می‌شود.')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()->success()->title('دورهمی برگزار می‌شود')->send();
                    $this->refreshFormData(['status']);
                }),

            // لغو دورهمی + بازگشت وجه
            Action::make('cancel_event')
                ->label('لغو دورهمی')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => in_array($this->record->status, ['needs_decision', 'active', 'full', 'closed', 'draft']))
                ->requiresConfirmation()
                ->modalHeading('لغو دورهمی')
                ->modalDescription('وجه پرداختی همه شرکت‌کنندگان به کیف پولشان بازگردانده می‌شود.')
                ->modalSubmitActionLabel('بله، لغو کن')
                ->action(function () {
                    $result = app(RegistrationService::class)->cancelEvent($this->record);
                    Notification::make()
                        ->success()
                        ->title('دورهمی لغو شد')
                        ->body("وجه {$result['refunded']} نفر بازگردانده شد")
                        ->send();
                    $this->refreshFormData(['status']);
                }),

            DeleteAction::make()->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

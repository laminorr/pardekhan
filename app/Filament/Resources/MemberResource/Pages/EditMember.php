<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Layer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // دکمه تایید
            Action::make('approve')
                ->label('تایید عضو')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => ! in_array($this->record->status, ['approved']))
                ->requiresConfirmation()
                ->modalHeading('تایید عضو')
                ->modalDescription('آیا از تایید این عضو مطمئن هستید؟')
                ->modalSubmitActionLabel('بله، تایید کن')
                ->action(function () {
                    $layer = Layer::active()->orderBy('min_score')->first();
                    $this->record->update([
                        'status'   => 'approved',
                        'layer_id' => $layer?->id,
                    ]);
                    Notification::make()
                        ->success()
                        ->title('عضو با موفقیت تایید شد')
                        ->send();
                    $this->refreshFormData(['status', 'layer_id']);
                }),

            // دکمه نیاز به اطلاعات بیشتر
            Action::make('needs_info')
                ->label('درخواست اطلاعات بیشتر')
                ->color('warning')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->visible(fn () => in_array($this->record->status, ['pending_review']))
                ->form([
                    \Filament\Forms\Components\Textarea::make('message')
                        ->label('سوال یا توضیح برای کاربر')
                        ->required()
                        ->rows(4)
                        ->placeholder('سوال خود را بنویسید...'),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'needs_more_info']);
                    $this->record->messages()->create([
                        'type'    => 'interactive',
                        'subject' => 'نیاز به اطلاعات بیشتر',
                        'body'    => $data['message'],
                    ]);
                    Notification::make()
                        ->warning()
                        ->title('پیام برای کاربر ارسال شد')
                        ->send();
                    $this->refreshFormData(['status']);
                }),

            // دکمه رد
            Action::make('reject')
                ->label('رد درخواست')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => ! in_array($this->record->status, ['rejected']))
                ->requiresConfirmation()
                ->modalHeading('رد درخواست عضویت')
                ->modalDescription('این عضو دیگر نمی‌تواند با همین شماره ثبت‌نام کند.')
                ->modalSubmitActionLabel('بله، رد کن')
                ->action(function () {
                    $this->record->update(['status' => 'rejected']);
                    Notification::make()
                        ->danger()
                        ->title('درخواست رد شد')
                        ->send();
                    $this->refreshFormData(['status']);
                }),

            DeleteAction::make()->label('حذف کامل'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

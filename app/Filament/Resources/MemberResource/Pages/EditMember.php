<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Layer;
use App\Services\ScoreService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    /**
     * بعد از ذخیره فرم، لایه را با امتیاز همگام کن
     */
    protected function afterSave(): void
    {
        $member = $this->record;
        if ($member->status !== 'approved') return;

        $correct = Layer::active()
            ->where('min_score', '<=', $member->score)
            ->orderByDesc('min_score')
            ->first();

        if ($correct && $member->layer_id !== $correct->id) {
            $member->update(['layer_id' => $correct->id]);
            $this->refreshFormData(['layer_id']);
            Notification::make()
                ->success()
                ->title("لایه به‌روز شد: {$correct->name}")
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // تایید عضو
            Action::make('approve')
                ->label('تایید عضو')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->status !== 'approved')
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
                    Notification::make()->success()->title('عضو با موفقیت تایید شد')->send();
                    $this->refreshFormData(['status', 'layer_id']);
                }),

            // درخواست اطلاعات بیشتر
            Action::make('needs_info')
                ->label('درخواست اطلاعات بیشتر')
                ->color('warning')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->visible(fn () => $this->record->status === 'pending_review')
                ->form([
                    \Filament\Forms\Components\Textarea::make('message')
                        ->label('سوال یا توضیح برای کاربر')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'needs_more_info']);
                    $this->record->messages()->create([
                        'type'    => 'interactive',
                        'subject' => 'نیاز به اطلاعات بیشتر',
                        'body'    => $data['message'],
                    ]);
                    Notification::make()->warning()->title('پیام برای کاربر ارسال شد')->send();
                    $this->refreshFormData(['status']);
                }),

            // رد درخواست
            Action::make('reject')
                ->label('رد درخواست')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => $this->record->status !== 'rejected')
                ->requiresConfirmation()
                ->modalHeading('رد درخواست عضویت')
                ->modalDescription('این عضو دیگر نمی‌تواند با همین شماره ثبت‌نام کند.')
                ->modalSubmitActionLabel('بله، رد کن')
                ->action(function () {
                    $this->record->update(['status' => 'rejected']);
                    Notification::make()->danger()->title('درخواست رد شد')->send();
                    $this->refreshFormData(['status']);
                }),

            // تغییر امتیاز دستی
            Action::make('adjust_score')
                ->label('تغییر امتیاز')
                ->color('info')
                ->icon('heroicon-o-star')
                ->form([
                    \Filament\Forms\Components\TextInput::make('points')
                        ->label('امتیاز (مثبت یا منفی)')
                        ->numeric()
                        ->required()
                        ->helperText('مثلاً 10 برای افزایش یا -10 برای کاهش'),
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('دلیل')
                        ->required()
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    app(ScoreService::class)->addManual(
                        $this->record,
                        (int) $data['points'],
                        'تنظیم دستی: ' . $data['reason'],
                        auth()->id(),
                        $data['reason'],
                    );
                    Notification::make()->success()->title('امتیاز تغییر کرد')->send();
                    $this->refreshFormData(['score', 'layer_id']);
                }),

            // تایید عکس پروفایل
            Action::make('approve_avatar')
                ->label('تایید عکس')
                ->color('success')
                ->icon('heroicon-o-photo')
                ->visible(fn () => $this->record->avatar && ! $this->record->avatar_approved)
                ->action(function () {
                    $this->record->update(['avatar_approved' => true]);
                    Notification::make()->success()->title('عکس تایید شد')->send();
                    $this->refreshFormData(['avatar_approved']);
                }),

            DeleteAction::make()->label('حذف کامل'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

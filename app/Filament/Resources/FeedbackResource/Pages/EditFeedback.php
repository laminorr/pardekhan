<?php
namespace App\Filament\Resources\FeedbackResource\Pages;
use App\Filament\Resources\FeedbackResource;
use Filament\Resources\Pages\EditRecord;
class EditFeedback extends EditRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['admin_reply'])) {
            $data['replied_at'] = now();
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

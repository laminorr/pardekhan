<?php

namespace App\Filament\Resources\TheaterReviewResource\Pages;

use App\Filament\Resources\TheaterReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTheaterReview extends EditRecord
{
    protected static string $resource = TheaterReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['cover'] = TheaterReviewResource\Concerns\CompressesCover::run($data['cover'] ?? null);
        return $data;
    }
}

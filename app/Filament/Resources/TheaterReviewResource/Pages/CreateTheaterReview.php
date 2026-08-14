<?php

namespace App\Filament\Resources\TheaterReviewResource\Pages;

use App\Filament\Resources\TheaterReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTheaterReview extends CreateRecord
{
    protected static string $resource = TheaterReviewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['cover'] = TheaterReviewResource\Concerns\CompressesCover::run($data['cover'] ?? null);
        return $data;
    }
}

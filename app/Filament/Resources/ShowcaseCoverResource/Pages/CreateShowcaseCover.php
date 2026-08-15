<?php

namespace App\Filament\Resources\ShowcaseCoverResource\Pages;

use App\Filament\Resources\ShowcaseCoverResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShowcaseCover extends CreateRecord
{
    protected static string $resource = ShowcaseCoverResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['image'] = ShowcaseCoverResource\Concerns\CompressesCover::run($data['image'] ?? null);
        return $data;
    }
}

<?php

namespace App\Filament\Resources\ShowcaseCoverResource\Pages;

use App\Filament\Resources\ShowcaseCoverResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShowcaseCover extends EditRecord
{
    protected static string $resource = ShowcaseCoverResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['image'] = ShowcaseCoverResource\Concerns\CompressesCover::run($data['image'] ?? null);
        return $data;
    }
}

<?php

namespace App\Filament\Resources\ScoreSettingResource\Pages;

use App\Filament\Resources\ScoreSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScoreSettings extends ListRecords
{
    protected static string $resource = ScoreSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('رفتار جدید')];
    }
}

<?php

namespace App\Filament\Resources\ScoreSettingResource\Pages;

use App\Filament\Resources\ScoreSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScoreSetting extends CreateRecord
{
    protected static string $resource = ScoreSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\ScoreSettingResource\Pages;

use App\Filament\Resources\ScoreSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditScoreSetting extends EditRecord
{
    protected static string $resource = ScoreSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

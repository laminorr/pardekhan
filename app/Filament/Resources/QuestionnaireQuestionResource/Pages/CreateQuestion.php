<?php

namespace App\Filament\Resources\QuestionnaireQuestionResource\Pages;

use App\Filament\Resources\QuestionnaireQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionnaireQuestionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

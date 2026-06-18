<?php

namespace App\Filament\Resources\QuestionnaireQuestionResource\Pages;

use App\Filament\Resources\QuestionnaireQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionnaireQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('سوال جدید')];
    }
}

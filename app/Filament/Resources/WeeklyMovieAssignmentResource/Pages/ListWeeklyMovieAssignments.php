<?php

namespace App\Filament\Resources\WeeklyMovieAssignmentResource\Pages;

use App\Filament\Resources\WeeklyMovieAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeeklyMovieAssignments extends ListRecords
{
    protected static string $resource = WeeklyMovieAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('تخصیص جدید'),
        ];
    }
}

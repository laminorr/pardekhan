<?php

namespace App\Filament\Resources\ShowcaseCoverResource\Pages;

use App\Filament\Resources\ShowcaseCoverResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShowcaseCovers extends ListRecords
{
    protected static string $resource = ShowcaseCoverResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('کاور جدید')];
    }
}

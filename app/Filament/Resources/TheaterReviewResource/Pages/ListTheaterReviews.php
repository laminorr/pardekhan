<?php

namespace App\Filament\Resources\TheaterReviewResource\Pages;

use App\Filament\Resources\TheaterReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTheaterReviews extends ListRecords
{
    protected static string $resource = TheaterReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

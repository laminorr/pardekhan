<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEpisode extends EditRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view_page')
                ->label('مشاهده صفحه')
                ->icon('heroicon-o-eye')
                ->url(fn () => url($this->record->slug))
                ->openUrlInNewTab(),
        ];
    }
}

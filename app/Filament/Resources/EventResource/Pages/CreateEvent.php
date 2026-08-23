<?php
namespace App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;
class CreateEvent extends CreateRecord
{
    use InteractsWithLayerPrices;

    protected static string $resource = EventResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractLayerPrices($data);
    }

    protected function afterCreate(): void
    {
        $this->syncLayerPrices();
    }
}

<?php

namespace App\Filament\Resources\FilmResource\Pages;

use App\Filament\Resources\FilmResource;
use App\Models\DailyFilm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListFilms extends ListRecords
{
    protected static string $resource = FilmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('فیلم جدید'),

            Action::make('import_json')
                ->label('ایمپورت از JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->form([
                    Forms\Components\Textarea::make('json')
                        ->label('داده JSON')
                        ->rows(12)
                        ->required()
                        ->helperText('آرایه‌ای از فیلم‌ها. هر فیلم می‌تواند این کلیدها را داشته باشد: title، original_title، year، director، genre، cover_url، description، link، show_date (مثل 2026-06-23)، is_active'),
                ])
                ->action(function (array $data) {
                    $items = json_decode($data['json'], true);

                    if (! is_array($items)) {
                        Notification::make()->danger()->title('JSON نامعتبر است')->send();
                        return;
                    }

                    // اگر یک شیء واحد بود، در آرایه بپیچ
                    if (isset($items['title'])) {
                        $items = [$items];
                    }

                    $count = 0;
                    foreach ($items as $item) {
                        if (empty($item['title'])) continue;

                        DailyFilm::create([
                            'title'          => $item['title'],
                            'original_title' => $item['original_title'] ?? null,
                            'year'           => $item['year'] ?? null,
                            'director'       => $item['director'] ?? null,
                            'genre'          => $item['genre'] ?? null,
                            'cover_url'      => $item['cover_url'] ?? null,
                            'description'    => $item['description'] ?? null,
                            'link'           => $item['link'] ?? null,
                            'show_date'      => $item['show_date'] ?? now()->toDateString(),
                            'is_active'      => $item['is_active'] ?? true,
                        ]);
                        $count++;
                    }

                    Notification::make()
                        ->success()
                        ->title("$count فیلم وارد شد")
                        ->send();
                }),
        ];
    }
}

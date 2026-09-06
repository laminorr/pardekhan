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
                        ->helperText('آرایه‌ای از فیلم‌ها. کلیدها: title (فارسی)، original_title (انگلیسی) — حداقل یکی لازم است، year، director، genre، cover_url، description، imdb_url، filimo_url، link، is_active'),
                ])
                ->action(function (array $data) {
                    $items = json_decode($data['json'], true);

                    if (! is_array($items)) {
                        Notification::make()->danger()->title('JSON نامعتبر است')->send();
                        return;
                    }

                    // اگر یک شیء واحد بود (آرایه‌ی لیست‌مانند نبود)، در آرایه بپیچ
                    if (! array_is_list($items)) {
                        $items = [$items];
                    }

                    $count = 0;
                    $skipped = 0;
                    foreach ($items as $item) {
                        if (! is_array($item)) continue;

                        $title         = $item['title'] ?? null;
                        $originalTitle = $item['original_title'] ?? null;

                        // حداقل یکی از title یا original_title لازم است
                        if (empty($title) && empty($originalTitle)) continue;

                        $year = isset($item['year']) && $item['year'] !== null && $item['year'] !== ''
                            ? (int) $item['year']
                            : null;

                        // حذف تکراری: اگر فیلمی با همان (title یا original_title) و همان year موجود بود
                        $duplicate = DailyFilm::query()
                            ->where(function ($q) use ($title, $originalTitle) {
                                if (! empty($title)) {
                                    $q->orWhere('title', $title)
                                      ->orWhere('original_title', $title);
                                }
                                if (! empty($originalTitle)) {
                                    $q->orWhere('title', $originalTitle)
                                      ->orWhere('original_title', $originalTitle);
                                }
                            })
                            ->where('year', $year)
                            ->exists();

                        if ($duplicate) {
                            $skipped++;
                            continue;
                        }

                        DailyFilm::create([
                            'title'          => $title,
                            'original_title' => $originalTitle,
                            'year'           => $year,
                            'director'       => $item['director'] ?? null,
                            'genre'          => $item['genre'] ?? null,
                            'cover_url'      => $item['cover_url'] ?? null,
                            'description'    => $item['description'] ?? null,
                            'imdb_url'       => $item['imdb_url'] ?? null,
                            'filimo_url'     => $item['filimo_url'] ?? null,
                            'link'           => $item['link'] ?? null,
                            'is_active'      => $item['is_active'] ?? true,
                        ]);
                        $count++;
                    }

                    $title = "$count فیلم وارد شد";
                    if ($skipped > 0) {
                        $title .= " ($skipped مورد تکراری نادیده گرفته شد)";
                    }

                    Notification::make()
                        ->success()
                        ->title($title)
                        ->send();
                }),
        ];
    }
}

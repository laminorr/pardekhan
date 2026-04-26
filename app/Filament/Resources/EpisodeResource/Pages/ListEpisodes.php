<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use App\Models\Episode;
use App\Models\Theme;
use App\Models\Lesson;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListEpisodes extends ListRecords
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('import')
                ->label('ایمپورت JSON')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
->form([
    Forms\Components\FileUpload::make('json_file')
        ->label('فایل JSON')
        ->acceptedFileTypes(['application/json'])
        ->required()
        ->disk('public')
        ->directory('imports')
        ->helperText('فایل JSON خروجی Claude رو اینجا آپلود کنید'),
])
                ->action(function (array $data) {
                    $path = storage_path('app/public/' . $data['json_file']);
                    $json = json_decode(file_get_contents($path), true);

                    if (!$json) {
                        \Filament\Notifications\Notification::make()
                            ->title('خطا')
                            ->body('فایل JSON معتبر نیست')
                            ->danger()
                            ->send();
                        return;
                    }

                    DB::transaction(function () use ($json) {
                        $episode = Episode::updateOrCreate(
                            ['slug' => $json['slug']],
                            collect($json)->except(['themes', 'lessons'])->toArray() + [
                                'is_published' => $json['is_published'] ?? true,
                                'published_at' => $json['published_at'] ?? now(),
                            ]
                        );

                        if (isset($json['themes'])) {
                            $episode->themes()->delete();
                            foreach ($json['themes'] as $i => $t) {
                                $episode->themes()->create(array_merge($t, ['sort_order' => $i]));
                            }
                        }

                        if (isset($json['lessons'])) {
                            $episode->lessons()->delete();
                            foreach ($json['lessons'] as $i => $l) {
                                $episode->lessons()->create(array_merge($l, ['sort_order' => $i]));
                            }
                        }
                    });

                    \Filament\Notifications\Notification::make()
                        ->title('موفق!')
                        ->body('اپیزود «' . $json['title_fa'] . '» با موفقیت ایمپورت شد')
                        ->success()
                        ->send();
                }),
        ];
    }
}
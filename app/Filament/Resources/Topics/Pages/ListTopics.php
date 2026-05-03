<?php

namespace App\Filament\Resources\Topics\Pages;

use App\Filament\Resources\Topics\TopicResource;
use App\Models\Topic;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListTopics extends ListRecords
{
    protected static string $resource = TopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importTopicJson')
                ->label('ورود JSON پرونده')
                ->color('gray')
                ->modalHeading('ورود JSON پرونده موضوعی')
                ->modalDescription('یک فایل JSON معتبر انتخاب کن تا پرونده موضوعی ساخته یا به‌روزرسانی شود.')
                ->modalSubmitActionLabel('ایمپورت پرونده')
                ->form([
                    FileUpload::make('json_file')
                        ->label('فایل JSON')
                        ->disk('local')
                        ->directory('imports/topics')
                        ->required()
                        ->helperText('فایل باید یک آبجکت JSON معتبر شامل slug، title و hero_title باشد.'),
                ])
                ->action(function (array $data): void {
                    try {
                        $path = $data['json_file'] ?? null;

                        if (is_array($path)) {
                            $path = reset($path);
                        }

                        if (! $path || ! Storage::disk('local')->exists($path)) {
                            Notification::make()
                                ->title('فایل پیدا نشد')
                                ->body('فایل JSON آپلود شده قابل خواندن نیست.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $content = Storage::disk('local')->get($path);
                        $payload = json_decode($content, true);

                        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($payload)) {
                            Notification::make()
                                ->title('JSON نامعتبر است')
                                ->body('ساختار فایل JSON درست نیست.')
                                ->danger()
                                ->send();

                            return;
                        }

                        foreach (['slug', 'title', 'hero_title'] as $field) {
                            if (empty($payload[$field])) {
                                Notification::make()
                                    ->title('فیلد ضروری ناقص است')
                                    ->body("فیلد {$field} در JSON وجود ندارد یا خالی است.")
                                    ->danger()
                                    ->send();

                                return;
                            }
                        }

                        $isPublished = (bool) ($payload['is_published'] ?? false);

                        Topic::updateOrCreate(
                            [
                                'slug' => (string) $payload['slug'],
                            ],
                            [
                                'title' => (string) $payload['title'],
                                'seo_title' => $payload['seo_title'] ?? null,
                                'seo_description' => $payload['seo_description'] ?? null,

                                'hero_kicker' => $payload['hero_kicker'] ?? 'پرونده موضوعی پرده‌خوان',
                                'hero_title' => (string) $payload['hero_title'],
                                'hero_lead' => $payload['hero_lead'] ?? null,

                                'key_concepts' => array_values($payload['key_concepts'] ?? []),
                                'related_tags' => array_values($payload['related_tags'] ?? []),
                                'featured_episode_slugs' => array_values($payload['featured_episode_slugs'] ?? []),
                                'sections' => array_values($payload['sections'] ?? []),
                                'faq' => array_values($payload['faq'] ?? []),

                                'is_published' => $isPublished,
                                'sort_order' => (int) ($payload['sort_order'] ?? 0),
                                'published_at' => $payload['published_at'] ?? ($isPublished ? now() : null),
                            ]
                        );

                        Notification::make()
                            ->title('پرونده ایمپورت شد')
                            ->body('پرونده موضوعی با موفقیت ساخته یا به‌روزرسانی شد.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('خطا در ایمپورت JSON')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->label('ایجاد پرونده موضوعی'),
        ];
    }
}
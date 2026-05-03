<?php

namespace App\Filament\Resources\Topics\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان پرونده')
                    ->required()
                    ->maxLength(200),

                TextInput::make('slug')
                    ->label('اسلاگ انگلیسی')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(160)
                    ->helperText('مثلاً: trauma-in-cinema'),

                TextInput::make('seo_title')
                    ->label('عنوان سئو')
                    ->maxLength(200),

                Textarea::make('seo_description')
                    ->label('توضیح سئو')
                    ->rows(3)
                    ->maxLength(300)
                    ->columnSpanFull(),

                TextInput::make('hero_kicker')
                    ->label('لیبل بالای هیرو')
                    ->maxLength(160)
                    ->default('پرونده موضوعی پرده‌خوان'),

                TextInput::make('hero_title')
                    ->label('عنوان هیرو')
                    ->required()
                    ->maxLength(200),

                Textarea::make('hero_lead')
                    ->label('متن لید هیرو')
                    ->rows(4)
                    ->columnSpanFull(),

                TagsInput::make('key_concepts')
                    ->label('مفاهیم کلیدی')
                    ->placeholder('مثلاً: روان‌زخم')
                    ->helperText('هر مفهوم را جداگانه وارد کن.')
                    ->columnSpanFull(),

                TagsInput::make('related_tags')
                    ->label('تگ‌های مرتبط')
                    ->placeholder('مثلاً: تروما')
                    ->helperText('این تگ‌ها برای پیدا کردن اپیزودهای مرتبط استفاده می‌شوند.')
                    ->columnSpanFull(),

                TagsInput::make('featured_episode_slugs')
                    ->label('اسلاگ اپیزودهای ویژه')
                    ->placeholder('مثلاً: froshndh')
                    ->helperText('اسلاگ اپیزودهایی که می‌خواهی حتماً در این پرونده نمایش داده شوند.')
                    ->columnSpanFull(),

                Repeater::make('sections')
                    ->label('بخش‌های محتوایی پرونده')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان بخش')
                            ->required()
                            ->maxLength(200),

                        Textarea::make('body')
                            ->label('متن بخش')
                            ->rows(6)
                            ->required(),
                    ])
                    ->addActionLabel('افزودن بخش')
                    ->columnSpanFull(),

                Repeater::make('faq')
                    ->label('پرسش‌های رایج')
                    ->schema([
                        TextInput::make('question')
                            ->label('سؤال')
                            ->required()
                            ->maxLength(200),

                        Textarea::make('answer')
                            ->label('پاسخ')
                            ->rows(4)
                            ->required(),
                    ])
                    ->addActionLabel('افزودن پرسش')
                    ->columnSpanFull(),

                Toggle::make('is_published')
                    ->label('منتشر شود؟')
                    ->default(false)
                    ->required(),

                TextInput::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->required()
                    ->numeric()
                    ->default(0),

                DateTimePicker::make('published_at')
                    ->label('زمان انتشار'),
            ]);
    }
}

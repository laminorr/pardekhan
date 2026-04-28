<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationLabel = 'اپیزودها';
    protected static ?string $modelLabel = 'اپیزود';
    protected static ?string $pluralModelLabel = 'اپیزودها';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            \Filament\Schemas\Components\Tabs::make('episode')->tabs([

                // Tab 1: Film Info
                \Filament\Schemas\Components\Tabs\Tab::make('اطلاعات فیلم')
                    ->icon('heroicon-o-film')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('title_fa')
                                ->label('عنوان فارسی فیلم')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                            Forms\Components\TextInput::make('title_en')
                                ->label('عنوان انگلیسی فیلم')
                                ->required(),
                        ]),
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('slug')
                                ->label('اسلاگ URL')
                                ->required()
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('episode_number')
                                ->label('شماره اپیزود')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('year')
                                ->label('سال ساخت')
                                ->numeric()
                                ->required(),
                        ]),
                        \Filament\Schemas\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('director')
                                ->label('کارگردان')
                                ->required(),
                            Forms\Components\TextInput::make('imdb_url')
                                ->label('لینک IMDB')
                                ->url(),
                        ]),
Forms\Components\TextInput::make('aparat_hash')
                            ->label('کد ویدیو آپارات')
                            ->helperText('فقط کد hash — مثلاً: sbhiazo'),
Forms\Components\FileUpload::make('cover_image')
    ->label('تصویر کاور')
    ->image()
    ->disk('public')
    ->directory('covers')
    ->helperText('تصویر عمودی با نسبت 3:4 — پیشنهاد: 600x800 پیکسل'),
                    ]),

                // Tab 2: Hero
                \Filament\Schemas\Components\Tabs\Tab::make('هیرو')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Forms\Components\TextInput::make('hero_title_html')
                            ->label('تیتر اصلی (HTML مجاز)')
                            ->required()
                            ->helperText('میتونی از <span class="hi"> و <span class="red"> استفاده کنی'),
                        Forms\Components\Textarea::make('hero_lead')
                            ->label('متن مقدمه هیرو')
                            ->rows(4)
                            ->required(),
                    ]),

                // Tab 3: Essay
                \Filament\Schemas\Components\Tabs\Tab::make('مقدمه')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('essay_title_html')
                            ->label('تیتر بخش مقدمه (HTML مجاز)')
                            ->required(),
                        Forms\Components\Repeater::make('essay_paragraphs')
                            ->label('پاراگراف‌های مقدمه')
                            ->simple(
                                Forms\Components\Textarea::make('text')
                                    ->label('پاراگراف')
                                    ->rows(3)
                                    ->required()
                            )
                            ->addActionLabel('افزودن پاراگراف')
                            ->reorderable()
                            ->required(),
                        Forms\Components\Textarea::make('opening_quote_text')
                            ->label('نقل‌قول افتتاحیه')
                            ->rows(3)
                            ->required(),
                        Forms\Components\TextInput::make('opening_quote_cite')
                            ->label('منبع نقل‌قول')
                            ->required(),
                        Forms\Components\Repeater::make('essay_after_paragraphs')
                            ->label('پاراگراف‌های بعد از نقل‌قول')
                            ->simple(
                                Forms\Components\Textarea::make('text')
                                    ->label('پاراگراف')
                                    ->rows(3)
                            )
                            ->addActionLabel('افزودن پاراگراف')
                            ->reorderable(),
                    ]),

                // Tab 4: Themes
                \Filament\Schemas\Components\Tabs\Tab::make('محورهای تحلیل')
                    ->icon('heroicon-o-light-bulb')
                    ->schema([
                        Forms\Components\Repeater::make('themes')
                            ->label('محورها')
                            ->relationship()
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('number_label')
                                        ->label('شماره')
                                        ->default('۰۱')
                                        ->required(),
                                    Forms\Components\TextInput::make('title')
                                        ->label('عنوان')
                                        ->required()
                                        ->columnSpan(2),
                                ]),
                                Forms\Components\TextInput::make('approach')
                                    ->label('رویکرد و نظریه‌پرداز')
                                    ->placeholder('مثل: رویکرد روان‌پویشی · کرنبرگ')
                                    ->required(),
                                Forms\Components\Textarea::make('paragraph')
                                    ->label('متن تحلیل')
                                    ->rows(4)
                                    ->required(),
                                Forms\Components\Textarea::make('quote')
                                    ->label('نقل‌قول از فیلم')
                                    ->rows(2)
                                    ->required(),
                                \Filament\Schemas\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('reference_fa')
                                        ->label('ارجاع فارسی')
                                        ->required(),
                                    Forms\Components\TextInput::make('reference_en')
                                        ->label('ارجاع انگلیسی')
                                        ->required(),
                                ]),
                                Forms\Components\Textarea::make('simple_explanation')
                                    ->label('به زبان ساده')
                                    ->rows(2)
                                    ->required(),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->orderColumn('sort_order')
                            ->addActionLabel('افزودن محور')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => ($state['number_label'] ?? '') . ' — ' . ($state['title'] ?? '')),
                    ]),

                // Tab 5: Big Quote
                \Filament\Schemas\Components\Tabs\Tab::make('نقل‌قول بزرگ')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->schema([
                        Forms\Components\TextInput::make('big_quote_highlight')
                            ->label('بخش هایلایت')
                            ->placeholder('آدم‌ها رو ببینید.')
                            ->required(),
                        Forms\Components\TextInput::make('big_quote_rest')
                            ->label('ادامه نقل‌قول')
                            ->placeholder('قبل از اینکه خیلی دیر بشه.')
                            ->required(),
                        Forms\Components\TextInput::make('big_quote_source')
                            ->label('منبع')
                            ->required(),
                    ]),

                // Tab 6: Lessons
                \Filament\Schemas\Components\Tabs\Tab::make('درس‌های بالینی')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Repeater::make('lessons')
                            ->label('درس‌ها')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('عنوان درس')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->label('توضیح')
                                    ->rows(3)
                                    ->required(),
                                Forms\Components\Textarea::make('example')
                                    ->label('مثال مکالمه')
                                    ->rows(2)
                                    ->required(),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->orderColumn('sort_order')
                            ->addActionLabel('افزودن درس')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? ''),
                    ]),

                // Tab 7: Meta
                \Filament\Schemas\Components\Tabs\Tab::make('متادیتا')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('meta_duration')
                                ->label('مدت پادکست')
                                ->placeholder('45:23'),
                            Forms\Components\TextInput::make('meta_approaches_count')
                                ->label('تعداد رویکردها')
                                ->numeric(),
                            Forms\Components\TextInput::make('meta_references_count')
                                ->label('تعداد ارجاعات')
                                ->numeric(),
                        ]),
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('meta_quotes_count')
                                ->label('تعداد نقل‌قول')
                                ->numeric(),
                            Forms\Components\Select::make('meta_level')
                                ->label('سطح')
                                ->options([
                                    'مقدماتی' => 'مقدماتی',
                                    'متوسط' => 'متوسط',
                                    'پیشرفته' => 'پیشرفته',
                                ]),
                            Forms\Components\TagsInput::make('meta_tags')
                                ->label('تگ‌ها'),
                        ]),
                    ]),

                // Tab 8: Next Episode
                \Filament\Schemas\Components\Tabs\Tab::make('قسمت بعدی')
                    ->icon('heroicon-o-arrow-left')
                    ->schema([
                        Forms\Components\TextInput::make('next_episode_number')
                            ->label('شماره قسمت بعدی')
                            ->placeholder('۰۲'),
                        Forms\Components\TextInput::make('next_episode_title')
                            ->label('عنوان فیلم بعدی')
                            ->placeholder('Anatomy of a Fall'),
                        Forms\Components\TextInput::make('next_episode_subtitle')
                            ->label('زیرنویس')
                            ->placeholder('وقتی حقیقت روان‌شناختی با حقیقت قضایی تقابل می‌کند'),
                    ]),

                // Tab 9: SEO
                \Filament\Schemas\Components\Tabs\Tab::make('سئو')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('عنوان سئو')
                            ->helperText('اگه خالی باشه از عنوان فیلم استفاده میشه'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('توضیح سئو')
                            ->rows(3)
                            ->helperText('حداکثر ۱۶۰ کاراکتر'),
                        Forms\Components\FileUpload::make('og_image')
                            ->label('تصویر شبکه‌های اجتماعی')
                            ->image()
                            ->disk('public')  
                            ->directory('og-images'),
                    ]),

                // Tab 10: Publish
                \Filament\Schemas\Components\Tabs\Tab::make('انتشار')
                    ->icon('heroicon-o-rocket-launch')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('منتشر شده')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('تاریخ انتشار')
                            ->default(now()),
                    ]),

            ])->columnSpanFull(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('episode_number')
                    ->label('شماره')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('title_fa')
                    ->label('فیلم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title_en')
                    ->label('عنوان انگلیسی')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('سال')
                    ->sortable(),
                Tables\Columns\TextColumn::make('themes_count')
                    ->label('محورها')
                    ->counts('themes')
                    ->badge(),
                Tables\Columns\TextColumn::make('lessons_count')
                    ->label('درس‌ها')
                    ->counts('lessons')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('منتشر')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاریخ')
->formatStateUsing(function ($state) {
    $date = \Morilog\Jalali\Jalalian::fromDateTime($state)->format('Y/m/d');

    return strtr($date, [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹',
    ]);
})
                    ->sortable(),
            ])
            ->defaultSort('episode_number', 'desc')
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_published')
                    ->label('وضعیت انتشار'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('view_page')
                    ->label('مشاهده')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Episode $record): string => url($record->slug))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }
}

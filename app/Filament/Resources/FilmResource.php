<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FilmResource\Pages;
use App\Models\DailyFilm;
use App\Models\WeeklyMovieAssignment;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FilmResource extends Resource
{
    protected static ?string $model = DailyFilm::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationLabel = 'فیلم هفته';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'فیلم';
    protected static ?string $pluralModelLabel = 'کتابخانهٔ فیلم‌ها';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('عنوان فارسی')
                // حداقل یکی از عنوان فارسی یا انگلیسی لازم است
                ->rule('required_without:original_title')
                ->maxLength(255)
                ->helperText('عنوان فارسی یا انگلیسی — حداقل یکی لازم است'),
            Forms\Components\TextInput::make('original_title')
                ->label('عنوان اصلی (انگلیسی)')
                ->rule('required_without:title')
                ->maxLength(255)
                ->helperText('عنوان فارسی یا انگلیسی — حداقل یکی لازم است'),
            Forms\Components\TextInput::make('year')
                ->label('سال ساخت')
                ->maxLength(10),
            Forms\Components\TextInput::make('director')
                ->label('کارگردان')
                ->maxLength(255),
            Forms\Components\TextInput::make('genre')
                ->label('ژانر')
                ->maxLength(255),
            Forms\Components\FileUpload::make('cover')
                ->label('کاور (آپلود)')
                ->image()
                ->disk('public')
                ->directory('films')
                ->helperText('یا به‌جای آپلود، لینک عکس را در فیلد زیر بگذارید'),
            Forms\Components\TextInput::make('cover_url')
                ->label('لینک عکس کاور')
                ->url()
                ->maxLength(500),
            Forms\Components\Textarea::make('description')
                ->label('خلاصهٔ داستان')
                ->rows(5)
                ->maxLength(3000),
            Forms\Components\TextInput::make('link')
                ->label('لینک (تریلر / تماشا)')
                ->url()
                ->maxLength(500),
            Forms\Components\TextInput::make('imdb_url')
                ->label('لینک IMDb')
                ->url()
                ->helperText('مثل imdb.com/title/...'),
            Forms\Components\TextInput::make('filimo_url')
                ->label('لینک فیلیمو')
                ->url()
                ->helperText('مثل filimo.com/m/...'),
            Forms\Components\Toggle::make('is_active')
                ->label('فعال (قابل انتخاب برای فیلم هفته)')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('پوستر')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان فارسی')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_title')
                    ->label('عنوان انگلیسی')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('سال')
                    ->searchable()
                    ->sortable(),
                // «دفعات نمایش» (times_used) — از withCount برای پرهیز از N+1
                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('دفعات نمایش')
                    ->sortable(),
                // «آخرین هفتهٔ نمایش» (last_used_at) — از subquery
                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('آخرین هفتهٔ نمایش')
                    ->formatStateUsing(fn ($state) => $state ? pdate($state, 'Y/m/d') : '—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('وضعیت'),

                Tables\Filters\Filter::make('unused')
                    ->label('استفاده‌نشده')
                    ->query(fn (Builder $query) => $query->doesntHave('assignments')),

                Tables\Filters\Filter::make('used')
                    ->label('استفاده‌شده')
                    ->query(fn (Builder $query) => $query->has('assignments')),

                Tables\Filters\Filter::make('future')
                    ->label('برنامهٔ آینده')
                    ->query(fn (Builder $query) => $query->whereHas(
                        'assignments',
                        fn (Builder $q) => $q->whereDate('week_start', '>', now('Asia/Tehran')->toDateString())
                    )),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            // شمارش دفعات تخصیص (times_used) بدون N+1
            ->withCount('assignments')
            // آخرین هفتهٔ نمایش (last_used_at) با subquery
            ->addSelect([
                'last_used_at' => WeeklyMovieAssignment::query()
                    ->select('week_start')
                    ->whereColumn('film_id', 'daily_films.id')
                    ->orderByDesc('week_start')
                    ->limit(1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFilms::route('/'),
            'create' => Pages\CreateFilm::route('/create'),
            'edit'   => Pages\EditFilm::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeeklyMovieAssignmentResource\Pages;
use App\Models\DailyFilm;
use App\Models\WeeklyMovieAssignment;
use App\Services\WeeklyMovie\WeeklyMovieWeekResolver;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class WeeklyMovieAssignmentResource extends Resource
{
    protected static ?string $model = WeeklyMovieAssignment::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'برنامهٔ فیلم هفته';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 6;
    protected static ?string $modelLabel = 'تخصیص هفتگی';
    protected static ?string $pluralModelLabel = 'برنامهٔ فیلم هفته';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('film_id')
                ->label('فیلم')
                ->options(fn () => DailyFilm::query()
                    ->where('is_active', true)
                    ->orderBy('title')
                    ->get()
                    ->mapWithKeys(fn (DailyFilm $film) => [
                        $film->id => static::filmOptionLabel($film),
                    ])
                    ->all())
                ->searchable()
                ->required(),

            Forms\Components\DatePicker::make('week_start')
                ->label('هفته')
                ->required()
                ->live()
                ->helperText(fn ($state) => new HtmlString(
                    ($state ? 'معادل شمسی: ' . pdate($state, 'l j F Y') . '<br>' : '')
                    . 'هر تاریخی در هفتهٔ موردنظر را می‌توانید انتخاب کنید؛ '
                    . 'به‌طور خودکار به شنبهٔ همان هفته نرمال می‌شود.'
                )),

            // منبعِ تخصیص در این فاز همیشه دستی است.
            Forms\Components\Hidden::make('assignment_source')
                ->default('manual'),
        ]);
    }

    public static function table(Table $table): Table
    {
        $currentWeekStart = (new WeeklyMovieWeekResolver)->currentWeek()['start']->toDateString();

        $isCurrentWeek = fn (WeeklyMovieAssignment $record): bool => $record->week_start
            && $record->week_start->toDateString() === $currentWeekStart;

        return $table
            ->defaultSort('week_start', 'desc')
            // شمارشِ تفکیکیِ رأی‌ها در همان کوئریِ جدول (بدون کوئریِ اضافه per-row).
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'decisions as will_watch_count' => fn ($q) => $q->where('decision', 'will_watch'),
                'decisions as will_not_count'   => fn ($q) => $q->where('decision', 'will_not_watch'),
            ]))
            ->columns([
                // ردیفِ هفتهٔ جاری با رنگِ برند و نشانِ «● هفتهٔ جاری» برجسته می‌شود.
                Tables\Columns\TextColumn::make('week_start')
                    ->label('بازهٔ هفته')
                    ->formatStateUsing(fn (WeeklyMovieAssignment $record) => 'شنبه '
                        . pdate($record->week_start, 'Y/m/d')
                        . ' تا جمعه '
                        . pdate($record->week_end, 'Y/m/d'))
                    ->description(fn (WeeklyMovieAssignment $record) => $isCurrentWeek($record)
                        ? '● هفتهٔ جاری'
                        : null)
                    ->color(fn (WeeklyMovieAssignment $record) => $isCurrentWeek($record) ? 'primary' : null)
                    ->weight(fn (WeeklyMovieAssignment $record) => $isCurrentWeek($record)
                        ? \Filament\Support\Enums\FontWeight::Bold
                        : null)
                    ->sortable(),

                Tables\Columns\TextColumn::make('film.title')
                    ->label('فیلم')
                    ->getStateUsing(fn (WeeklyMovieAssignment $record) => $record->film?->title
                        ?: $record->film?->original_title
                        ?: '—')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'     => 'فعال',
                        'superseded' => 'جایگزین‌شده',
                        default      => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'active'     => 'success',
                        'superseded' => 'gray',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignment_source')
                    ->label('منبع')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'manual'    => 'دستی',
                        'automatic' => 'خودکار',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('decisions_count')
                    ->label('تصمیم‌ها')
                    ->counts('decisions')
                    ->sortable(),

                Tables\Columns\TextColumn::make('will_watch_count')
                    ->label('می‌بینند')
                    ->formatStateUsing(fn ($state) => fa((int) $state))
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('will_not_count')
                    ->label('نمی‌بینند')
                    ->formatStateUsing(fn ($state) => fa((int) $state))
                    ->toggleable()
                    ->sortable(),

                // ٪ تماشا = will_watch / (will_watch + will_not) — بدون رأی، صفر.
                Tables\Columns\TextColumn::make('watch_pct')
                    ->label('٪ تماشا')
                    ->getStateUsing(function (WeeklyMovieAssignment $record): string {
                        $watch = (int) ($record->will_watch_count ?? 0);
                        $not   = (int) ($record->will_not_count ?? 0);
                        $total = $watch + $not;

                        return '٪' . fa($total ? (int) round($watch / $total * 100) : 0);
                    })
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active'     => 'فعال',
                        'superseded' => 'جایگزین‌شده',
                    ]),

                Tables\Filters\Filter::make('current_week')
                    ->label('هفتهٔ جاری')
                    ->query(fn (Builder $query) => $query->whereDate('week_start', $currentWeekStart)),

                Tables\Filters\Filter::make('future_weeks')
                    ->label('هفته‌های آینده')
                    ->query(fn (Builder $query) => $query->whereDate('week_start', '>', $currentWeekStart)),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    /** برچسبِ گزینهٔ فیلم: «عنوان (عنوان اصلی) — سال». */
    protected static function filmOptionLabel(DailyFilm $film): string
    {
        $label = $film->title ?: $film->original_title ?: ('#' . $film->id);

        if ($film->title && $film->original_title) {
            $label .= ' (' . $film->original_title . ')';
        }

        if ($film->year) {
            $label .= ' — ' . $film->year;
        }

        return $label;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWeeklyMovieAssignments::route('/'),
            'create' => Pages\CreateWeeklyMovieAssignment::route('/create'),
            'edit'   => Pages\EditWeeklyMovieAssignment::route('/{record}/edit'),
        ];
    }
}

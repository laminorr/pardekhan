<?php

namespace App\Filament\Resources\Topics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class TopicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان پرونده')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('published_at')
                    ->label('تاریخ نشر')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => self::toJalaliDateTime($state)),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('ویرایش'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف گروهی'),
                ]),
            ]);
    }

    protected static function toJalaliDateTime($state): string
    {
        if (! $state) {
            return 'منتشر نشده';
        }

        $date = $state instanceof Carbon
            ? $state
            : Carbon::parse($state);

        [$jy, $jm, $jd] = self::gregorianToJalali(
            (int) $date->format('Y'),
            (int) $date->format('m'),
            (int) $date->format('d')
        );

        $formatted = sprintf(
            '%04d/%02d/%02d ساعت %s',
            $jy,
            $jm,
            $jd,
            $date->format('H:i')
        );

        return self::faDigits($formatted);
    }

    protected static function faDigits(string $value): string
    {
        return strtr($value, [
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
    }

    protected static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy -= 1600;
        $gm -= 1;
        $gd -= 1;

        $gDayNo = 365 * $gy
            + intdiv($gy + 3, 4)
            - intdiv($gy + 99, 100)
            + intdiv($gy + 399, 400);

        for ($i = 0; $i < $gm; $i++) {
            $gDayNo += $gDaysInMonth[$i];
        }

        if ($gm > 1 && (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0))) {
            $gDayNo++;
        }

        $gDayNo += $gd;

        $jDayNo = $gDayNo - 79;

        $jNp = intdiv($jDayNo, 12053);
        $jDayNo %= 12053;

        $jy = 979 + 33 * $jNp + 4 * intdiv($jDayNo, 1461);
        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jy += intdiv($jDayNo - 1, 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        for ($i = 0; $i < 11 && $jDayNo >= $jDaysInMonth[$i]; $i++) {
            $jDayNo -= $jDaysInMonth[$i];
        }

        $jm = $i + 1;
        $jd = $jDayNo + 1;

        return [$jy, $jm, $jd];
    }
}
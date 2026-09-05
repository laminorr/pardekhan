<?php

namespace App\Filament\Resources\WeeklyMovieAssignmentResource\Pages;

use App\Filament\Resources\WeeklyMovieAssignmentResource;
use App\Models\WeeklyMovieAssignment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateWeeklyMovieAssignment extends CreateRecord
{
    protected static string $resource = WeeklyMovieAssignmentResource::class;

    /**
     * ساخت رکوردِ تخصیص در یک ترنزکشن:
     *  ۱) منبع = manual و created_by = کاربرِ جاری.
     *  ۲) نرمال‌سازیِ week_start به شنبه (در model event) هنگام create.
     *  ۳) supersede کردنِ تخصیصِ فعالِ پیشین برای همان هفته.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): WeeklyMovieAssignment {
            $data['assignment_source'] = 'manual';
            $data['created_by'] = auth()->id();
            $data['status'] = 'active';

            /** @var WeeklyMovieAssignment $record */
            $record = static::getModel()::create($data);
            $record->supersedePeers();

            return $record;
        });
    }
}

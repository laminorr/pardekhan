<?php

namespace App\Filament\Resources\WeeklyMovieAssignmentResource\Pages;

use App\Filament\Resources\WeeklyMovieAssignmentResource;
use App\Models\WeeklyMovieAssignment;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditWeeklyMovieAssignment extends EditRecord
{
    protected static string $resource = WeeklyMovieAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    /**
     * ویرایشِ تخصیص در یک ترنزکشن: پس از به‌روزرسانی، اگر رکورد فعال است
     * و هفته‌اش عوض شده، تخصیصِ فعالِ پیشینِ همان هفته supersede می‌شود
     * (منطقِ «یک فعال در هر هفته» هنگام تغییرِ وسطِ هفته).
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): WeeklyMovieAssignment {
            /** @var WeeklyMovieAssignment $record */
            $record->update($data);
            $record->supersedePeers();

            return $record;
        });
    }
}

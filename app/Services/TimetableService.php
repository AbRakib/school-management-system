<?php

namespace App\Services;

use App\Models\Timetable;

class TimetableService
{
    public function isTeacherAvailable(int $staffId, string $day, int $periodId, ?int $excludeTimetableId = null): bool
    {
        return ! Timetable::query()
            ->where('staff_id', $staffId)
            ->where('day', $day)
            ->where('timetable_period_id', $periodId)
            ->when($excludeTimetableId, fn ($query) => $query->where('id', '!=', $excludeTimetableId))
            ->exists();
    }
}

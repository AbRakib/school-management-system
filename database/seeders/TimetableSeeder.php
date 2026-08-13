<?php

namespace Database\Seeders;

use App\Models\ClassTeacherSubject;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use Illuminate\Database\Seeder;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        $times = [
            ['Period 1', '09:00', '10:00'],
            ['Period 2', '10:00', '11:00'],
            ['Period 3', '11:00', '12:00'],
            ['Period 4', '12:00', '13:00'],
            ['Period 5', '14:00', '15:00'],
            ['Period 6', '15:00', '16:00'],
        ];

        foreach ($times as [$name, $start, $end]) {
            TimetablePeriod::query()->updateOrCreate(['name' => $name], ['start_time' => $start, 'end_time' => $end]);
        }

        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];
        $periods = TimetablePeriod::query()->orderBy('start_time')->get();
        $usedTeachers = [];

        foreach (ClassTeacherSubject::query()->take(18)->get() as $index => $assignment) {
            $day = $days[$index % count($days)];
            $period = $periods[$index % $periods->count()];
            $teacherKey = "{$assignment->staff_id}-{$day}-{$period->id}";
            if (isset($usedTeachers[$teacherKey])) {
                continue;
            }
            $usedTeachers[$teacherKey] = true;

            Timetable::query()->updateOrCreate([
                'class_id' => $assignment->class_id,
                'section_id' => $assignment->section_id,
                'day' => $day,
                'timetable_period_id' => $period->id,
            ], [
                'academic_session_id' => $assignment->academic_session_id,
                'subject_id' => $assignment->subject_id,
                'staff_id' => $assignment->staff_id,
            ]);
        }
    }
}

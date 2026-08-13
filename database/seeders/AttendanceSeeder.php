<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $markedBy = User::query()->first();

        if (! $markedBy) {
            return;
        }

        $studentStatuses = ['present', 'present', 'present', 'present', 'absent', 'late', 'half_day'];
        $staffStatuses = ['present', 'present', 'present', 'absent', 'late', 'on_leave'];

        foreach (range(0, 29) as $day) {
            $date = now()->subDays($day)->toDateString();

            Student::query()
                ->whereNotNull('class_id')
                ->whereNotNull('section_id')
                ->whereNotNull('academic_session_id')
                ->get()
                ->each(function (Student $student) use ($date, $markedBy, $studentStatuses) {
                    StudentAttendance::query()->updateOrCreate([
                        'student_id' => $student->id,
                        'date' => $date,
                    ], [
                        'class_id' => $student->class_id,
                        'section_id' => $student->section_id,
                        'academic_session_id' => $student->academic_session_id,
                        'status' => fake()->randomElement($studentStatuses),
                        'marked_by' => $markedBy->id,
                        'remarks' => null,
                    ]);
                });

            Staff::query()
                ->where('status', 'active')
                ->get()
                ->each(function (Staff $staff) use ($date, $markedBy, $staffStatuses) {
                    $status = fake()->randomElement($staffStatuses);

                    StaffAttendance::query()->updateOrCreate([
                        'staff_id' => $staff->id,
                        'date' => $date,
                    ], [
                        'status' => $status,
                        'check_in' => in_array($status, ['present', 'late'], true) ? ($status === 'late' ? '09:30' : '08:30') : null,
                        'check_out' => in_array($status, ['present', 'late'], true) ? '16:00' : null,
                        'marked_by' => $markedBy->id,
                    ]);
                });
        }
    }
}

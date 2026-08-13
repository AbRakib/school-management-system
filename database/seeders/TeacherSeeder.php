<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Seed teachers and assign each class a class teacher.
     */
    public function run(): void
    {
        $headTeacher = Teacher::query()->updateOrCreate([
            'email' => 'headteacher@school.test',
        ], [
            'name' => 'Dr. Amina Rahman',
            'phone' => '01710000001',
            'designation' => 'head_teacher',
            'subject' => 'Administration',
            'joining_date' => now()->subYears(8)->toDateString(),
            'address' => 'School Campus',
        ]);

        $headTeacherUser = User::query()->updateOrCreate([
            'email' => 'headteacher@school.test',
        ], [
            'name' => 'Dr. Amina Rahman',
            'password' => Hash::make('password'),
        ]);
        $headTeacherUser->forceFill(['email_verified_at' => now()])->save();
        $headTeacherUser->syncRoles([SchoolRoles::TEACHER]);

        $subjects = [
            'Bangla',
            'English',
            'Mathematics',
            'Science',
            'Social Science',
            'Religion',
            'ICT',
            'Physical Education',
            'Arts',
            'General Studies',
        ];

        foreach (range(1, 10) as $grade) {
            $teacher = Teacher::query()->updateOrCreate([
                'email' => "teacher{$grade}@school.test",
            ], [
                'name' => "Teacher {$grade}",
                'phone' => '01710'.str_pad((string) $grade, 6, '0', STR_PAD_LEFT),
                'designation' => 'teacher',
                'subject' => $subjects[$grade - 1],
                'joining_date' => now()->subYears(2)->toDateString(),
                'address' => 'School Campus',
            ]);

            $teacherUser = User::query()->updateOrCreate([
                'email' => "teacher{$grade}@school.test",
            ], [
                'name' => "Teacher {$grade}",
                'password' => Hash::make('password'),
            ]);
            $teacherUser->forceFill(['email_verified_at' => now()])->save();
            $teacherUser->syncRoles([SchoolRoles::TEACHER]);

            SchoolClass::query()->updateOrCreate([
                'grade_level' => $grade,
            ], [
                'name' => "Class {$grade}",
                'section' => 'A',
                'room' => "Room {$grade}",
                'class_teacher_id' => $grade === 10 ? $headTeacher->id : $teacher->id,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@school.test',
        ], [
            'name' => 'School Administrator',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

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

            $class = SchoolClass::query()->updateOrCreate([
                'grade_level' => $grade,
            ], [
                'name' => "Class {$grade}",
                'section' => 'A',
                'room' => "Room {$grade}",
                'class_teacher_id' => $grade === 10 ? $headTeacher->id : $teacher->id,
            ]);

            $guardian = Guardian::query()->updateOrCreate([
                'phone' => '01810'.str_pad((string) $grade, 6, '0', STR_PAD_LEFT),
            ], [
                'name' => "Parent {$grade}",
                'relationship' => $grade % 2 === 0 ? 'Mother' : 'Father',
                'email' => "parent{$grade}@school.test",
                'occupation' => 'Service Holder',
                'address' => "Student Address {$grade}",
            ]);

            Student::query()->updateOrCreate([
                'admission_number' => 'SMS-'.str_pad((string) $grade, 4, '0', STR_PAD_LEFT),
            ], [
                'name' => "Student {$grade}",
                'date_of_birth' => now()->subYears(5 + $grade)->toDateString(),
                'gender' => $grade % 2 === 0 ? 'Female' : 'Male',
                'school_class_id' => $class->id,
                'guardian_id' => $guardian->id,
                'phone' => null,
                'admission_date' => now()->subMonths($grade)->toDateString(),
                'address' => "Student Address {$grade}",
            ]);
        }
    }
}

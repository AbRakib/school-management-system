<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seed subjects for each school class.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'Bangla', 'code' => 'BAN'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'Mathematics', 'code' => 'MAT'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'Social Science', 'code' => 'SOC'],
            ['name' => 'Religion', 'code' => 'REL'],
            ['name' => 'ICT', 'code' => 'ICT'],
            ['name' => 'Physical Education', 'code' => 'PED'],
        ];

        foreach (SchoolClass::query()->orderBy('grade_level')->get() as $schoolClass) {
            foreach ($subjects as $subject) {
                $teacher = Teacher::query()
                    ->where('subject', $subject['name'])
                    ->first();

                Subject::query()->updateOrCreate([
                    'code' => $subject['code'].'-'.$schoolClass->grade_level,
                ], [
                    'name' => $subject['name'],
                    'school_class_id' => $schoolClass->id,
                    'teacher_id' => $teacher?->id,
                    'description' => "{$subject['name']} syllabus for {$schoolClass->name}.",
                ]);
            }
        }
    }
}

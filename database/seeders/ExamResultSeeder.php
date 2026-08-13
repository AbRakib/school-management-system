<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamType;
use App\Models\GradingScale;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\ResultService;
use Illuminate\Database\Seeder;

class ExamResultSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Class Test', 'weight_percentage' => 20],
            ['name' => 'Midterm', 'weight_percentage' => 30],
            ['name' => 'Final', 'weight_percentage' => 50],
        ] as $type) {
            ExamType::query()->updateOrCreate(['name' => $type['name']], $type);
        }

        foreach ([
            ['grade_name' => 'A+', 'min_percentage' => 80, 'max_percentage' => 100, 'grade_point' => 5, 'remarks' => 'Excellent'],
            ['grade_name' => 'A', 'min_percentage' => 70, 'max_percentage' => 79.99, 'grade_point' => 4, 'remarks' => 'Very Good'],
            ['grade_name' => 'B', 'min_percentage' => 60, 'max_percentage' => 69.99, 'grade_point' => 3.5, 'remarks' => 'Good'],
            ['grade_name' => 'C', 'min_percentage' => 50, 'max_percentage' => 59.99, 'grade_point' => 3, 'remarks' => 'Average'],
            ['grade_name' => 'D', 'min_percentage' => 40, 'max_percentage' => 49.99, 'grade_point' => 2, 'remarks' => 'Pass'],
            ['grade_name' => 'F', 'min_percentage' => 0, 'max_percentage' => 39.99, 'grade_point' => 0, 'remarks' => 'Fail'],
        ] as $scale) {
            GradingScale::query()->updateOrCreate(['grade_name' => $scale['grade_name']], $scale);
        }

        $class = SchoolClass::query()->where('grade_level', 5)->first() ?? SchoolClass::query()->first();
        $session = AcademicSession::query()->first();
        $enteredBy = User::query()->first();
        if (! $class || ! $session || ! $enteredBy) {
            return;
        }

        $exam = Exam::query()->updateOrCreate([
            'title' => 'Class 5 Midterm',
            'class_id' => $class->id,
            'academic_session_id' => $session->id,
        ], [
            'exam_type_id' => ExamType::query()->where('name', 'Midterm')->value('id'),
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'status' => 'completed',
        ]);

        $service = app(ResultService::class);
        $subjects = Subject::query()->take(3)->get();
        foreach ($subjects as $subject) {
            $examSubject = $exam->subjects()->updateOrCreate([
                'subject_id' => $subject->id,
            ], [
                'exam_date' => now()->addDays(rand(7, 14))->toDateString(),
                'full_marks' => 100,
                'pass_marks' => 40,
            ]);

            Student::query()
                ->where('class_id', $class->id)
                ->where('academic_session_id', $session->id)
                ->get()
                ->each(function (Student $student) use ($exam, $examSubject, $enteredBy, $service) {
                    $marks = rand(35, 98);
                    $grade = $service->calculateGrade($marks, 100);
                    ExamResult::query()->updateOrCreate([
                        'exam_subject_id' => $examSubject->id,
                        'student_id' => $student->id,
                    ], [
                        'exam_id' => $exam->id,
                        'marks_obtained' => $marks,
                        'grade' => $grade['grade'],
                        'grade_point' => $grade['grade_point'],
                        'remarks' => $grade['remarks'],
                        'entered_by' => $enteredBy->id,
                    ]);
                });
        }
    }
}

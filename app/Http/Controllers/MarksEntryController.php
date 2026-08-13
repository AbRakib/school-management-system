<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamSubject;
use App\Models\Student;
use App\Services\ResultService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MarksEntryController extends Controller
{
    public function show(Exam $exam, ExamSubject $examSubject): Response
    {
        abort_unless($examSubject->exam_id === $exam->id, 404);

        $existing = ExamResult::query()
            ->where('exam_subject_id', $examSubject->id)
            ->get()
            ->keyBy('student_id');

        $students = Student::query()
            ->where('class_id', $exam->class_id)
            ->where('academic_session_id', $exam->academic_session_id)
            ->orderByRaw('CAST(roll_no AS UNSIGNED), name')
            ->get()
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'admission_no' => $student->admission_no,
                'roll_no' => $student->roll_no,
                'marks_obtained' => $existing[$student->id]->marks_obtained ?? null,
                'remarks' => $existing[$student->id]->remarks ?? '',
            ]);

        return Inertia::render('exams/MarksEntry', [
            'exam' => $exam->load(['classRoom', 'academicSession']),
            'examSubject' => $examSubject->load('subject'),
            'students' => $students,
        ]);
    }

    public function store(Request $request, Exam $exam, ExamSubject $examSubject, ResultService $resultService): RedirectResponse
    {
        abort_unless($examSubject->exam_id === $exam->id, 404);

        $data = $request->validate([
            'results' => ['array'],
            'results.*.student_id' => ['required', 'exists:students,id'],
            'results.*.marks_obtained' => ['nullable', 'numeric', 'min:0', 'max:'.$examSubject->full_marks],
            'results.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $exam, $examSubject, $resultService, $request) {
            foreach ($data['results'] as $row) {
                $grade = $resultService->calculateGrade($row['marks_obtained'], (float) $examSubject->full_marks);
                ExamResult::query()->updateOrCreate([
                    'exam_subject_id' => $examSubject->id,
                    'student_id' => $row['student_id'],
                ], [
                    'exam_id' => $exam->id,
                    'marks_obtained' => $row['marks_obtained'],
                    'grade' => $grade['grade'],
                    'grade_point' => $grade['grade_point'],
                    'remarks' => $row['remarks'] ?? null,
                    'entered_by' => $request->user()->id,
                ]);
            }
        });

        return to_route('exams.show', $exam);
    }
}

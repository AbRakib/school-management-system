<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\GradingScale;

class ResultService
{
    /**
     * @return array{grade: string, grade_point: float, percentage: float, remarks: string|null}
     */
    public function calculateGrade(float|int|null $marksObtained, float|int $fullMarks): array
    {
        $percentage = $fullMarks > 0 && $marksObtained !== null
            ? round(((float) $marksObtained / (float) $fullMarks) * 100, 2)
            : 0.0;

        $scale = GradingScale::query()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->orderByDesc('min_percentage')
            ->first();

        return [
            'grade' => $scale?->grade_name ?? 'F',
            'grade_point' => (float) ($scale?->grade_point ?? 0),
            'percentage' => $percentage,
            'remarks' => $scale?->remarks,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function generateReportCard(int $studentId, int $examId): array
    {
        $exam = Exam::query()
            ->with(['examType', 'academicSession', 'classRoom', 'subjects.subject'])
            ->findOrFail($examId);

        $results = ExamResult::query()
            ->with(['examSubject.subject'])
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get()
            ->keyBy('exam_subject_id');

        $subjects = $exam->subjects->map(function ($examSubject) use ($results) {
            $result = $results->get($examSubject->id);
            $marks = $result?->marks_obtained;
            $grade = $this->calculateGrade($marks, (float) $examSubject->full_marks);
            $passed = $marks !== null && (float) $marks >= (float) $examSubject->pass_marks;

            return [
                'subject' => $examSubject->subject->name,
                'full_marks' => (float) $examSubject->full_marks,
                'pass_marks' => (float) $examSubject->pass_marks,
                'marks_obtained' => $marks !== null ? (float) $marks : null,
                'grade' => $grade['grade'],
                'grade_point' => $grade['grade_point'],
                'remarks' => $result?->remarks,
                'passed' => $passed,
            ];
        });

        $totalMarks = $subjects->sum('marks_obtained');
        $fullMarks = $subjects->sum('full_marks');
        $percentage = $fullMarks > 0 ? round(($totalMarks / $fullMarks) * 100, 2) : 0.0;
        $overall = $this->calculateGrade($totalMarks, $fullMarks ?: 1);
        $failed = $subjects->contains(fn (array $subject) => ! $subject['passed']);

        return [
            'exam' => $exam,
            'subjects' => $subjects->values(),
            'total_marks' => $totalMarks,
            'full_marks' => $fullMarks,
            'percentage' => $percentage,
            'overall_grade' => $failed ? 'F' : $overall['grade'],
            'gpa' => $failed ? 0 : $overall['grade_point'],
            'passed' => ! $failed,
        ];
    }
}

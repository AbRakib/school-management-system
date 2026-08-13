<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Services\ResultService;
use Inertia\Inertia;
use Inertia\Response;

class ReportCardController extends Controller
{
    public function show(Exam $exam, Student $student, ResultService $resultService): Response
    {
        return Inertia::render('exams/ReportCard', [
            'student' => $student->load(['guardian', 'section']),
            'report' => $resultService->generateReportCard($student->id, $exam->id),
        ]);
    }
}

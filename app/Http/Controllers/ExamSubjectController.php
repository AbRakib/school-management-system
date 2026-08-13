<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExamSubjectController extends Controller
{
    public function show(Exam $exam): Response
    {
        return Inertia::render('exams/Subjects', [
            'exam' => $exam->load(['subjects.subject', 'classRoom']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name', 'code', 'school_class_id']),
        ]);
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate([
            'subjects' => ['array'],
            'subjects.*.subject_id' => ['required', 'exists:subjects,id'],
            'subjects.*.exam_date' => ['nullable', 'date'],
            'subjects.*.full_marks' => ['required', 'numeric', 'min:1'],
            'subjects.*.pass_marks' => ['required', 'numeric', 'min:0'],
        ]);

        $exam->subjects()->delete();
        foreach ($data['subjects'] ?? [] as $subject) {
            $exam->subjects()->create($subject);
        }

        return to_route('exams.show', $exam);
    }
}

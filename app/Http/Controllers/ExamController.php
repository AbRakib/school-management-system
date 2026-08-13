<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['academic_session_id', 'class_id']);

        return Inertia::render('exams/Index', [
            'exams' => Exam::query()
                ->with(['examType:id,name', 'academicSession:id,name', 'classRoom:id,name'])
                ->when($filters['academic_session_id'] ?? null, fn ($query, $id) => $query->where('academic_session_id', $id))
                ->when($filters['class_id'] ?? null, fn ($query, $id) => $query->where('class_id', $id))
                ->latest()
                ->get(),
            'filters' => $filters,
            'options' => $this->options(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('exams/Create', [...$this->options(), 'exam' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $subjects = $data['subjects'] ?? [];
        unset($data['subjects']);
        $exam = Exam::query()->create($data);
        foreach ($subjects as $subject) {
            $exam->subjects()->create($subject);
        }
        return to_route('exams.show', $exam);
    }

    public function show(Exam $exam): Response
    {
        $exam->load(['examType', 'academicSession', 'classRoom', 'subjects.subject', 'subjects.results']);
        $studentCount = $exam->classRoom->students()->where('academic_session_id', $exam->academic_session_id)->count();
        return Inertia::render('exams/Show', [
            'exam' => $exam,
            'studentCount' => $studentCount,
        ]);
    }

    public function edit(Exam $exam): Response
    {
        return Inertia::render('exams/Create', [...$this->options(), 'exam' => $exam->load('subjects')]);
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $data = $this->validated($request, $exam);
        unset($data['subjects']);
        $exam->update($data);
        return to_route('exams.show', $exam);
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();
        return to_route('exams.index');
    }

    public function publish(Exam $exam): RedirectResponse
    {
        $exam->update(['status' => 'result_published']);
        return back();
    }

    private function validated(Request $request, ?Exam $exam = null): array
    {
        return $request->validate([
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'result_published'])],
            'subjects' => ['array'],
            'subjects.*.subject_id' => ['required', 'exists:subjects,id'],
            'subjects.*.exam_date' => ['nullable', 'date'],
            'subjects.*.full_marks' => ['required', 'numeric', 'min:1'],
            'subjects.*.pass_marks' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function options(): array
    {
        return [
            'examTypes' => ExamType::query()->orderBy('name')->get(['id', 'name']),
            'academicSessions' => AcademicSession::query()->orderByDesc('start_date')->get(['id', 'name']),
            'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name', 'grade_level']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name', 'code', 'school_class_id']),
            'statuses' => ['scheduled', 'ongoing', 'completed', 'result_published'],
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentAttendanceController extends Controller
{
    public function create(Request $request): Response
    {
        $filters = [
            'class_id' => $request->query('class_id'),
            'section_id' => $request->query('section_id'),
            'academic_session_id' => $request->query('academic_session_id'),
            'date' => $request->query('date', now()->toDateString()),
        ];

        $students = collect();

        if ($filters['class_id'] && $filters['section_id'] && $filters['academic_session_id']) {
            $marked = StudentAttendance::query()
                ->where('date', $filters['date'])
                ->where('class_id', $filters['class_id'])
                ->where('section_id', $filters['section_id'])
                ->get()
                ->keyBy('student_id');

            $students = Student::query()
                ->with(['schoolClass:id,name', 'section:id,name'])
                ->where('class_id', $filters['class_id'])
                ->where('section_id', $filters['section_id'])
                ->where('academic_session_id', $filters['academic_session_id'])
                ->where('status', 'active')
                ->orderByRaw('CAST(roll_no AS UNSIGNED), name')
                ->get()
                ->map(fn (Student $student) => [
                    'id' => $student->id,
                    'admission_no' => $student->admission_no,
                    'name' => $student->name,
                    'roll_no' => $student->roll_no,
                    'image_url' => $student->image_url,
                    'status' => $marked[$student->id]->status ?? 'present',
                    'remarks' => $marked[$student->id]->remarks ?? '',
                ]);
        }

        return Inertia::render('attendance/MarkStudent', [
            'students' => $students,
            'filters' => $filters,
            'options' => $this->options(),
            'statuses' => ['present', 'absent', 'late', 'half_day'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id'],
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'attendances.*.status' => ['required', Rule::in(['present', 'absent', 'late', 'half_day'])],
            'attendances.*.remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data, $request) {
            foreach ($data['attendances'] as $attendance) {
                StudentAttendance::query()->updateOrCreate([
                    'student_id' => $attendance['student_id'],
                    'date' => $data['date'],
                ], [
                    'class_id' => $data['class_id'],
                    'section_id' => $data['section_id'],
                    'academic_session_id' => $data['academic_session_id'],
                    'status' => $attendance['status'],
                    'remarks' => $attendance['remarks'] ?? null,
                    'marked_by' => $request->user()->id,
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student attendance saved successfully.']);

        return to_route('attendance.students.create', $request->only(['class_id', 'section_id', 'academic_session_id', 'date']));
    }

    public function report(Request $request): Response
    {
        $filters = [
            'class_id' => $request->query('class_id'),
            'section_id' => $request->query('section_id'),
            'student_id' => $request->query('student_id'),
            'start_date' => $request->query('start_date', now()->subDays(30)->toDateString()),
            'end_date' => $request->query('end_date', now()->toDateString()),
        ];

        $rows = Student::query()
            ->with(['schoolClass:id,name', 'section:id,name'])
            ->when($filters['class_id'], fn ($query, $id) => $query->where('class_id', $id))
            ->when($filters['section_id'], fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['student_id'], fn ($query, $id) => $query->where('id', $id))
            ->withCount([
                'attendances as marked_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]),
                'attendances as present_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']])->whereIn('status', ['present', 'late', 'half_day']),
                'attendances as absent_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']])->where('status', 'absent'),
                'attendances as late_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']])->where('status', 'late'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'admission_no' => $student->admission_no,
                'full_class_name' => $student->full_class_name,
                'marked_days' => $student->marked_days,
                'present_days' => $student->present_days,
                'absent_days' => $student->absent_days,
                'late_days' => $student->late_days,
                'percentage' => $student->marked_days ? round(($student->present_days / $student->marked_days) * 100, 2) : 0,
            ]);

        return Inertia::render('attendance/StudentReport', [
            'rows' => $rows,
            'filters' => $filters,
            'options' => [
                ...$this->options(),
                'students' => Student::query()->orderBy('name')->get(['id', 'name', 'admission_no']),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name', 'grade_level']),
            'sections' => Section::query()->orderBy('name')->get(['id', 'school_class_id', 'name']),
            'academicSessions' => AcademicSession::query()->orderByDesc('start_date')->get(['id', 'name']),
        ];
    }
}

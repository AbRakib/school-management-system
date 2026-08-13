<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassTeacherSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use App\Services\TimetableService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TimetableController extends Controller
{
    private array $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];

    public function builder(Request $request): Response
    {
        $filters = [
            'class_id' => $request->query('class_id'),
            'section_id' => $request->query('section_id'),
            'academic_session_id' => $request->query('academic_session_id'),
        ];

        return Inertia::render('timetable/Builder', [
            'days' => $this->days,
            'periods' => TimetablePeriod::query()->orderBy('start_time')->get(),
            'entries' => Timetable::query()
                ->with(['subject:id,name,code', 'staff:id,name,staff_id'])
                ->when($filters['class_id'], fn ($q, $id) => $q->where('class_id', $id))
                ->when($filters['section_id'], fn ($q, $id) => $q->where('section_id', $id))
                ->when($filters['academic_session_id'], fn ($q, $id) => $q->where('academic_session_id', $id))
                ->get(),
            'assignments' => ClassTeacherSubject::query()
                ->with(['subject:id,name,code', 'staff:id,name,staff_id'])
                ->when($filters['class_id'], fn ($q, $id) => $q->where('class_id', $id))
                ->when($filters['section_id'], fn ($q, $id) => $q->where('section_id', $id))
                ->when($filters['academic_session_id'], fn ($q, $id) => $q->where('academic_session_id', $id))
                ->get(),
            'busySlots' => Timetable::query()->get(['id', 'staff_id', 'day', 'timetable_period_id']),
            'filters' => $filters,
            'options' => $this->options(),
        ]);
    }

    public function saveCell(Request $request, TimetableService $service): RedirectResponse
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'day' => ['required', Rule::in($this->days)],
            'timetable_period_id' => ['required', 'exists:timetable_periods,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'staff_id' => ['required', 'exists:staff,id'],
        ]);

        $existing = Timetable::query()
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('day', $data['day'])
            ->where('timetable_period_id', $data['timetable_period_id'])
            ->first();

        if (! $service->isTeacherAvailable((int) $data['staff_id'], $data['day'], (int) $data['timetable_period_id'], $existing?->id)) {
            return back()->withErrors(['staff_id' => 'This teacher is already booked for that day and period.']);
        }

        try {
            Timetable::query()->updateOrCreate([
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'day' => $data['day'],
                'timetable_period_id' => $data['timetable_period_id'],
            ], $data);
        } catch (QueryException) {
            return back()->withErrors(['staff_id' => 'This teacher is already booked for that day and period.']);
        }

        return back();
    }

    public function teacherSchedule(Request $request, ?Staff $staff = null): Response
    {
        $staff ??= Staff::query()->teachers()->first();
        return Inertia::render('timetable/TeacherSchedule', [
            'selectedStaff' => $staff,
            'staff' => Staff::query()->teachers()->orderBy('name')->get(['id', 'staff_id', 'name']),
            'days' => $this->days,
            'periods' => TimetablePeriod::query()->orderBy('start_time')->get(),
            'entries' => $staff ? Timetable::query()->with(['classRoom:id,name', 'section:id,name', 'subject:id,name,code'])->where('staff_id', $staff->id)->get() : [],
        ]);
    }

    private function options(): array
    {
        return [
            'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name', 'grade_level']),
            'sections' => Section::query()->orderBy('name')->get(['id', 'school_class_id', 'name']),
            'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(['id', 'name']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name', 'code', 'school_class_id']),
        ];
    }
}

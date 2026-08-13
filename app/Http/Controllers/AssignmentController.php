<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassTeacherSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function show(Staff $staff): Response
    {
        return Inertia::render('staff/Assignments', [
            'staffMember' => $staff->load([
                'department:id,name',
                'designation:id,name,is_teaching_staff',
                'assignments.classRoom:id,name',
                'assignments.section:id,name',
                'assignments.subject:id,name,code',
                'assignments.academicSession:id,name',
            ]),
            'options' => [
                'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name', 'grade_level']),
                'sections' => Section::query()->orderBy('name')->get(['id', 'school_class_id', 'name']),
                'subjects' => Subject::query()->orderBy('name')->get(['id', 'name', 'code', 'school_class_id']),
                'academicSessions' => AcademicSession::query()->orderByDesc('start_date')->get(['id', 'name']),
            ],
        ]);
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validate([
            'assignments' => ['array'],
            'assignments.*.class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'assignments.*.section_id' => ['required', 'integer', 'exists:sections,id'],
            'assignments.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'assignments.*.academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ]);

        try {
            $staff->assignments()->delete();

            foreach ($data['assignments'] ?? [] as $assignment) {
                ClassTeacherSubject::query()->create([
                    ...$assignment,
                    'staff_id' => $staff->id,
                ]);
            }
        } catch (QueryException) {
            return back()->withErrors([
                'assignments' => 'This class, section, subject, and session already has a teacher assigned.',
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Assignments saved successfully.']);

        return to_route('staff.assignments.show', $staff);
    }
}

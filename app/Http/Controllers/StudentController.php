<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicSession;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['class_id', 'section_id', 'academic_session_id', 'status', 'search']);

        return Inertia::render('students/Index', [
            'students' => Student::query()
                ->with(['schoolClass:id,name', 'section:id,name', 'guardian:id,name,phone', 'academicSession:id,name'])
                ->when($filters['class_id'] ?? null, fn ($query, $classId) => $query->where('class_id', $classId))
                ->when($filters['section_id'] ?? null, fn ($query, $sectionId) => $query->where('section_id', $sectionId))
                ->when($filters['academic_session_id'] ?? null, fn ($query, $sessionId) => $query->where('academic_session_id', $sessionId))
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('admission_no', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'filters' => $filters,
            'options' => $this->options(),
            'canManage' => $this->canManage($request),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('students/Form', [
            ...$this->options(),
            'student' => null,
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $this->studentData($request);
        $data['guardian_id'] = $this->resolveGuardian($request);

        if ($photo = $request->file('photo')) {
            $data['photo'] = $photo->store('students', 'public');
        }

        $student = Student::query()->create($data);

        if ($request->boolean('create_user')) {
            $this->createLinkedUser($student);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student admitted successfully.']);

        return to_route('students.show', $student);
    }

    public function show(Student $student): Response
    {
        return Inertia::render('students/Show', [
            'student' => $student->load([
                'user:id,name,email',
                'schoolClass:id,name',
                'section:id,name',
                'academicSession:id,name',
                'guardian',
                'studentCategory:id,name',
            ]),
        ]);
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('students/Form', [
            ...$this->options(),
            'student' => $student->load(['guardian', 'section', 'academicSession', 'studentCategory']),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $this->studentData($request);
        $data['guardian_id'] = $this->resolveGuardian($request);

        if ($photo = $request->file('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            $data['photo'] = $photo->store('students', 'public');
        }

        $student->update($data);

        if ($request->boolean('create_user') && ! $student->user_id) {
            $this->createLinkedUser($student->fresh());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student updated successfully.']);

        return to_route('students.show', $student);
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student deleted successfully.']);

        return to_route('students.index');
    }

    public function idCards(Request $request): Response
    {
        $filters = $request->only(['class_id', 'section_id']);

        return Inertia::render('students/IdCards', [
            'students' => Student::query()
                ->with(['schoolClass:id,name', 'section:id,name', 'guardian:id,name,phone'])
                ->when($filters['class_id'] ?? null, fn ($query, $classId) => $query->where('class_id', $classId))
                ->when($filters['section_id'] ?? null, fn ($query, $sectionId) => $query->where('section_id', $sectionId))
                ->orderBy('roll_no')
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
            'options' => $this->options(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function studentData(Request $request): array
    {
        return $request->safe()->only([
            'admission_no',
            'class_id',
            'section_id',
            'academic_session_id',
            'roll_no',
            'student_category_id',
            'name',
            'dob',
            'gender',
            'blood_group',
            'religion',
            'present_address',
            'permanent_address',
            'admission_date',
            'status',
        ]);
    }

    private function resolveGuardian(Request $request): int
    {
        if ($request->input('guardian_mode') === 'existing') {
            return (int) $request->input('guardian_id');
        }

        $guardian = $request->input('guardian', []);
        $relation = $guardian['relation'] ?? 'guardian';

        return Guardian::query()->create([
            'name' => $guardian['name'],
            'phone' => $guardian['phone'],
            'email' => $guardian['email'] ?? null,
            'relation' => $relation,
            'relationship' => ucfirst($relation),
            'occupation' => $guardian['occupation'] ?? null,
            'address' => $guardian['address'] ?? null,
        ])->id;
    }

    private function createLinkedUser(Student $student): void
    {
        Role::findOrCreate(SchoolRoles::STUDENT);

        $email = strtolower($student->admission_no).'@students.school.test';
        $user = User::query()->updateOrCreate([
            'email' => $email,
        ], [
            'name' => $student->name,
            'password' => Hash::make('password'),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->syncRoles([SchoolRoles::STUDENT]);

        $student->forceFill(['user_id' => $user->id])->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name', 'grade_level']),
            'sections' => Section::query()->with('schoolClass:id,name')->orderBy('name')->get(['id', 'school_class_id', 'name']),
            'academicSessions' => AcademicSession::query()->orderByDesc('start_date')->orderBy('name')->get(['id', 'name', 'status']),
            'guardians' => Guardian::query()->orderBy('name')->get(['id', 'name', 'phone', 'relation']),
            'categories' => StudentCategory::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => ['active', 'inactive', 'graduated', 'transferred'],
        ];
    }

    private function canManage(Request $request): bool
    {
        return $request->user()?->hasAnyRole([SchoolRoles::SUPER_ADMIN, SchoolRoles::ADMIN]) ?? false;
    }
}

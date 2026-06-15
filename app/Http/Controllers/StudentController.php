<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProfileImages;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    use HandlesProfileImages;

    public function index(): Response
    {
        return Inertia::render('students/Index', [
            'students' => Student::query()
                ->with(['schoolClass:id,name', 'guardian:id,name,phone'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('students/Form', $this->options());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        if ($image = $this->storeProfileImage($request, 'profile-images/students')) {
            $data['image'] = $image;
        }

        Student::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student admitted successfully.']);

        return to_route('students.index');
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('students/Form', [
            ...$this->options(),
            'student' => $student,
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $this->validated($request, $student);
        unset($data['image']);

        $oldImage = $student->image;
        if ($image = $this->storeProfileImage($request, 'profile-images/students')) {
            $data['image'] = $image;
        }

        $student->update($data);

        if (isset($data['image'])) {
            $this->deleteProfileImage($oldImage);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student updated successfully.']);

        return to_route('students.index');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $image = $student->image;
        $student->delete();
        $this->deleteProfileImage($image);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student deleted successfully.']);

        return to_route('students.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'admission_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students')->ignore($student),
            ],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'guardian_id' => ['required', 'integer', 'exists:guardians,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'admission_date' => ['required', 'date'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name']),
            'guardians' => Guardian::query()->orderBy('name')->get(['id', 'name', 'phone']),
        ];
    }
}

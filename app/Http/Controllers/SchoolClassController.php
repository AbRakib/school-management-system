<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SchoolClassController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('classes/Index', [
            'classes' => SchoolClass::query()
                ->with('classTeacher:id,name')
                ->withCount('students')
                ->orderBy('grade_level')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('classes/Form', [
            'teachers' => $this->teachers(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        SchoolClass::query()->create($this->validated($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Class created successfully.']);

        return to_route('classes.index');
    }

    public function edit(SchoolClass $class): Response
    {
        return Inertia::render('classes/Form', [
            'schoolClass' => $class,
            'teachers' => $this->teachers(),
        ]);
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $class->update($this->validated($request, $class));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Class updated successfully.']);

        return to_route('classes.index');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        if ($class->students()->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Move students before deleting this class.']);

            return to_route('classes.index');
        }

        $class->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Class deleted successfully.']);

        return to_route('classes.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?SchoolClass $class = null): array
    {
        return $request->validate([
            'grade_level' => [
                'required',
                'integer',
                'between:1,10',
                Rule::unique('school_classes')->ignore($class),
            ],
            'name' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:20'],
            'room' => ['nullable', 'string', 'max:50'],
            'class_teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
        ]);
    }

    /**
     * @return Collection<int, Teacher>
     */
    private function teachers(): Collection
    {
        return Teacher::query()->orderBy('name')->get(['id', 'name', 'designation']);
    }
}

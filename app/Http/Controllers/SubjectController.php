<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('subjects/Index', [
            'subjects' => Subject::query()
                ->with(['schoolClass:id,name,grade_level', 'teacher:id,name'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('subjects/Form', $this->options());
    }

    public function store(Request $request): RedirectResponse
    {
        Subject::query()->create($this->validated($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Subject created successfully.']);

        return to_route('subjects.index');
    }

    public function edit(Subject $subject): Response
    {
        return Inertia::render('subjects/Form', [
            'subject' => $subject,
            ...$this->options(),
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $subject->update($this->validated($request, $subject));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Subject updated successfully.']);

        return to_route('subjects.index');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Subject deleted successfully.']);

        return to_route('subjects.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Subject $subject = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects')->ignore($subject)],
            'school_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /**
     * @return array{classes: Collection<int, SchoolClass>, teachers: Collection<int, Teacher>}
     */
    private function options(): array
    {
        return [
            'classes' => SchoolClass::query()
                ->orderBy('grade_level')
                ->get(['id', 'name', 'grade_level']),
            'teachers' => Teacher::query()
                ->orderBy('name')
                ->get(['id', 'name', 'designation']),
        ];
    }
}

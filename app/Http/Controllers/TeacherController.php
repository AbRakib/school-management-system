<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProfileImages;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    use HandlesProfileImages;

    public function index(): Response
    {
        return Inertia::render('teachers/Index', [
            'teachers' => Teacher::query()
                ->withCount('classes')
                ->orderByRaw("case when designation = 'head_teacher' then 0 else 1 end")
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('teachers/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        if ($image = $this->storeProfileImage($request, 'profile-images/teachers')) {
            $data['image'] = $image;
        }

        Teacher::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Teacher created successfully.']);

        return to_route('teachers.index');
    }

    public function edit(Teacher $teacher): Response
    {
        return Inertia::render('teachers/Form', [
            'teacher' => $teacher,
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $data = $this->validated($request, $teacher);
        unset($data['image']);

        $oldImage = $teacher->image;
        if ($image = $this->storeProfileImage($request, 'profile-images/teachers')) {
            $data['image'] = $image;
        }

        $teacher->update($data);

        if (isset($data['image'])) {
            $this->deleteProfileImage($oldImage);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Teacher updated successfully.']);

        return to_route('teachers.index');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $image = $teacher->image;
        $teacher->delete();
        $this->deleteProfileImage($image);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Teacher deleted successfully.']);

        return to_route('teachers.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Teacher $teacher = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('teachers')->ignore($teacher)],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['required', Rule::in(['teacher', 'head_teacher'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\StudentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('student-categories/Index', [
            'categories' => StudentCategory::query()
                ->withCount('students')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        StudentCategory::query()->create($this->validated($request));

        return to_route('student-categories.index');
    }

    public function update(Request $request, StudentCategory $studentCategory): RedirectResponse
    {
        $studentCategory->update($this->validated($request, $studentCategory));

        return to_route('student-categories.index');
    }

    public function destroy(StudentCategory $studentCategory): RedirectResponse
    {
        if ($studentCategory->students()->exists()) {
            return back()->withErrors(['category' => 'This category still has students.']);
        }

        $studentCategory->delete();

        return to_route('student-categories.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?StudentCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('student_categories')->ignore($category)],
        ]);
    }
}

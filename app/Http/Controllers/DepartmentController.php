<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('staff/Settings', [
            'departments' => Department::query()->withCount('staff')->orderBy('name')->get(),
            'designations' => Designation::query()->withCount('staff')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Department::query()->create($this->validated($request));

        return to_route('departments.index');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $department->update($this->validated($request, $department));

        return to_route('departments.index');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->staff()->exists()) {
            return back()->withErrors(['department' => 'This department still has staff members.']);
        }

        $department->delete();

        return to_route('departments.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Department $department = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department)],
        ]);
    }
}

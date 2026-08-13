<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('departments.index');
    }

    public function store(Request $request): RedirectResponse
    {
        Designation::query()->create($this->validated($request));

        return to_route('departments.index');
    }

    public function update(Request $request, Designation $designation): RedirectResponse
    {
        $designation->update($this->validated($request, $designation));

        return to_route('departments.index');
    }

    public function destroy(Designation $designation): RedirectResponse
    {
        if ($designation->staff()->exists()) {
            return back()->withErrors(['designation' => 'This designation still has staff members.']);
        }

        $designation->delete();

        return to_route('departments.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Designation $designation = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('designations')->ignore($designation)],
            'is_teaching_staff' => ['boolean'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['department_id', 'designation_id', 'status', 'search']);

        return Inertia::render('staff/Index', [
            'staff' => Staff::query()
                ->with(['department:id,name', 'designation:id,name,is_teaching_staff'])
                ->when($filters['department_id'] ?? null, fn ($query, $id) => $query->where('department_id', $id))
                ->when($filters['designation_id'] ?? null, fn ($query, $id) => $query->where('designation_id', $id))
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'filters' => $filters,
            'options' => $this->options(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('staff/Form', [
            ...$this->options(),
            'staffMember' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($photo = $request->file('photo')) {
            $data['photo'] = $photo->store('staff', 'public');
        }

        $designation = Designation::query()->findOrFail($data['designation_id']);
        $user = $this->createUser($data['name'], $data['email'], $designation->is_teaching_staff);
        $data['user_id'] = $user->id;

        $staff = Staff::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Staff member created successfully.']);

        return to_route('staff.show', $staff);
    }

    public function show(Staff $staff): Response
    {
        return Inertia::render('staff/Show', [
            'staffMember' => $staff->load([
                'user:id,name,email',
                'department:id,name',
                'designation:id,name,is_teaching_staff',
                'assignments.classRoom:id,name',
                'assignments.section:id,name',
                'assignments.subject:id,name,code',
                'assignments.academicSession:id,name',
            ]),
        ]);
    }

    public function edit(Staff $staff): Response
    {
        return Inertia::render('staff/Form', [
            ...$this->options(),
            'staffMember' => $staff->load(['department:id,name', 'designation:id,name,is_teaching_staff']),
        ]);
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $data = $this->validated($request, $staff);

        if ($photo = $request->file('photo')) {
            if ($staff->photo) {
                Storage::disk('public')->delete($staff->photo);
            }

            $data['photo'] = $photo->store('staff', 'public');
        }

        $staff->update($data);
        $staff->user->update(['name' => $staff->name, 'email' => $staff->email]);
        $this->syncRole($staff->user, $staff->designation->is_teaching_staff);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Staff member updated successfully.']);

        return to_route('staff.show', $staff);
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $staff->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Staff member deleted successfully.']);

        return to_route('staff.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Staff $staff = null): array
    {
        return $request->validate([
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'designation_id' => ['required', 'integer', 'exists:designations,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('staff', 'email')->ignore($staff)],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'joining_date' => ['required', 'date'],
            'qualification' => ['nullable', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive', 'resigned'])],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'designations' => Designation::query()->orderBy('name')->get(['id', 'name', 'is_teaching_staff']),
            'statuses' => ['active', 'inactive', 'resigned'],
        ];
    }

    private function createUser(string $name, string $email, bool $isTeacher): User
    {
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $this->syncRole($user, $isTeacher);

        return $user;
    }

    private function syncRole(User $user, bool $isTeacher): void
    {
        Role::findOrCreate($isTeacher ? SchoolRoles::TEACHER : SchoolRoles::STAFF);
        $user->syncRoles([$isTeacher ? SchoolRoles::TEACHER : SchoolRoles::STAFF]);
    }
}

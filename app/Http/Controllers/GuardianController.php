<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProfileImages;
use App\Models\Guardian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GuardianController extends Controller
{
    use HandlesProfileImages;

    public function index(): Response
    {
        return Inertia::render('guardians/Index', [
            'guardians' => Guardian::query()
                ->withCount('students')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('guardians/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        if ($image = $this->storeProfileImage($request, 'profile-images/guardians')) {
            $data['image'] = $image;
        }

        Guardian::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Parent created successfully.']);

        return to_route('guardians.index');
    }

    public function edit(Guardian $guardian): Response
    {
        return Inertia::render('guardians/Form', [
            'guardian' => $guardian,
        ]);
    }

    public function update(Request $request, Guardian $guardian): RedirectResponse
    {
        $data = $this->validated($request, $guardian);
        unset($data['image']);

        $oldImage = $guardian->image;
        if ($image = $this->storeProfileImage($request, 'profile-images/guardians')) {
            $data['image'] = $image;
        }

        $guardian->update($data);

        if (isset($data['image'])) {
            $this->deleteProfileImage($oldImage);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Parent updated successfully.']);

        return to_route('guardians.index');
    }

    public function destroy(Guardian $guardian): RedirectResponse
    {
        if ($guardian->students()->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'This parent still has linked students.']);

            return to_route('guardians.index');
        }

        $image = $guardian->image;
        $guardian->delete();
        $this->deleteProfileImage($image);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Parent deleted successfully.']);

        return to_route('guardians.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Guardian $guardian = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'relationship' => ['required', 'string', 'max:50'],
            'relation' => ['nullable', Rule::in(['father', 'mother', 'guardian'])],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('guardians')->ignore($guardian)],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

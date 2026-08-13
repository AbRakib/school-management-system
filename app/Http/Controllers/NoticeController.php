<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class NoticeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Notices/Index', [
            'notices' => Notice::query()
                ->with(['classRoom:id,name', 'createdBy:id,name'])
                ->visible()
                ->forRole($user)
                ->latest('publish_date')
                ->paginate(10)
                ->withQueryString(),
            'canManage' => $user->hasAnyRole(['super-admin', 'admin']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Notices/Create', $this->formProps());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('notices', 'public');
        }

        Notice::query()->create($data);

        return to_route('notices.index');
    }

    public function edit(Notice $notice): Response
    {
        return Inertia::render('Notices/Edit', [
            ...$this->formProps(),
            'notice' => $notice->load('classRoom:id,name'),
        ]);
    }

    public function update(Request $request, Notice $notice): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('attachment')) {
            if ($notice->attachment) {
                Storage::disk('public')->delete($notice->attachment);
            }

            $data['attachment'] = $request->file('attachment')->store('notices', 'public');
        }

        $notice->update($data);

        return to_route('notices.index');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        if ($notice->attachment) {
            Storage::disk('public')->delete($notice->attachment);
        }

        $notice->delete();

        return back();
    }

    /**
     * @return array{classes: mixed}
     */
    private function formProps(): array
    {
        return [
            'classes' => ClassRoom::query()->orderBy('grade_level')->orderBy('name')->get(['id', 'name', 'grade_level']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'audience' => ['required', 'in:all,students,staff,specific_class'],
            'class_id' => ['nullable', 'required_if:audience,specific_class', 'exists:school_classes,id'],
            'publish_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:publish_date'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ]);

        if ($data['audience'] !== 'specific_class') {
            $data['class_id'] = null;
        }

        return $data;
    }
}

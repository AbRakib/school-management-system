<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ExamTypeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('exams/Types/Index', ['types' => ExamType::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        ExamType::query()->create($this->validated($request));
        return back();
    }

    public function update(Request $request, ExamType $examType): RedirectResponse
    {
        $examType->update($this->validated($request, $examType));
        return back();
    }

    public function destroy(ExamType $examType): RedirectResponse
    {
        $examType->delete();
        return back();
    }

    private function validated(Request $request, ?ExamType $examType = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exam_types')->ignore($examType)],
            'weight_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
    }
}

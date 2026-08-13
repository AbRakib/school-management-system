<?php

namespace App\Http\Controllers;

use App\Models\GradingScale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradingScaleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('exams/GradingScale/Index', [
            'scales' => GradingScale::query()->orderByDesc('min_percentage')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        GradingScale::query()->create($this->validated($request));
        return back();
    }

    public function update(Request $request, GradingScale $gradingScale): RedirectResponse
    {
        $gradingScale->update($this->validated($request));
        return back();
    }

    public function destroy(GradingScale $gradingScale): RedirectResponse
    {
        $gradingScale->delete();
        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'grade_name' => ['required', 'string', 'max:20'],
            'min_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_point' => ['required', 'numeric', 'min:0', 'max:5'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

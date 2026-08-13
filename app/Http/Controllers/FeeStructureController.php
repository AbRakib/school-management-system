<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeeStructureController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('fees/Structure/Index', [
            'feeTypes' => FeeType::query()->orderBy('name')->get(),
            'structures' => FeeStructure::query()->with(['classRoom:id,name', 'feeType:id,name,is_recurring', 'academicSession:id,name'])->latest()->get(),
            'options' => [
                'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name']),
                'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(['id', 'name']),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FeeStructure::query()->updateOrCreate(
            $request->validate([
                'class_id' => ['required', 'exists:school_classes,id'],
                'fee_type_id' => ['required', 'exists:fee_types,id'],
                'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            ]),
            $request->validate([
                'amount' => ['required', 'numeric', 'min:0'],
                'due_day' => ['nullable', 'integer', 'min:1', 'max:28'],
            ]),
        );
        return back();
    }

    public function destroy(FeeStructure $feeStructure): RedirectResponse
    {
        $feeStructure->delete();
        return back();
    }
}

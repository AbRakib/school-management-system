<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeeDueController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('fees/Due', [
            'invoices' => FeeInvoice::query()
                ->with(['student.schoolClass:id,name', 'student.section:id,name'])
                ->where('due_amount', '>', 0)
                ->when($request->query('class_id'), fn ($q, $id) => $q->whereHas('student', fn ($q) => $q->where('class_id', $id)))
                ->when($request->query('section_id'), fn ($q, $id) => $q->whereHas('student', fn ($q) => $q->where('section_id', $id)))
                ->orderByDesc('due_amount')
                ->get(),
            'filters' => $request->only(['class_id', 'section_id']),
            'options' => [
                'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name']),
                'sections' => Section::query()->orderBy('name')->get(['id', 'name', 'school_class_id']),
            ],
        ]);
    }
}

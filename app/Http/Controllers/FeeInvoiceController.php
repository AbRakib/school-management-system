<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\AcademicSession;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeeInvoiceController extends Controller
{
    public function generateInvoices(Request $request, FeeService $feeService): RedirectResponse
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'month' => ['required', 'date_format:Y-m'],
        ]);
        $count = $feeService->generateMonthlyInvoices((int) $data['class_id'], (int) $data['academic_session_id'], $data['month']);
        return back()->with('message', "{$count} invoices generated.");
    }
}

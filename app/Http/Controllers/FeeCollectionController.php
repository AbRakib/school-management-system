<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeeCollectionController extends Controller
{
    public function search(Request $request): Response
    {
        $search = $request->query('search');
        $students = Student::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('admission_no', 'like', "%{$search}%"))
            ->limit(10)
            ->get(['id', 'name', 'admission_no', 'class_id', 'section_id']);
        $student = $request->query('student_id') ? Student::with(['feeInvoices.items.feeType', 'feeInvoices.payments'])->find($request->query('student_id')) : null;
        return Inertia::render('fees/Collect', ['students' => $students, 'selectedStudent' => $student, 'search' => $search]);
    }

    public function pay(Request $request, FeeInvoice $invoice, FeeService $feeService): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,bank,mobile_banking,card'],
            'transaction_ref' => ['nullable', 'string', 'max:255'],
        ]);
        $payment = $feeService->recordPayment($invoice->id, (float) $data['amount'], $data['payment_method'], $request->user()->id, $data['transaction_ref'] ?? null);
        return back()->with('receipt_payment_id', $payment->id);
    }
}

<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FeeService
{
    public function generateMonthlyInvoices(int $classId, int $academicSessionId, string $month): int
    {
        $structures = FeeStructure::query()
            ->with('feeType')
            ->where('class_id', $classId)
            ->where('academic_session_id', $academicSessionId)
            ->get();

        if ($structures->isEmpty()) {
            return 0;
        }

        $count = 0;
        foreach (Student::query()->where('class_id', $classId)->where('academic_session_id', $academicSessionId)->where('status', 'active')->get() as $student) {
            if (FeeInvoice::query()->where('student_id', $student->id)->where('academic_session_id', $academicSessionId)->where('month', $month)->exists()) {
                continue;
            }

            DB::transaction(function () use ($student, $academicSessionId, $month, $structures) {
                $total = $structures->sum('amount');
                $dueDay = $structures->pluck('due_day')->filter()->first() ?? 10;
                $invoice = FeeInvoice::query()->create([
                    'student_id' => $student->id,
                    'academic_session_id' => $academicSessionId,
                    'month' => $month,
                    'total_amount' => $total,
                    'discount' => 0,
                    'paid_amount' => 0,
                    'due_amount' => $total,
                    'status' => 'unpaid',
                    'due_date' => $month.'-'.str_pad((string) $dueDay, 2, '0', STR_PAD_LEFT),
                ]);

                foreach ($structures as $structure) {
                    $invoice->items()->create([
                        'fee_type_id' => $structure->fee_type_id,
                        'amount' => $structure->amount,
                    ]);
                }
            });
            $count++;
        }

        return $count;
    }

    public function recordPayment(int $invoiceId, float $amount, string $method, int $receivedBy, ?string $transactionRef = null): FeePayment
    {
        return DB::transaction(function () use ($invoiceId, $amount, $method, $receivedBy, $transactionRef) {
            $invoice = FeeInvoice::query()->lockForUpdate()->findOrFail($invoiceId);
            $amount = min($amount, (float) $invoice->due_amount);

            $payment = FeePayment::query()->create([
                'fee_invoice_id' => $invoice->id,
                'amount_paid' => $amount,
                'payment_method' => $method,
                'payment_date' => now()->toDateString(),
                'received_by' => $receivedBy,
                'transaction_ref' => $transactionRef,
            ]);

            $paid = (float) $invoice->paid_amount + $amount;
            $due = max(((float) $invoice->total_amount - (float) $invoice->discount) - $paid, 0);
            $invoice->update([
                'paid_amount' => $paid,
                'due_amount' => $due,
                'status' => $due <= 0 ? 'paid' : 'partial',
            ]);

            AccountTransaction::query()->create([
                'type' => 'income',
                'amount' => $amount,
                'source' => 'fee_payment',
                'reference_type' => FeePayment::class,
                'reference_id' => $payment->id,
                'description' => 'Fee payment for invoice #'.$invoice->id,
                'transaction_date' => now()->toDateString(),
                'created_by' => $receivedBy,
            ]);

            return $payment;
        });
    }
}

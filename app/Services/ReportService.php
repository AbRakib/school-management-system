<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\Exam;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        private readonly ResultService $resultService,
        private readonly AccountService $accountService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function attendanceReport(?int $classId, ?int $sectionId, string $startDate, string $endDate): array
    {
        $rows = Student::query()
            ->with(['schoolClass:id,name', 'section:id,name'])
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->when($sectionId, fn ($query) => $query->where('section_id', $sectionId))
            ->withCount([
                'attendances as marked_days' => fn ($query) => $query->whereBetween('date', [$startDate, $endDate]),
                'attendances as present_days' => fn ($query) => $query->whereBetween('date', [$startDate, $endDate])->whereIn('status', ['present', 'late', 'half_day']),
                'attendances as absent_days' => fn ($query) => $query->whereBetween('date', [$startDate, $endDate])->where('status', 'absent'),
                'attendances as late_days' => fn ($query) => $query->whereBetween('date', [$startDate, $endDate])->where('status', 'late'),
            ])
            ->orderByRaw('CAST(roll_no AS UNSIGNED), name')
            ->get()
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'admission_no' => $student->admission_no,
                'full_class_name' => $student->full_class_name,
                'marked_days' => (int) $student->marked_days,
                'present_days' => (int) $student->present_days,
                'absent_days' => (int) $student->absent_days,
                'late_days' => (int) $student->late_days,
                'percentage' => $student->marked_days ? round(($student->present_days / $student->marked_days) * 100, 2) : 0.0,
            ]);

        return [
            'rows' => $rows,
            'summary' => [
                'students' => $rows->count(),
                'class_average' => round((float) $rows->avg('percentage'), 2),
                'marked_days' => $rows->sum('marked_days'),
                'present_days' => $rows->sum('present_days'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function resultReport(?int $examId, ?int $classId): array
    {
        if (! $examId) {
            return ['rows' => collect(), 'summary' => $this->emptyResultSummary(), 'exam' => null];
        }

        $exam = Exam::query()->with(['subjects.results', 'subjects.subject'])->find($examId);
        if (! $exam) {
            return ['rows' => collect(), 'summary' => $this->emptyResultSummary(), 'exam' => null];
        }

        $students = Student::query()
            ->with(['schoolClass:id,name', 'section:id,name'])
            ->where('class_id', $classId ?: $exam->class_id)
            ->orderByRaw('CAST(roll_no AS UNSIGNED), name')
            ->get();

        $results = $exam->subjects
            ->flatMap(fn ($subject) => $subject->results->map(fn ($result) => [$subject, $result]))
            ->groupBy(fn ($pair) => $pair[1]->student_id);

        $rows = $students->map(function (Student $student) use ($exam, $results) {
            $studentResults = $results->get($student->id, collect());
            $fullMarks = (float) $exam->subjects->sum('full_marks');
            $totalMarks = (float) $studentResults->sum(fn ($pair) => (float) ($pair[1]->marks_obtained ?? 0));
            $percentage = $fullMarks > 0 ? round(($totalMarks / $fullMarks) * 100, 2) : 0.0;
            $failed = $exam->subjects->contains(function ($subject) use ($studentResults) {
                $pair = $studentResults->first(fn ($pair) => $pair[0]->id === $subject->id);
                $marks = $pair ? $pair[1]->marks_obtained : null;

                return $marks === null || (float) $marks < (float) $subject->pass_marks;
            });
            $grade = $this->resultService->calculateGrade($totalMarks, $fullMarks ?: 1);

            return [
                'id' => $student->id,
                'student_name' => $student->name,
                'admission_no' => $student->admission_no,
                'full_class_name' => $student->full_class_name,
                'total_marks' => round($totalMarks, 2),
                'full_marks' => round($fullMarks, 2),
                'percentage' => $percentage,
                'grade' => $failed ? 'F' : $grade['grade'],
                'status' => $failed ? 'fail' : 'pass',
                'rank' => 0,
            ];
        })->sortByDesc('percentage')->values();

        $ranked = $rows->map(function (array $row, int $index) {
            $row['rank'] = $index + 1;

            return $row;
        });

        return [
            'rows' => $ranked,
            'summary' => [
                'students' => $ranked->count(),
                'class_average' => round((float) $ranked->avg('percentage'), 2),
                'pass_rate' => $ranked->count() ? round(($ranked->where('status', 'pass')->count() / $ranked->count()) * 100, 2) : 0.0,
                'passed' => $ranked->where('status', 'pass')->count(),
                'failed' => $ranked->where('status', 'fail')->count(),
            ],
            'exam' => $exam->only(['id', 'title', 'class_id', 'status']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function feeCollectionReport(string $startDate, string $endDate, ?int $classId = null): array
    {
        $payments = FeePayment::query()
            ->with(['invoice.student:id,name,admission_no,class_id', 'invoice.items.feeType:id,name'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->when($classId, fn ($query) => $query->whereHas('invoice.student', fn ($query) => $query->where('class_id', $classId)))
            ->orderByDesc('payment_date')
            ->get();

        $transactions = $payments->map(fn (FeePayment $payment) => [
            'id' => $payment->id,
            'date' => $payment->payment_date,
            'student' => $payment->invoice?->student?->name,
            'admission_no' => $payment->invoice?->student?->admission_no,
            'invoice_id' => $payment->fee_invoice_id,
            'amount' => (float) $payment->amount_paid,
            'method' => $payment->payment_method,
            'reference' => $payment->transaction_ref,
        ]);

        $trend = $transactions
            ->groupBy('date')
            ->map(fn (Collection $items, string $date) => ['date' => $date, 'amount' => round((float) $items->sum('amount'), 2)])
            ->values();
        $trendMax = max((float) $trend->max('amount'), 1);

        return [
            'summary' => [
                'total_collected' => round((float) $payments->sum('amount_paid'), 2),
                'total_due' => round((float) Student::query()
                    ->when($classId, fn ($query) => $query->where('class_id', $classId))
                    ->withSum(['feeInvoices as due_sum' => fn ($query) => $query->where('due_amount', '>', 0)], 'due_amount')
                    ->get()
                    ->sum('due_sum'), 2),
                'transactions' => $payments->count(),
            ],
            'by_fee_type' => $this->feeTypeBreakdown($payments),
            'trend' => $trend->map(fn (array $item) => [
                ...$item,
                'chart_percentage' => round(((float) $item['amount'] / $trendMax) * 100, 2),
            ]),
            'transactions' => $transactions,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function financialReport(string $startDate, string $endDate): array
    {
        $transactions = AccountTransaction::query()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();

        $income = (float) $transactions->where('type', 'income')->sum('amount');
        $expense = (float) $transactions->where('type', 'expense')->sum('amount');

        $incomeByCategory = $transactions
            ->where('type', 'income')
            ->groupBy('source')
            ->map(fn (Collection $items, string $source) => ['category' => $source, 'amount' => round((float) $items->sum('amount'), 2)])
            ->values();
        $expenseByCategory = Expense::query()
            ->with('category:id,name')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn (Expense $expense) => $expense->category?->name ?? 'Uncategorized')
            ->map(fn (Collection $items, string $category) => ['category' => $category, 'amount' => round((float) $items->sum('amount'), 2)])
            ->values();
        $categoryMax = max((float) $incomeByCategory->concat($expenseByCategory)->max('amount'), 1);

        return [
            'summary' => [
                'income' => round($income, 2),
                'expense' => round($expense, 2),
                'net_change' => round($income - $expense, 2),
                'current_balance' => $this->accountService->getCurrentBalance(),
            ],
            'income_by_category' => $incomeByCategory->map(fn (array $item) => [
                ...$item,
                'chart_percentage' => round(((float) $item['amount'] / $categoryMax) * 100, 2),
            ]),
            'expense_by_category' => $expenseByCategory->map(fn (array $item) => [
                ...$item,
                'chart_percentage' => round(((float) $item['amount'] / $categoryMax) * 100, 2),
            ]),
            'trend' => collect($this->accountService->getBalanceHistory($startDate, $endDate))
                ->map(function (array $item) {
                    $max = max((float) $item['income'], (float) $item['expense'], 1);

                    return [
                        ...$item,
                        'income_chart_percentage' => round(((float) $item['income'] / $max) * 100, 2),
                        'expense_chart_percentage' => round(((float) $item['expense'] / $max) * 100, 2),
                    ];
                }),
            'transactions' => $transactions->map(fn (AccountTransaction $transaction) => [
                'id' => $transaction->id,
                'date' => $transaction->transaction_date,
                'type' => $transaction->type,
                'source' => $transaction->source,
                'description' => $transaction->description,
                'amount' => (float) $transaction->amount,
            ]),
        ];
    }

    /**
     * @param  Collection<int, FeePayment>  $payments
     * @return Collection<int, array{fee_type: string, amount: float}>
     */
    private function feeTypeBreakdown(Collection $payments): Collection
    {
        $breakdown = collect();

        foreach ($payments as $payment) {
            $items = $payment->invoice?->items ?? collect();
            $itemTotal = (float) $items->sum('amount');

            foreach ($items as $item) {
                $ratio = $itemTotal > 0 ? (float) $item->amount / $itemTotal : 0;
                $breakdown->push([
                    'fee_type' => $item->feeType?->name ?? 'Unknown',
                    'amount' => round((float) $payment->amount_paid * $ratio, 2),
                ]);
            }
        }

        return $breakdown
            ->groupBy('fee_type')
            ->map(fn (Collection $items, string $feeType) => ['fee_type' => $feeType, 'amount' => round((float) $items->sum('amount'), 2)])
            ->values();
    }

    /**
     * @return array<string, float|int>
     */
    private function emptyResultSummary(): array
    {
        return [
            'students' => 0,
            'class_average' => 0.0,
            'pass_rate' => 0.0,
            'passed' => 0,
            'failed' => 0,
        ];
    }
}

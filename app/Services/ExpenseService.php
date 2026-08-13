<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * @param array<string, mixed> $data
     */
    public function recordExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::query()->create($data);

            AccountTransaction::query()->create([
                'type' => 'expense',
                'amount' => $expense->amount,
                'source' => 'expense',
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'description' => $expense->description,
                'transaction_date' => $expense->expense_date,
                'created_by' => $expense->paid_by,
            ]);

            return $expense;
        });
    }
}

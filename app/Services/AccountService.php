<?php

namespace App\Services;

use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function getCurrentBalance(): float
    {
        $income = AccountTransaction::query()->where('type', 'income')->sum('amount');
        $expense = AccountTransaction::query()->where('type', 'expense')->sum('amount');

        return round((float) $income - (float) $expense, 2);
    }

    public function getBalanceHistory(string $startDate, string $endDate): array
    {
        return AccountTransaction::query()
            ->selectRaw('transaction_date, SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income, SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('transaction_date')
            ->orderBy('transaction_date')
            ->get()
            ->toArray();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function balance(Request $request, AccountService $accountService): Response
    {
        $start = $request->query('start_date', now()->subDays(30)->toDateString());
        $end = $request->query('end_date', now()->toDateString());
        return Inertia::render('accounts/Balance', [
            'balance' => $accountService->getCurrentBalance(),
            'history' => $accountService->getBalanceHistory($start, $end),
            'transactions' => $this->queryTransactions($request)->limit(20)->get(),
            'filters' => ['start_date' => $start, 'end_date' => $end],
        ]);
    }

    public function transactions(Request $request): Response
    {
        return Inertia::render('accounts/Balance', [
            'balance' => app(AccountService::class)->getCurrentBalance(),
            'history' => [],
            'transactions' => $this->queryTransactions($request)->paginate(50)->withQueryString(),
            'filters' => $request->only(['type', 'start_date', 'end_date']),
        ]);
    }

    private function queryTransactions(Request $request)
    {
        return AccountTransaction::query()
            ->with('createdBy:id,name')
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('start_date'), fn ($q, $date) => $q->whereDate('transaction_date', '>=', $date))
            ->when($request->query('end_date'), fn ($q, $date) => $q->whereDate('transaction_date', '<=', $date))
            ->latest();
    }
}

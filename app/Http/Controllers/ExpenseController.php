<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('fees/Expenses/Index', [
            'expenses' => Expense::query()->with(['category:id,name', 'paidBy:id,name'])
                ->when($request->query('category_id'), fn ($q, $id) => $q->where('expense_category_id', $id))
                ->when($request->query('start_date'), fn ($q, $date) => $q->whereDate('expense_date', '>=', $date))
                ->when($request->query('end_date'), fn ($q, $date) => $q->whereDate('expense_date', '<=', $date))
                ->latest()->get(),
            'categories' => ExpenseCategory::query()->orderBy('name')->get(),
            'filters' => $request->only(['category_id', 'start_date', 'end_date']),
        ]);
    }

    public function store(Request $request, ExpenseService $expenseService): RedirectResponse
    {
        $data = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string', 'max:1000'],
            'expense_date' => ['required', 'date'],
        ]);
        $data['paid_by'] = $request->user()->id;
        $expenseService->recordExpense($data);
        return back();
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();
        return back();
    }
}

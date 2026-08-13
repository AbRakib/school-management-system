<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        ExpenseCategory::query()->create($request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('expense_categories')]]));
        return back();
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->update($request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('expense_categories')->ignore($expenseCategory)]]));
        return back();
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->delete();
        return back();
    }
}

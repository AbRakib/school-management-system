<?php

namespace App\Http\Controllers;

use App\Models\BookCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        BookCategory::query()->create($request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('book_categories')]]));
        return back();
    }

    public function update(Request $request, BookCategory $bookCategory): RedirectResponse
    {
        $bookCategory->update($request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('book_categories')->ignore($bookCategory)]]));
        return back();
    }

    public function destroy(BookCategory $bookCategory): RedirectResponse
    {
        $bookCategory->delete();
        return back();
    }
}

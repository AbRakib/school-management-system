<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        return Inertia::render('library/Books/Index', [
            'books' => Book::query()
                ->with('category:id,name')
                ->when($search, fn ($q) => $q->where(fn ($q) => $q
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")))
                ->orderBy('title')
                ->get(),
            'categories' => BookCategory::query()->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Book::query()->create($this->validated($request));
        return back();
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validated($request, $book);
        $book->update($data);
        return back();
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();
        return back();
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        $data = $request->validate([
            'book_category_id' => ['required', 'exists:book_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', Rule::unique('books')->ignore($book)],
            'publisher' => ['nullable', 'string', 'max:255'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'available_copies' => ['nullable', 'integer', 'min:0'],
            'shelf_location' => ['nullable', 'string', 'max:255'],
        ]);
        $data['available_copies'] ??= $data['total_copies'];
        return $data;
    }
}

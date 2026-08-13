<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Staff;
use App\Models\Student;
use App\Services\BookIssueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class BookIssueController extends Controller
{
    public function issue(Request $request): Response
    {
        $bookSearch = $request->query('book_search');
        $borrowerSearch = $request->query('borrower_search');
        $borrowerType = $request->query('borrower_type', 'student');

        return Inertia::render('library/Issue', [
            'books' => Book::query()->when($bookSearch, fn ($q) => $q->where(fn ($q) => $q
                ->where('title', 'like', "%{$bookSearch}%")
                ->orWhere('author', 'like', "%{$bookSearch}%")
                ->orWhere('isbn', 'like', "%{$bookSearch}%")))->limit(10)->get(),
            'borrowers' => $borrowerType === 'staff'
                ? Staff::query()->when($borrowerSearch, fn ($q) => $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$borrowerSearch}%")
                    ->orWhere('staff_id', 'like', "%{$borrowerSearch}%")))->limit(10)->get(['id', 'name', 'staff_id', 'phone', 'email'])
                : Student::query()->when($borrowerSearch, fn ($q) => $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$borrowerSearch}%")
                    ->orWhere('admission_no', 'like', "%{$borrowerSearch}%")))->limit(10)->get(['id', 'name', 'admission_no']),
            'filters' => compact('bookSearch', 'borrowerSearch', 'borrowerType'),
        ]);
    }

    public function store(Request $request, BookIssueService $service): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'borrower_type' => ['required', 'in:student,staff'],
            'borrower_id' => ['required', 'integer'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
        ]);

        $borrower = $data['borrower_type'] === 'staff'
            ? Staff::query()->findOrFail($data['borrower_id'])
            : Student::query()->findOrFail($data['borrower_id']);

        try {
            $service->issueBook((int) $data['book_id'], $borrower, $data['issue_date'], $data['due_date'], $request->user()->id);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['book_id' => $exception->getMessage()]);
        }

        return back();
    }

    public function returns(Request $request): Response
    {
        $search = $request->query('search');
        return Inertia::render('library/Returns', [
            'issues' => BookIssue::query()
                ->with(['book', 'borrower'])
                ->whereIn('status', ['issued', 'overdue'])
                ->when($search, fn ($q) => $q->where(fn ($q) => $q
                    ->whereHas('book', fn ($q) => $q->where('title', 'like', "%{$search}%"))
                    ->orWhereHasMorph('borrower', [Student::class, Staff::class], fn ($q) => $q->where('name', 'like', "%{$search}%"))))
                ->latest()
                ->get(),
            'search' => $search,
        ]);
    }

    public function return(BookIssue $issue, BookIssueService $service): RedirectResponse
    {
        $service->returnBook($issue);
        return back();
    }

    public function due(): Response
    {
        return Inertia::render('library/Due', [
            'issues' => BookIssue::query()
                ->with(['book', 'borrower'])
                ->whereIn('status', ['issued', 'overdue'])
                ->whereDate('due_date', '<', now()->toDateString())
                ->orderBy('due_date')
                ->get(),
            'fineRate' => 10,
        ]);
    }
}

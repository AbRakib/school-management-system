<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookIssue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookIssueService
{
    public function issueBook(int $bookId, Model $borrower, string $issueDate, string $dueDate, int $issuedBy): BookIssue
    {
        return DB::transaction(function () use ($bookId, $borrower, $issueDate, $dueDate, $issuedBy) {
            $book = Book::query()->lockForUpdate()->findOrFail($bookId);

            if ($book->available_copies <= 0) {
                throw new RuntimeException('No available copies for this book.');
            }

            $book->decrement('available_copies');

            return BookIssue::query()->create([
                'book_id' => $book->id,
                'borrower_type' => $borrower::class,
                'borrower_id' => $borrower->getKey(),
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'status' => 'issued',
                'fine_amount' => 0,
                'issued_by' => $issuedBy,
            ]);
        });
    }

    public function returnBook(BookIssue $issue, float $perDayFine = 10): BookIssue
    {
        return DB::transaction(function () use ($issue, $perDayFine) {
            $issue = BookIssue::query()->lockForUpdate()->findOrFail($issue->id);

            if ($issue->status === 'returned') {
                return $issue;
            }

            $daysOverdue = max(now()->startOfDay()->diffInDays($issue->due_date, false) * -1, 0);
            $fine = $daysOverdue * $perDayFine;

            $issue->update([
                'return_date' => now()->toDateString(),
                'status' => 'returned',
                'fine_amount' => $fine,
            ]);

            $issue->book()->lockForUpdate()->first()->increment('available_copies');

            return $issue;
        });
    }
}

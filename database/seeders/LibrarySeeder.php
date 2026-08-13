<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Services\BookIssueService;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['Science', 'Literature', 'History', 'Mathematics', 'Reference'])
            ->map(fn ($name) => BookCategory::query()->updateOrCreate(['name' => $name]));

        foreach (range(1, 30) as $index) {
            $total = rand(2, 8);
            Book::query()->updateOrCreate([
                'isbn' => 'LIB-ISBN-'.str_pad((string) $index, 5, '0', STR_PAD_LEFT),
            ], [
                'book_category_id' => $categories->random()->id,
                'title' => fake()->sentence(3),
                'author' => fake()->name(),
                'publisher' => fake()->company(),
                'total_copies' => $total,
                'available_copies' => $total,
                'shelf_location' => 'S-'.$index,
            ]);
        }

        $user = User::query()->first();
        if (! $user) {
            return;
        }

        if (BookIssue::query()->exists()) {
            return;
        }

        $service = app(BookIssueService::class);
        $borrowers = collect(Student::query()->take(4)->get())->merge(Staff::query()->take(2)->get());
        foreach ($borrowers as $i => $borrower) {
            $book = Book::query()->where('available_copies', '>', 0)->inRandomOrder()->first();
            if (! $book) {
                continue;
            }

            $issue = $service->issueBook(
                $book->id,
                $borrower,
                now()->subDays(10 + $i)->toDateString(),
                now()->addDays($i % 2 === 0 ? -2 : 7)->toDateString(),
                $user->id,
            );

            if ($issue->due_date->isPast()) {
                $issue->update(['status' => 'overdue']);
            }
        }
    }
}

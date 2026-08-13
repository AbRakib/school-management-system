<?php

namespace App\Console\Commands;

use App\Models\BookIssue;
use Illuminate\Console\Command;

class FlagOverdueBookIssues extends Command
{
    protected $signature = 'library:flag-overdue';

    protected $description = 'Flag issued library books as overdue after their due date.';

    public function handle(): int
    {
        $count = BookIssue::query()
            ->where('status', 'issued')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $this->info("Flagged {$count} overdue issue(s).");

        return self::SUCCESS;
    }
}

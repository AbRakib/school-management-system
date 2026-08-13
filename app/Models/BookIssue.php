<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['book_id', 'borrower_type', 'borrower_id', 'issue_date', 'due_date', 'return_date', 'status', 'fine_amount', 'issued_by'])]
class BookIssue extends Model
{
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function borrower(): MorphTo
    {
        return $this->morphTo();
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    protected function casts(): array
    {
        return [
            'issue_date' => 'date:Y-m-d',
            'due_date' => 'date:Y-m-d',
            'return_date' => 'date:Y-m-d',
            'fine_amount' => 'decimal:2',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['expense_category_id', 'amount', 'description', 'expense_date', 'paid_by'])]
class Expense extends Model
{
    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
    public function paidBy(): BelongsTo { return $this->belongsTo(User::class, 'paid_by'); }
}

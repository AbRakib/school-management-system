<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'amount', 'source', 'reference_type', 'reference_id', 'description', 'transaction_date', 'created_by'])]
class AccountTransaction extends Model
{
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}

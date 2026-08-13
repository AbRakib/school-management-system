<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['student_id', 'academic_session_id', 'month', 'total_amount', 'discount', 'paid_amount', 'due_amount', 'status', 'due_date'])]
class FeeInvoice extends Model
{
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
    public function items(): HasMany { return $this->hasMany(FeeInvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(FeePayment::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['class_id', 'fee_type_id', 'academic_session_id', 'amount', 'due_day'])]
class FeeStructure extends Model
{
    public function classRoom(): BelongsTo { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function feeType(): BelongsTo { return $this->belongsTo(FeeType::class); }
    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
}

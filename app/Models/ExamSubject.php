<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['exam_id', 'subject_id', 'exam_date', 'full_marks', 'pass_marks'])]
class ExamSubject extends Model
{
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function results(): HasMany { return $this->hasMany(ExamResult::class); }

    protected function casts(): array
    {
        return ['exam_date' => 'date:Y-m-d', 'full_marks' => 'decimal:2', 'pass_marks' => 'decimal:2'];
    }
}

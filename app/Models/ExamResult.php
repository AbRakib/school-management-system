<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['exam_id', 'exam_subject_id', 'student_id', 'marks_obtained', 'grade', 'grade_point', 'remarks', 'entered_by'])]
class ExamResult extends Model
{
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function examSubject(): BelongsTo { return $this->belongsTo(ExamSubject::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function enteredBy(): BelongsTo { return $this->belongsTo(User::class, 'entered_by'); }

    protected function casts(): array
    {
        return ['marks_obtained' => 'decimal:2', 'grade_point' => 'decimal:2'];
    }
}

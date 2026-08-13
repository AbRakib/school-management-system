<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['exam_type_id', 'academic_session_id', 'class_id', 'title', 'start_date', 'end_date', 'status'])]
class Exam extends Model
{
    public function examType(): BelongsTo { return $this->belongsTo(ExamType::class); }
    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
    public function classRoom(): BelongsTo { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function subjects(): HasMany { return $this->hasMany(ExamSubject::class); }
    public function results(): HasMany { return $this->hasMany(ExamResult::class); }

    protected function casts(): array
    {
        return ['start_date' => 'date:Y-m-d', 'end_date' => 'date:Y-m-d'];
    }
}

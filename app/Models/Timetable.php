<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['class_id', 'section_id', 'academic_session_id', 'day', 'timetable_period_id', 'subject_id', 'staff_id'])]
class Timetable extends Model
{
    public function classRoom(): BelongsTo { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
    public function period(): BelongsTo { return $this->belongsTo(TimetablePeriod::class, 'timetable_period_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}

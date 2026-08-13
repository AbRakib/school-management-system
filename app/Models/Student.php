<?php

namespace App\Models;

use App\Models\Concerns\HasProfileImage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'admission_no',
    'admission_number',
    'class_id',
    'section_id',
    'academic_session_id',
    'roll_no',
    'name',
    'dob',
    'image',
    'photo',
    'date_of_birth',
    'gender',
    'blood_group',
    'religion',
    'school_class_id',
    'guardian_id',
    'student_category_id',
    'phone',
    'admission_date',
    'address',
    'present_address',
    'permanent_address',
    'status',
])]
class Student extends Model
{
    use HasProfileImage, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $appends = ['full_class_name', 'image_url'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * @return BelongsTo<ClassRoom, $this>
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @return BelongsTo<AcademicSession, $this>
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return BelongsTo<StudentCategory, $this>
     */
    public function studentCategory(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class);
    }

    /**
     * @return HasMany<StudentAttendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function attendancePercentage(string $startDate, string $endDate): float
    {
        $total = $this->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        if ($total === 0) {
            return 0.0;
        }

        $present = $this->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['present', 'late', 'half_day'])
            ->count();

        return round(($present / $total) * 100, 2);
    }

    protected function totalDue(): Attribute
    {
        return Attribute::get(fn (): float => (float) $this->feeInvoices()->where('due_amount', '>', 0)->sum('due_amount'));
    }

    public function feeInvoices(): HasMany
    {
        return $this->hasMany(FeeInvoice::class);
    }

    public function currentTransport(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentTransport::class)->latestOfMany();
    }

    /**
     * @return Attribute<string, never>
     */
    protected function fullClassName(): Attribute
    {
        return Attribute::get(function (): string {
            $class = $this->schoolClass?->name ?? 'Class not assigned';
            $section = $this->section?->name;

            return $section ? "{$class} - Section {$section}" : $class;
        });
    }

    /**
     * @return Attribute<non-falsy-string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn (): string => ($this->photo || $this->image)
                ? '/storage/'.($this->photo ?? $this->image)
                : $this->defaultProfileImage(),
        );
    }

    /**
     * @return non-falsy-string
     */
    protected function defaultProfileImage(): string
    {
        return '/images/default-student.png';
    }

    protected function casts(): array
    {
        return [
            'dob' => 'date:Y-m-d',
            'date_of_birth' => 'date:Y-m-d',
            'admission_date' => 'date:Y-m-d',
        ];
    }
}

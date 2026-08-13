<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'staff_id',
    'department_id',
    'designation_id',
    'name',
    'phone',
    'email',
    'dob',
    'gender',
    'joining_date',
    'qualification',
    'address',
    'photo',
    'salary',
    'status',
])]
class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';

    /**
     * @var list<string>
     */
    protected $appends = ['photo_url'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Designation, $this>
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * @return HasMany<ClassTeacherSubject, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ClassTeacherSubject::class);
    }

    /**
     * @return HasMany<StaffAttendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * @param Builder<Staff> $query
     * @return Builder<Staff>
     */
    public function scopeTeachers(Builder $query): Builder
    {
        return $query->whereHas('designation', fn (Builder $query) => $query->where('is_teaching_staff', true));
    }

    /**
     * @return Attribute<non-falsy-string, never>
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->photo ? '/storage/'.$this->photo : '/images/default-teacher.png');
    }

    protected function casts(): array
    {
        return [
            'dob' => 'date:Y-m-d',
            'joining_date' => 'date:Y-m-d',
            'salary' => 'decimal:2',
        ];
    }
}

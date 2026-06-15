<?php

namespace App\Models;

use App\Models\Concerns\HasProfileImage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'admission_number',
    'name',
    'image',
    'date_of_birth',
    'gender',
    'school_class_id',
    'guardian_id',
    'phone',
    'admission_date',
    'address',
])]
class Student extends Model
{
    use HasProfileImage;

    /**
     * @var list<string>
     */
    protected $appends = ['image_url'];

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
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
            'date_of_birth' => 'date:Y-m-d',
            'admission_date' => 'date:Y-m-d',
        ];
    }
}

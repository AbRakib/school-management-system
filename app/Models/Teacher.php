<?php

namespace App\Models;

use App\Models\Concerns\HasProfileImage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'image',
    'email',
    'phone',
    'designation',
    'subject',
    'joining_date',
    'address',
])]
class Teacher extends Model
{
    use HasProfileImage;

    /**
     * @var list<string>
     */
    protected $appends = ['image_url'];

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'class_teacher_id');
    }

    /**
     * @return non-falsy-string
     */
    protected function defaultProfileImage(): string
    {
        return '/images/default-teacher.png';
    }

    protected function casts(): array
    {
        return [
            'joining_date' => 'date:Y-m-d',
        ];
    }
}

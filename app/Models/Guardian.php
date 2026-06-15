<?php

namespace App\Models;

use App\Models\Concerns\HasProfileImage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'image',
    'relationship',
    'phone',
    'email',
    'occupation',
    'address',
])]
class Guardian extends Model
{
    use HasProfileImage;

    /**
     * @var list<string>
     */
    protected $appends = ['image_url'];

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * @return non-falsy-string
     */
    protected function defaultProfileImage(): string
    {
        return '/images/default-parent.png';
    }
}

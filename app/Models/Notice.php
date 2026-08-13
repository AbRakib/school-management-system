<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'description',
    'audience',
    'class_id',
    'publish_date',
    'expiry_date',
    'attachment',
    'created_by',
])]
class Notice extends Model
{
    /**
     * @return BelongsTo<ClassRoom, $this>
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @param  Builder<Notice>  $query
     * @return Builder<Notice>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->whereDate('publish_date', '<=', now()->toDateString())
            ->where(fn (Builder $query) => $query
                ->whereNull('expiry_date')
                ->orWhereDate('expiry_date', '>=', now()->toDateString()));
    }

    /**
     * @param  Builder<Notice>  $query
     * @return Builder<Notice>
     */
    public function scopeForRole(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return $query;
        }

        $studentClassId = Student::query()
            ->where('user_id', $user->id)
            ->value('class_id');

        return $query->where(function (Builder $query) use ($user, $studentClassId) {
            $query->where('audience', 'all');

            if ($user->hasAnyRole(['student', 'parent'])) {
                $query->orWhere('audience', 'students');

                if ($studentClassId) {
                    $query->orWhere(fn (Builder $query) => $query
                        ->where('audience', 'specific_class')
                        ->where('class_id', $studentClassId));
                }
            }

            if ($user->hasAnyRole(['teacher', 'staff', 'accountant'])) {
                $query->orWhere('audience', 'staff');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'publish_date' => 'date:Y-m-d',
            'expiry_date' => 'date:Y-m-d',
        ];
    }
}

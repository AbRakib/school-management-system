<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'start_time', 'end_time'])]
class TimetablePeriod extends Model
{
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}

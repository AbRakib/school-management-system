<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['grade_name', 'min_percentage', 'max_percentage', 'grade_point', 'remarks'])]
class GradingScale extends Model
{
    protected function casts(): array
    {
        return ['min_percentage' => 'decimal:2', 'max_percentage' => 'decimal:2', 'grade_point' => 'decimal:2'];
    }
}

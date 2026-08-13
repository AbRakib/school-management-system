<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'is_recurring'])]
class FeeType extends Model
{
    protected function casts(): array { return ['is_recurring' => 'boolean']; }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'vehicle_number',
    'type',
    'capacity',
    'driver_name',
    'driver_phone',
    'status',
])]
class Vehicle extends Model
{
    /**
     * @return HasMany<TransportRoute, $this>
     */
    public function transportRoutes(): HasMany
    {
        return $this->hasMany(TransportRoute::class);
    }
}

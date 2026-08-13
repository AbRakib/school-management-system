<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'vehicle_id',
    'fare_amount',
])]
class TransportRoute extends Model
{
    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return HasMany<TransportStop, $this>
     */
    public function stops(): HasMany
    {
        return $this->hasMany(TransportStop::class)->orderBy('order');
    }

    /**
     * @return HasMany<StudentTransport, $this>
     */
    public function studentTransports(): HasMany
    {
        return $this->hasMany(StudentTransport::class);
    }
}

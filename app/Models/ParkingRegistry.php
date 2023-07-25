<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingRegistry extends Model
{
    protected $fillable = [
        'vehicle_id',
        'plate_number',
        'entry_time',
        'exit_time'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
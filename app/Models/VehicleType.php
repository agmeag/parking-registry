<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = ['name', 'key', 'parking_cost_per_minute'];

    // Vehicles Relationship
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
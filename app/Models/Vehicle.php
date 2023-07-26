<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['vehicle_type_id', 'plate_number', 'accumulated_time'];

    // Vehicle Type Relationship
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    // Parking Registries Relationship
    public function parking_registries()
    {
        return $this->hasMany(ParkingRegistry::class);
    }

    // Accessor for is_official attribute
    public function getIsOfficialAttribute()
    {
        return $this->vehicleType->key === 'official';
    }

    // Accessor for is_resident attribute
    public function getIsResidentAttribute()
    {
        return $this->vehicleType->key === 'resident';
    }
}
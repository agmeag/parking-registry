<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    public function registerVehicleAsOfficial(VehicleRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($vehicle = Vehicle::wherePlateNumber($request->plate_number)->first()) {
                $vehicle->update(['vehicle_type_id' => VehicleType::where('key', 'official')->first()->id]);
            } else {
                // Create the vehicle and associate it with the official vehicle type
                $vehicle = Vehicle::create([
                    'plate_number' => $request->plate_number,
                    'vehicle_type_id' => VehicleType::where('key', 'official')->first()->id,
                ]);
            }

            DB::commit();

            $result = [
                "plate_number" => $vehicle->plate_number,
                "vehicle_type" => $vehicle->vehicleType->name,
            ];

            // Return a response indicating success
            return $this->success($result, 'Vehiculo oficial registrado correctamente', 201);
        } catch (\Throwable $th) {
            Log::error("ERROR | Message: {$th->getMessage()}, Line: {$th->getLine()}, File: {$th->getFile()}");
            DB::rollBack();

            return $this->error('Vehiculo oficial no pudo ser registrado', 400);
        }
    }

    public function registerVehicleAsResident(VehicleRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($vehicle = Vehicle::wherePlateNumber($request->plate_number)->first()) {
                $vehicle->update(['vehicle_type_id' => VehicleType::where('key', 'resident')->first()->id]);
            } else {
                // Create the vehicle and associate it with the resident vehicle type
                $vehicle = Vehicle::create([
                    'plate_number' => $request->plate_number,
                    'vehicle_type_id' => VehicleType::where('key', 'resident')->first()->id,
                ]);
            }

            DB::commit();

            $result = [
                "plate_number" => $vehicle->plate_number,
                "vehicle_type" => $vehicle->vehicleType->name,
            ];

            // Return a response indicating success
            return $this->success($result, 'Vehiculo residente registrado correctamente', 201);
        } catch (\Throwable $th) {
            Log::error("ERROR | Message: {$th->getMessage()}, Line: {$th->getLine()}, File: {$th->getFile()}");
            DB::rollBack();

            return $this->error('Vehiculo residente no pudo ser registrado', 400);
        }
    }
}
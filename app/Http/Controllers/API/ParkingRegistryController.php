<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\ParkingRegistry;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParkingRegistryController extends Controller
{

    // Register entry
    public function registerEntry(VehicleRequest $request)
    {
        try {
            DB::beginTransaction();

            if (ParkingRegistry::wherePlateNumber($request->plate_number)->whereNull('exit_time')->first()) {
                return $this->error('Vehiculo placa ' . $request->plate_number . ' no posee registro de salida en su entrada anterior', 400);
            }

            // Create a new parking registry
            $registry = ParkingRegistry::create([
                'plate_number' => $request->plate_number,
                'entry_time' => now(),
                'vehicle_id' => optional(Vehicle::where('plate_number', $request->plate_number)->first())->id,
            ]);

            DB::commit();

            $result = [
                "plate_number" => $registry->plate_number,
                "entry_time" => $registry->entry_time
            ];
            return $this->success($result, 'Hora de entrada registrada correctamente');
        } catch (\Throwable $th) {
            Log::error("ERROR | Message: {$th->getMessage()}, Line: {$th->getLine()}, File: {$th->getFile()}");
            DB::rollBack();

            return $this->error('Hora de entrada no pudo ser registrada', 500);
        }
    }

    // Register exit
    public function registerExit(VehicleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Find the latest active parking registry for the vehicle
            if (!$registry = ParkingRegistry::where('plate_number', $request->plate_number)
                ->whereNull('exit_time')
                ->latest('entry_time')
                ->first()) {
                return $this->error("El vehiculo con placa" . $request->plate_number . "no posee registro de entrada");
            }

            $registry->update(['exit_time' => now()]);

            // Calculate elapsed time
            $elapsedMinutes = now()->parse($registry->entry_time)->diffInMinutes($registry->exit_time);

            $result = null;
            // If the vehicle is registered as official or resident, update the accumulated_time
            if ($registry->vehicle && ($registry->vehicle->is_official || $registry->vehicle->is_resident)) {
                $registry->vehicle->update(['accumulated_time' => $registry->vehicle->accumulated_time + $elapsedMinutes]);
            } else {
                $result = [
                    "plate_number" => $registry->plate_number,
                    "to_pay" => $elapsedMinutes * VehicleType::where('key', 'non-registered')->first()->parking_cost_per_minute,
                    "elapsed_time" => $elapsedMinutes,
                ];
            }

            DB::commit();

            return $this->success($result, 'Hora de salida registrada correctamente');
        } catch (\Throwable $th) {
            Log::error("ERROR | Message: {$th->getMessage()}, Line: {$th->getLine()}, File: {$th->getFile()}");
            DB::rollBack();

            return $this->error('Salida no pudo ser registrada', 500);
        }
    }

    public function monthStart()
    {
        Vehicle::whereHas('vehicleType', function ($query) {
            $query->whereIn('key', ['resident', 'official']);
        })->update(['accumulated_time' => 0]);

        ParkingRegistry::whereHas('vehicle.vehicleType', function ($query) {
            $query->where('key', 'official');
        })->delete();
    }
}
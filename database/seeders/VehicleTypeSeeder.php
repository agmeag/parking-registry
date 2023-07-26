<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicleTypes = [
            [
                'name' => 'Oficial',
                'key' => 'official',
                'parking_cost_per_minute' => 0
            ],
            [
                'name' => 'Residente',
                'key' => 'resident',
                'parking_cost_per_minute' => 0
            ],
            [
                'name' => 'No registrado',
                'key' => 'non-registered',
                'parking_cost_per_minute' => 0.5
            ]
        ];

        foreach ($vehicleTypes as $type) {
            $type = (object) $type;
            if (VehicleType::whereKey($type->key)->doesntExist()) {
                VehicleType::create((array) $type);
            }
        }
    }
}
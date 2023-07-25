<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\ParkingRegistryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('status',  function () {
    return response()->json(['message' => 'Working project.']);
});
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'getUser']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('vehicle')->group(function () {
        Route::prefix('register')->group(function () {
            Route::post('official', [VehicleController::class, 'registerVehicleAsOfficial']);
            Route::post('resident', [VehicleController::class, 'registerVehicleAsResident']);
        });
    });

    Route::prefix('parking')->group(function () {
        Route::post('entry', [ParkingRegistryController::class, 'registerEntry']);
        Route::post('exit', [ParkingRegistryController::class, 'registerExit']);
        Route::get('monthstart', [ParkingRegistryController::class, 'monthStart']);
    });
});
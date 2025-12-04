<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReferenceController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::prefix('references')->group(function () {
    Route::get('/car-brands', [ReferenceController::class, 'carBrands']);
    Route::get('/car-models', [ReferenceController::class, 'carModels']);
    Route::get('/car-models/{brandId}', [ReferenceController::class, 'carModels']);
    Route::get('/fuel-types', [ReferenceController::class, 'fuelTypes']);
    Route::get('/engine-types', [ReferenceController::class, 'engineTypes']);
    Route::get('/gearbox-types', [ReferenceController::class, 'gearboxTypes']);
    Route::get('/drive-unit-types', [ReferenceController::class, 'driveUnitTypes']);
    Route::get('/booking-statuses', [ReferenceController::class, 'bookingStatuses']);
    Route::get('/services', [ReferenceController::class, 'services']);
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::get('/{userId}', [BookingController::class, 'getUserBookings']);
});

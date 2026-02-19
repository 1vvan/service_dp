<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CarsController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ServiceController;
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

Route::prefix('clients')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{client}', [ClientController::class, 'show']);
        Route::put('/{client}', [ClientController::class, 'update']);
        Route::delete('/{client}', [ClientController::class, 'destroy']);
    });
    Route::get('/{client}/bookings', [BookingController::class, 'getUserBookings']);
    Route::get('/{client}/cars', [CarsController::class, 'getUserCars']);
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::get('/upcoming', [BookingController::class, 'getUpcomingBookings']);
    Route::get('/{booking}', [BookingController::class, 'getBooking']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{client}/create', [BookingController::class, 'createBooking']);
        Route::post('/{booking}/update', [BookingController::class, 'updateBooking']);
        Route::post('/calculate-price', [BookingController::class, 'calculatePrice']);
        Route::get('/{booking}/receipt', [BookingController::class, 'generateReceipt']);
        Route::post('/{booking}/payment/create-session', [PaymentController::class, 'createCheckoutSession']);
        Route::post('/{booking}/confirm', [BookingController::class, 'confirmBooking']);
    });
});

Route::prefix('payments')->group(function () {
    Route::post('/webhook/stripe', [PaymentController::class, 'handleWebhook']);
});

Route::prefix('cars')->group(function () {
    Route::get('/', [CarsController::class, 'index']);
    Route::get('/{car}', [CarsController::class, 'getCar']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{client}/create', [CarsController::class, 'createCar']);
        Route::post('/{car}/confirm', [CarsController::class, 'confirmCar']);
        Route::post('/{car}/update', [CarsController::class, 'updateCar']);
        Route::post('/{car}/photos', [CarsController::class, 'uploadPhoto']);
        Route::delete('/{car}/photos/{photo}', [CarsController::class, 'deletePhoto']);
    });
});

Route::prefix('services')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::post('/', [ServiceController::class, 'store']);
    Route::put('/{service}', [ServiceController::class, 'update']);
    Route::delete('/{service}', [ServiceController::class, 'destroy']);
});

Route::prefix('dashboard')->group(function () {
    Route::get('/stats/{clientId}', [DashboardController::class, 'getStats']);
    Route::get('/manager-stats', [DashboardController::class, 'getManagerStats']);
});

Route::prefix('reports')->middleware('auth:sanctum')->group(function () {
    Route::get('/revenue-by-month', [ReportController::class, 'revenueByMonth']);
    Route::get('/popular-services', [ReportController::class, 'popularServices']);
    Route::get('/bookings-by-status', [ReportController::class, 'bookingsByStatus']);
    Route::get('/export/revenue-by-month', [ReportController::class, 'exportRevenueExcel']);
    Route::get('/export/popular-services', [ReportController::class, 'exportPopularServicesExcel']);
    Route::get('/export/bookings-by-status', [ReportController::class, 'exportBookingsByStatusExcel']);
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PublicBookingController;
use App\Http\Controllers\Api\CarsController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

// --- Auth ---
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// --- Public booking (no auth required) ---
Route::post('/public/booking', [PublicBookingController::class, 'store']);

// --- Reference data (public, read-only dictionary tables) ---
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
    Route::get('/service-categories', [ReferenceController::class, 'serviceCategories']);
});

// --- Stripe webhook (no auth — signed by Stripe secret) ---
Route::post('/payments/webhook/stripe', [PaymentController::class, 'handleWebhook']);

// --- Authenticated routes ---
Route::middleware('auth:sanctum')->group(function () {

    // Clients
    Route::prefix('clients')->group(function () {
        // List and create — manager and admin only
        Route::middleware('role.manager_admin')->group(function () {
            Route::get('/', [ClientController::class, 'index']);
            Route::post('/', [ClientController::class, 'store']);
            Route::put('/{client}', [ClientController::class, 'update']);
            Route::delete('/{client}', [ClientController::class, 'destroy']);
        });

        // Read own data — manager/admin OR the client themselves
        Route::middleware('role.manager_admin_or_client')->group(function () {
            Route::get('/{client}', [ClientController::class, 'show']);
            Route::get('/{client}/bookings', [BookingController::class, 'getUserBookings']);
            Route::get('/{client}/cars', [CarsController::class, 'getUserCars']);
        });
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        // List / read / manage — manager and admin only
        Route::middleware('role.manager_admin')->group(function () {
            Route::get('/', [BookingController::class, 'index']);
            Route::get('/upcoming', [BookingController::class, 'getUpcomingBookings']);
            Route::get('/{booking}', [BookingController::class, 'getBooking']);
            Route::post('/{booking}/confirm', [BookingController::class, 'confirmBooking']);
            Route::post('/{booking}/mark-paid', [BookingController::class, 'markPaid']);
            Route::post('/{booking}/update', [BookingController::class, 'updateBooking']);
        });

        // Actions available to any authenticated user (except booking update which is above)
        Route::post('/{client}/create', [BookingController::class, 'createBooking']);
        Route::post('/calculate-price', [BookingController::class, 'calculatePrice']);
        Route::get('/{booking}/receipt', [BookingController::class, 'generateReceipt']);
        Route::post('/{booking}/payment/create-session', [PaymentController::class, 'createCheckoutSession']);
    });

    // Cars
    Route::prefix('cars')->group(function () {
        // List / confirm — manager and admin only
        Route::middleware('role.manager_admin')->group(function () {
            Route::get('/', [CarsController::class, 'index']);
            Route::post('/{car}/confirm', [CarsController::class, 'confirmCar']);
        });

        // Single car — manager/admin OR the car's owner client
        Route::middleware('role.manager_admin_or_car')->group(function () {
            Route::get('/{car}', [CarsController::class, 'getCar']);
        });

        // Actions available to any authenticated user
        Route::post('/{client}/create', [CarsController::class, 'createCar']);
        Route::post('/{car}/update', [CarsController::class, 'updateCar']);
        Route::post('/{car}/photos', [CarsController::class, 'uploadPhoto']);
        Route::delete('/{car}/photos/{photo}', [CarsController::class, 'deletePhoto']);
    });

    // Services — admin only
    Route::prefix('services')->middleware('role.admin')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::post('/', [ServiceController::class, 'store']);
        Route::put('/{service}', [ServiceController::class, 'update']);
        Route::delete('/{service}', [ServiceController::class, 'destroy']);
        Route::get('/categories', [ServiceController::class, 'categories']);
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats/{clientId}', [DashboardController::class, 'getStats']);
        Route::middleware('role.manager_admin')->get('/manager-stats', [DashboardController::class, 'getManagerStats']);
    });

    // Masters — manager and admin only
    Route::middleware('role.manager_admin')->group(function () {
        Route::get('/masters', [MasterController::class, 'masters']);
        Route::post('/masters/check-availability', [MasterController::class, 'checkAvailability']);
        Route::post('/bookings/{booking}/assign-master', [MasterController::class, 'assignMaster']);
    });

    // Master cabinet — master only
    Route::prefix('master')->middleware('role.master')->group(function () {
        Route::get('/bookings', [MasterController::class, 'index']);
        Route::get('/bookings/{booking}', [MasterController::class, 'show']);
        Route::post('/bookings/{booking}/status', [MasterController::class, 'updateStatus']);
    });

    // Reports — admin only
    Route::prefix('reports')->middleware('role.admin')->group(function () {
        Route::get('/revenue-by-month', [ReportController::class, 'revenueByMonth']);
        Route::get('/popular-services', [ReportController::class, 'popularServices']);
        Route::get('/bookings-by-status', [ReportController::class, 'bookingsByStatus']);
        Route::get('/export/revenue-by-month', [ReportController::class, 'exportRevenueExcel']);
        Route::get('/export/popular-services', [ReportController::class, 'exportPopularServicesExcel']);
        Route::get('/export/bookings-by-status', [ReportController::class, 'exportBookingsByStatusExcel']);
    });
});

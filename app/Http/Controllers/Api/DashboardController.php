<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Client;
use App\Models\ClientCar;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getStats(int $clientId): JsonResponse
    {
        $client = Client::find($clientId);

        if (!$client) {
            return response()->json([
                'message' => 'Клієнт не знайдений',
            ], 404);
        }

        $stats = [
            'total_bookings' => $client->bookings->count(),
            'total_cars' => $client->cars->count(),
            'total_spent' => $client->bookings()->where('status_id', BookingStatus::getPaidStatusId())->sum('total_price') ?? 0,
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }

    public function getManagerStats(): JsonResponse
    {
        $stats = [
            'total_bookings' => Booking::where('date', '>=', now())->count(),
            'total_cars' => ClientCar::count(),
            'total_clients' => Client::count(),
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
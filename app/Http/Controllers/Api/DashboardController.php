<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
            'total_spent' => 0,
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
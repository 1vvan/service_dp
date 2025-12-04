<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;

class BookingController extends Controller
{
    public function index()
    {
        return Booking::all();
    }

    public function getUserBookings($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user->bookings);
    }
}
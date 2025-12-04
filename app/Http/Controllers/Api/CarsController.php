<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientCar;
use App\Models\User;

class CarsController extends Controller
{
    public function index()
    {
        return ClientCar::with('models')->get();
    }

    public function getUserCars($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user->cars);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user || ($user->role_id !== User::ROLE_MANAGER && $user->role_id !== User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $clients = Client::query()
            ->with('user')
            ->withCount(['cars', 'bookings'])
            ->get();

        return response()->json($clients);
    }

    public function show(Request $request, Client $client): JsonResponse
    {
        $user = $request->user();
        
        if (!$user || ($user->role_id !== User::ROLE_MANAGER && $user->role_id !== User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $client->load([
            'user',
            'cars.carModel.brand',
            'bookings.services',
            'bookings.status',
            'bookings.car'
        ]);
        $client->loadCount(['cars', 'bookings']);

        return response()->json($client);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $user = $request->user();
        
        if (!$user || ($user->role_id !== User::ROLE_MANAGER && $user->role_id !== User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'sometimes|string|max:255',
        ]);

        $client->update($validated);
        $client->load([
            'user',
            'cars.carModel.brand',
            'bookings.services',
            'bookings.status',
            'bookings.car'
        ]);
        $client->loadCount(['cars', 'bookings']);

        return response()->json($client);
    }
}
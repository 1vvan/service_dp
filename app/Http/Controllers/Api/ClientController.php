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

        $query = Client::query()
            ->with('user')
            ->withCount(['cars', 'bookings']);

        $search = $request->input('search');
        if (!empty($search)) {
            $search = '%' . trim($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('clients.full_name', 'like', $search)
                    ->orWhere('clients.email', 'like', $search)
                    ->orWhere('clients.phone', 'like', $search)
                    ->orWhereHas('cars', function ($carQuery) use ($search) {
                        $carQuery->where('license_plate', 'like', $search)
                            ->orWhere('vin', 'like', $search);
                    });
            });
        }

        $clients = $query->orderBy('full_name')->get();

        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || ($user->role_id !== User::ROLE_MANAGER && $user->role_id !== User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        $client = Client::create($validated);
        $client->load('user')->loadCount(['cars', 'bookings']);

        return response()->json([
            'message' => 'Клієнта створено',
            'client' => $client,
        ], 201);
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
            'phone' => 'sometimes|nullable|string|max:255',
            'manager_notes' => 'sometimes|nullable|string|max:5000',
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

    public function destroy(Request $request, Client $client): JsonResponse
    {
        $user = $request->user();

        if (!$user || ($user->role_id !== User::ROLE_MANAGER && $user->role_id !== User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $client->delete();

        return response()->json(['message' => 'Клієнта видалено']);
    }
}
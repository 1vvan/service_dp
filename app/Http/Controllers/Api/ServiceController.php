<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role_id !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $services = Service::orderBy('name')->get();

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role_id !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
        ]);

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Послугу створено',
            'service' => $service,
        ], 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role_id !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
        ]);

        $service->update($validated);

        return response()->json([
            'message' => 'Послугу оновлено',
            'service' => $service,
        ]);
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role_id !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $service->delete();

        return response()->json(['message' => 'Послугу видалено']);
    }
}

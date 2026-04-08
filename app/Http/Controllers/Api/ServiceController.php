<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::with('category')->orderBy('name')->get();

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
        ]);

        $service = Service::create($validated);
        $service->load('category');

        return response()->json([
            'message' => 'Послугу створено',
            'service' => $service,
        ], 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
        ]);

        $service->update($validated);
        $service->load('category');

        return response()->json([
            'message' => 'Послугу оновлено',
            'service' => $service,
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(['message' => 'Послугу видалено']);
    }

    public function categories(): JsonResponse
    {
        return response()->json(ServiceCategory::orderBy('name')->get());
    }
}

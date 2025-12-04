<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingStatus;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\DriveUnitType;
use App\Models\EngineType;
use App\Models\FuelType;
use App\Models\GearboxType;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ReferenceController extends Controller
{
    public function carBrands(): JsonResponse
    {
        $brands = CarBrand::orderBy('name')->get();
        return response()->json($brands);
    }

    public function carModels(int $brandId = null): JsonResponse
    {
        $query = CarModel::with('brand');
        
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        
        $models = $query->orderBy('name')->get();
        return response()->json($models);
    }

    public function fuelTypes(): JsonResponse
    {
        $types = FuelType::orderBy('name')->get();
        return response()->json($types);
    }

    public function engineTypes(): JsonResponse
    {
        $types = EngineType::orderBy('name')->get();
        return response()->json($types);
    }

    public function gearboxTypes(): JsonResponse
    {
        $types = GearboxType::orderBy('name')->get();
        return response()->json($types);
    }

    public function driveUnitTypes(): JsonResponse
    {
        $types = DriveUnitType::orderBy('name')->get();
        return response()->json($types);
    }

    public function bookingStatuses(): JsonResponse
    {
        $statuses = BookingStatus::orderBy('name')->get();
        return response()->json($statuses);
    }

    public function services(): JsonResponse
    {
        $services = Service::orderBy('name')->get();
        return response()->json($services);
    }
}

